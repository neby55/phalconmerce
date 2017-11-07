<?php
/**
 * Phalconmerce
 * an e-commerce framework based on Phalcon PHP framework
 * 
 * Authors :
 *    Benjamin CORDIER <ben@progweb.fr>
 */

namespace Phalconmerce\Services\Abstracts;

use Phalcon\Di;
use Phalconmerce\Models\Popo\Currency;
use Phalconmerce\Models\Popo\Lang;
use Phalconmerce\Models\Utils;
use Phalconmerce\Services\FrontendService;
// TODO include POMO with composer
use Phalconmerce\Services\POMO\MO;
use Phalconmerce\Services\POMO\PO;

abstract class AbstractTranslationService extends MainService {

	/** @var MO $mo */
	protected $mo;
	/** @var string $langCode */
	protected $langCode;
	/** @var string $langId */
	protected $langId;
	/** @var Lang $lang */
	protected $lang;
	/** @var string $currencyCode */
	protected $currencyCode;
	/** @var Currency $currency */
	protected $currency;
	/** @var string[] */
	protected $poIndexesList;
	/** @var bool */
	protected $poIndexesActive;
	/** @var array Rates for available currencies */
	protected $currenciesRatesList;

	/** @var array Available Lang Codes and its LangIds (in index) */
	public static $validLangList = array(1=>'fr', 2=>'en');
	/** @var array Available Currency Codes and default currencies for LangIds (in index) */
	public static $validCurrenciesList = array(1=>'EUR', 2=>'USD');
	/** @var array Sigle for available currencies */
	public static $currenciesSiglesList = array('EUR'=>'€', 'USD'=>'$');
	/** @var Lang[] */
	public static $langsList;
	/** @var Currency[] */
	public static $currenciesList;

