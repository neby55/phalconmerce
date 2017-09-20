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
use Phalconmerce\Models\Utils;
use Phalconmerce\Services\FrontendService;
use POMO\MO;
use POMO\PO;

abstract class AbstractTranslationService extends MainService {

	/** @var MO $mo */
	protected $mo;
	/** @var string $langCode */
	protected $langCode;
	/** @var string $langId */
	protected $langId;
	/** @var string $currencyCode */
	protected $currencyCode;
	/** @var string[] */
	protected $poIndexesList;
	/** @var array Rates for available currencies */
	protected $currenciesRatesList;

	/** @var array Available Lang Codes and its LangIds (in index) */
	public static $validLangList = array(1=>'fr', 2=>'en');
	/** @var array Available Currency Codes and default currencies for LangIds (in index) */
	public static $validCurrenciesList = array(1=>'EUR', 2=>'USD');
	/** @var array Sigle for available currencies */
	public static $currenciesSiglesList = array('EUR'=>'€', 'USD'=>'$');

	public function __construct() {
		$this->langCode = '';
		$this->langId = '';
		$this->currencyCode = '';
		$this->poIndexesList = array();
		$this->currenciesRatesList = array();
		$this->mo = new MO();
	}

	/**
	 * @param string $str
	 * @param array $data
	 * @return string
	 */
	public function e($str, $data=array()) {
		// tant qu'on est pas en prod, on ajoute dans la DB le PO
		if (defined('DEBUG') && DEBUG == 1) {
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
		// tant qu'on est pas en prod, on ajoute dans la DB le PO
		if (defined('DEBUG') && DEBUG == 1) {
			$this->addPoIndex($str);
		}
		$translated = vsprintf($this->mo->translate($str), $data);
		if (trim($translated) != '') {
			return $translated;
		}
		return vsprintf($str, $data);
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
	private function setCurrentLang($langId, $setCookie=false) {
		if (array_key_exists($langId, self::$validLangList)) {
			$this->langCode = self::$validLangList[$langId];
			$this->langId = $langId;
			if ($setCookie) {
				$this->getDI('frontend')->addCookie(FrontendService::COOKIE_LANG_NAME, $this->langCode, FrontendService::COOKIES_LIFETIME_IN_DAYS * 86400);
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
	private function setCurrentCurrency($currencyCode, $setCookie=false) {
		if (in_array($currencyCode, self::$validCurrenciesList)) {
			$this->currencyCode = $currencyCode;
			if ($setCookie) {
				$this->getDI('frontend')->addCookie(FrontendService::COOKIE_CURRENCY_NAME, $this->currencyCode, FrontendService::COOKIES_LIFETIME_IN_DAYS * 86400);
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
			$moFilename = dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$langCode.'.mo';
			/*if (defined('DEBUG') && DEBUG == 1) {
				echo 'mo file = '.$moFilename.'<br>';
			}*/
			if (file_exists($moFilename)) {
				// TODO extends from POMO ?
				return $this->mo->import_from_file($moFilename);
			}
		}
		return false;
	}

	public function handleLangAndCurrencyFormPost() {
		$modifications = false;
		// Si modification langue
		if (!empty($_POST[FrontendService::COOKIE_LANG_NAME])) {
			$langPost = strtolower(trim($_POST[FrontendService::COOKIE_LANG_NAME]));
			$this->setCurrentLang(self::getLangIdFromLangCode($langPost), true);
			$modifications = true;
		}

		// Si modification currency
		if (!empty($_POST[FrontendService::COOKIE_CURRENCY_NAME])) {
			$currencyPost = strtoupper(trim($_POST[FrontendService::COOKIE_CURRENCY_NAME]));
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
		if ($this->setTranslateLangCode($langCode)) {

			$content = 'msgid ""
msgstr ""
"Project-Id-Version: InnocentStone\n"
"POT-Creation-Date: ' . date('Y-m-d H:iO') . '\n"
"PO-Revision-Date: ' . date('Y-m-d H:iO') . '\n"
"Last-Translator: InnocentStone\n"
"Language-Team: InnocentStone\n"
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
				$content .= sprintf($contentLine, PO::poify($currentPoIndex), ($isPotFile ? '""' : PO::poify($this->l($currentPoIndex))));
			}

			Utils::saveData('po_indexes'.($isPotFile ? '.pot' : '_'.$langCode.'.po'), $content);

			return true;
		}
		return false;
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
	private function loadPoIndexes() {
		$pdo = $this->getDI()->get('db');

		$sql = '
			SELECT name
			FROM poIndexes
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
	private function saveDB() {
		$pdo = $this->getDI()->get('db');
		$sql = '
			REPLACE INTO poIndexes (id, name) VALUES (:id, :str)
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
// Callback close method from FrontOrder when script is finished
// TODO check if it is necessary
// register_shutdown_function(array("Phalconmerce\Services\TranslationService", "close"));