<?php

namespace Frontend\Controllers\Abstracts;

use Frontend\Controllers\ControllerBase;
use Phalcon\Di;
use Phalcon\Mvc\View;
use Phalconmerce\Models\Utils;
use Phalconmerce\Models\Popo\Lang;
use Phalconmerce\Models\Popo\Seo;
use Phalconmerce\Models\Popo\Url;
use Phalconmerce\Services\Abstracts\AbstractFrontendService;
use samdark\sitemap\Sitemap;

abstract class AbstractSitemapController extends AbstractControllerBase {
	// TODO improve it (priority + url from manually defined routes
	public function indexAction() {
		// Getting content from cache
		$cacheKey = 'sitemap.xml';
		$sitemapContent = Utils::loadCacheData($cacheKey);

		// If cache is empty or expired
		if (empty($sitemapContent)) {
			// Tmp filename because the library permits only to save (not to display)
			$tmpFilename = Di::getDefault()->get('config')->cacheDir . 'sitemap.xml';

			// Instance of sitemap generator
			$sitemap = new Sitemap($tmpFilename, true);

			// Gets every active language
			$langList = $this->translation->getValidLangList();

			// Get every URL
			$urlData = array();
			$dbUrlList = \Phalconmerce\Models\Utils::loadData('routes');
			if (is_array($dbUrlList)) {
				/**
				 * @var \Phalconmerce\Models\Popo\Abstracts\AbstractUrl $currentUrlObject
				 */
				foreach ($dbUrlList as $currentPermalink => $currentUrlObject) {
					if (substr($currentPermalink, 0, 1) != '/') {
						$currentPermalink = '/' . $currentPermalink;
					}
					$currentKey = $currentUrlObject->entity . '-' . $currentUrlObject->entityId;
					if (!array_key_exists($currentKey, $urlData)) {
						$urlData[$currentKey] = array();
					}
					$urlData[$currentKey][$langList[$currentUrlObject->fk_lang_id]] = $this->frontendService->getAbsoluteBaseUri() . $currentUrlObject->getFullUrl();
				}
			}

			// then get info for each one
			if (!empty($urlData)) {
				foreach ($urlData as $currentSitemapInfo) {
					$sitemap->addItem($currentSitemapInfo);
				}
			}

			// save the sitemap
			$sitemap->write();

			// Get generated sitemap content
			$sitemapContent = file_get_contents($tmpFilename);

			// And cache the value for 12 hours
			Utils::saveCacheData($sitemapContent, $cacheKey, 3600*12);
		}

		// Disable the view engine
		$this->view->setRenderLevel(
			View::LEVEL_NO_RENDER
		);

		// and display its content
		$this->response->setHeader('Content-Type', 'text/xml');
		$this->response->setContent($sitemapContent);
		$this->response->send();
		exit;
	}

	public static function getPriorities() {
		return array(
			'home' => 20,
			'category' => 10,
			'cms_page' => 1,
			'product' => 5,
		);
	}
}