	public function __construct() {
		$this->langCode = '';
		$this->langId = '';
		$this->currencyCode = '';
		$this->poIndexesList = array();
		$this->poIndexesActive = Di::getDefault()->get('config')->loadTranslationIndexex;
		$this->currenciesRatesList = array();
		$this->mo = new MO();

		// load available currencies
		$results = Currency::find('status=1');
		self::$validLangList = array();
		/** @var Currency $currentResult */
		foreach ($results as $currentResult) {
			self::$currenciesList[$currentResult->isoCode] = $currentResult;
			self::$validCurrenciesList[$currentResult->id] = $currentResult->isoCode;
			self::$currenciesSiglesList[$currentResult->isoCode] = $currentResult->sigle;
		}
		// load available langs
		$results = Lang::find('status=1');
		self::$validLangList = array();
		/** @var Lang $currentResult */
		foreach ($results as $currentResult) {
			self::$langsList[$currentResult->id] = $currentResult;
			self::$validLangList[$currentResult->id] = $currentResult->code;
		}

		// If frontend use
		if (Di::getDefault()->has('frontendService')) {
			// Load lang from cookie
			if (isset($_COOKIE[Di::getDefault()->get('frontendService')->getCookieLangName()])) {
				$this->langCode = $_COOKIE[Di::getDefault()->get('frontendService')->getCookieLangName()];
			}
			else {
				$this->langCode = Di::getDefault()->get('config')->shop->default_lang;
			}
			$this->setTranslateLangCode($this->langCode);
			$this->langId = self::getLangIdFromLangCode($this->langCode);

			// Load currency from cookie
			if (isset($_COOKIE[Di::getDefault()->get('frontendService')->getCookieCurrencyName()])) {
				$this->currencyCode = $_COOKIE[Di::getDefault()->get('frontendService')->getCookieCurrencyName()];
			}
			else {
				$this->currencyCode = Di::getDefault()->get('config')->shop->default_currency;
			}

			// Getting current Lang object
			if (array_key_exists($this->langId, self::$langsList)) {
				$this->lang = self::$langsList[$this->langId];
			}
			// Getting current Currency object
			if (array_key_exists($this->currencyCode, self::$currenciesList)) {
				$this->currency = self::$currenciesList[$this->currencyCode];
			}

			// If PoIndexing is active
			if ($this->poIndexesActive) {
				// Callback close method
				register_shutdown_function(array('\Phalconmerce\Services\TranslationService', "close"));
			}
		}
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function e($str, $data=array()) {
		// If Po indexing is active
		if ($this->poIndexesActive) {
			$this->addPoIndex($str);
		}
		$translated = vsprintf(htmlentities($this->mo->translate($str)), $data);
		if (trim($translated) != '') {
			return $translated;
		}
		return vsprintf($str, $data);
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function t($str, $data=array()) {
		// If Po indexing is active
		if ($this->poIndexesActive) {
			$this->addPoIndex($str);
		}
		$translated = vsprintf($this->mo->translate($str), $data);
		if (trim($translated) != '') {
			return $translated;
		}
		return vsprintf($str, $data);
	}

	/**
	 * @return string
	 */
	public static function getCurrentLang() {
		$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : '';
		if (self::isValidLangCode($lang)) {
			return $lang;
		}
		return Di::getDefault()->get('config')->shop->default_lang;
	}

	/**
	 * @return string
	 */
	public static function getCurrentCurrency() {
		$currency = isset($_SESSION['currency']) ? $_SESSION['currency'] : '';
		if (self::isValidCurrency($currency)) {
			return $currency;
		}
		return Di::getDefault()->get('config')->shop->default_currency;
	}

	/**
	 * @param string $langCode
	 * @return int|bool
	 */
	public static function getLangIdFromLangCode($langCode) {
		if (in_array($langCode, self::$validLangList)) {
			$reverseLangs = array_flip(self::$validLangList);
			return $reverseLangs[$langCode];
		}
		return false;
	}

	/**
	 * @param int $langId
	 * @param bool $setCookie
	 * @return bool
	 */
	protected function setCurrentLang($langId, $setCookie=false) {
		Utils::debug($langId);
		if (array_key_exists($langId, self::$validLangList)) {
			$this->langCode = self::$validLangList[$langId];
			$this->langId = $langId;
			if ($setCookie) {
				Di::getDefault()->get('frontendService')->addCookie(Di::getDefault()->get('frontendService')->getCookieLangName(), $this->langCode, Di::getDefault()->get('frontendService')->getCookiesLifetimeInDays() * 86400);
			}
			return true;
		}
		return false;
	}

	/**
	 * @param string $currencyCode
	 * @param bool $setCookie
	 * @return bool
	 */
	protected function setCurrentCurrency($currencyCode, $setCookie=false) {
		if (in_array($currencyCode, self::$validCurrenciesList)) {
			$this->currencyCode = $currencyCode;
			if ($setCookie) {
				Di::getDefault()->get('frontendService')->addCookie(Di::getDefault()->get('frontendService')->getCookieCurrencyName(), $this->currencyCode, Di::getDefault()->get('frontendService')->getCookiesLifetimeInDays() * 86400);
			}
			return true;
		}
		return false;
	}

	/**
	 * @param string $langCode
	 * @return bool
	 */
	public function setTranslateLangCode($langCode='') {
		if (empty($langCode)) {
			$langCode = $this->langCode;
		}
		if ($this->isValidLangCode($langCode)) {
			$moFilename = $this->getMoFilename($langCode);
			/*
			// If Po indexing is active
			if ($this->poIndexesActive) {
				echo 'mo file = '.$moFilename.'<br>';
			}*/
			if (file_exists($moFilename)) {
				return $this->mo->import_from_file($moFilename);
			}
		}
		return false;
	}

	public function handleLangAndCurrencyFormPost() {
		$modifications = false;
		// Si modification langue
		if (!empty($_POST[Di::getDefault()->get('frontendService')->getCookieLangName()])) {
			$langPost = strtolower(trim($_POST[Di::getDefault()->get('frontendService')->getCookieLangName()]));
			$this->setCurrentLang(self::getLangIdFromLangCode($langPost), true);
			$modifications = true;
		}

		// Si modification currency
		if (!empty($_POST[Di::getDefault()->get('frontendService')->getCookieCurrencyName()])) {
			$currencyPost = strtoupper(trim($_POST[Di::getDefault()->get('frontendService')->getCookieCurrencyName()]));
			$this->setCurrentCurrency($currencyPost, true);
			$modifications = true;
		}

		return $modifications;
	}

	/**
	 * @param string $currency
	 * @return bool
	 */
	public static function isValidCurrency($currency) {
		return in_array($currency, self::$validCurrenciesList);
	}

	/**
	 * @param string $langCode
	 * @return bool
	 */
	public static function isValidLangCode($langCode) {
		return in_array($langCode, self::$validLangList);
	}

	/**
	 * @return bool
	 */
	public function generatePotFile() {
		// Force 'fr' cause its not important
		return $this->generatePoFile('fr', true);
	}

	/**
	 * @param string $langCode
	 * @param bool $isPotFile
	 * @return bool
	 */
	public function generatePoFile($langCode, $isPotFile=false) {
		// Load PO indexes from DB
		if (sizeof($this->poIndexesList) < 1) {
			$this->loadPoIndexes();
		}

		// Set correct Language MO file
		$this->setTranslateLangCode($langCode);

		$content = 'msgid ""
msgstr ""
"Project-Id-Version: '.Di::getDefault()->get('config')->frontTitle.'\n"
"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\n"
"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\n"
"Last-Translator: '.Di::getDefault()->get('config')->frontTitle.'\n"
"Language-Team: '.Di::getDefault()->get('config')->frontTitle.'\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset=UTF-8\n"
"Content-Transfer-Encoding: 8bit\n"
"X-Generator: TranslationStatic-PHP\n"
"X-Poedit-Basepath: .\n"
"Language: '.$langCode.'\n"
';
		$contentLine = '
msgid %s
msgstr %s
';

		foreach ($this->poIndexesList as $currentPoIndex) {
			$content .= sprintf($contentLine, PO::poify($currentPoIndex), ($isPotFile ? '""' : PO::poify($this->t($currentPoIndex))));
		}

		file_put_contents(($isPotFile ? $this->getPotFilename() : $this->getPoFilename($langCode)), $content);

		return true;
	}

	/**
	 * @param string $langCode
	 * @return bool
	 */
	public function updatePoFile($langCode) {
		$filename = $this->getPoFilename($langCode);

		// If file already exists, then call generatePO
		if (!file_exists($filename)) {
			return $this->generatePoFile($langCode);
		}

		// Load PO indexes from DB
		if (sizeof($this->poIndexesList) < 1) {
			$this->loadPoIndexes();
		}

		// retrieve all translation in MO file
		$moObject = new PO();
		$moObject->import_from_file($this->getPoFilename($langCode));
		$entryToAdd = $this->poIndexesList;
		// Delete every PoIndex already in MO file
		foreach ($entryToAdd as $index=>$currentPoIndex) {
			if (array_key_exists($currentPoIndex, $moObject->entries)) {
				unset($entryToAdd[$index]);
			}
		}

		// If there is PoIndex to add
		if (!empty($entryToAdd)) {
			// Set correct Language MO file
			if ($this->setTranslateLangCode($langCode)) {
				$fp = fopen($this->getPoFilename(), 'a');
				if ($fp) {
					$contentLine = '
	msgid %s
	msgstr %s
	';
					foreach ($entryToAdd as $currentPoIndex) {
						fwrite($fp, sprintf($contentLine, PO::poify($currentPoIndex), PO::poify($this->l($currentPoIndex))));
					}
					fclose($fp);

					return true;
				}
			}
		}
		return false;
	}

	/**
	 * @param string $forceLangCode
	 * @return string
	 */
	public function getPoFilename($forceLangCode='') {
		if (!empty($forceLangCode)) {
			return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$forceLangCode.'.po';
		}
		else {
			return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$this->langCode.'.po';
		}
	}

	/**
	 * @param string $forceLangCode
	 * @return string
	 */
	public function getMoFilename($forceLangCode='') {
		if (!empty($forceLangCode)) {
			return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$forceLangCode.'.mo';
		}
		else {
			return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$this->langCode.'.mo';
		}
	}

	/**
	 * @return string
	 */
	public function getPotFilename() {
		return APP_PATH.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.'po_indexes.pot';
	}

	/**
	 * @param string $index
	 */
	public function addPoIndex($index) {
		$this->poIndexesList[$index] = $index;
	}

	/**
	 * @return bool
	 */
	protected function loadPoIndexes() {
		$pdo = Di::getDefault()->get('db');

		$sql = '
			SELECT name
			FROM poindexes
		';
		$stmt = $pdo->query($sql, \PDO::FETCH_ASSOC);
		$results = $stmt->fetchAll();
		$this->poIndexesList = array();
		foreach ($results as $curRow) {
			$this->poIndexesList[] = $curRow['name'];
		}
		return (sizeof($this->poIndexesList) > 0);
	}

	/**
	 * @return bool
	 */
	protected function saveDB() {
		$pdo = $this->getDI()->get('db');
		$sql = '
			REPLACE INTO poindexes (id, name) VALUES (:id, :str)
		';
		// TODO make direct SQL request or add a Model
		$stmt = $pdo->prepare($sql);
		foreach ($this->poIndexesList as $currentPoIndex) {
			$stmt->bindValue(':id', md5($currentPoIndex));
			$stmt->bindValue(':str', $currentPoIndex);
			$stmt->execute();
		}
		return true;
	}

	/**
	 * Put data in session when page is closed
	 */
	public static function close() {
		Di::getDefault()->get('translation')->saveDB();
	}

	/**
	 * @return string
	 */
	public function getLangCode() {
		return $this->langCode;
	}

	/**
	 * @param string $langCode
	 */
	public function setLangCode($langCode) {
		$this->langCode = $langCode;
	}

	/**
	 * @return string
	 */
	public function getLangId() {
		return $this->langId;
	}

	/**
	 * @param string $langId
	 */
	public function setLangId($langId) {
		$this->langId = $langId;
	}

	/**
	 * @return string
	 */
	public function getCurrencyCode() {
		return $this->currencyCode;
	}

	/**
	 * @return string
	 */
	public function getCurrencySigle() {
		if ($this->isValidCurrency($this->getCurrencyCode())) {
			return self::$currenciesSiglesList[$this->getCurrencyCode()];
		}
		return '';
	}

	/**
	 * @param string $currencyCode
	 */
	public function setCurrencyCode($currencyCode) {
		$this->currencyCode = $currencyCode;
	}

	/**
	 * @return \string[]
	 */
	public function getPoIndexesList() {
		return $this->poIndexesList;
	}

	/**
	 * @return array
	 */
	public function getCurrenciesRatesList() {
		return $this->currenciesRatesList;
	}

	/**
	 * @return Lang
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 * @return Currency
	 */
	public function getCurrency() {
		return $this->currency;
	}

	/**
	 * @return array
	 */
	public static function getValidLangList() {
		return self::$validLangList;
	}

	/**
	 * @return array
	 */
	public static function getValidCurrenciesList() {
		return self::$validCurrenciesList;
	}

	/**
	 * @return array
	 */
	public static function getCurrenciesSiglesList() {
		return self::$currenciesSiglesList;
	}
}