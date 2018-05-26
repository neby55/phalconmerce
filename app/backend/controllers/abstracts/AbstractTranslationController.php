<?php

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Phalcon\Di;
use Phalcon\Mvc\View;
use Phalconmerce\Models\Utils;
use Phalconmerce\Models\Popo\Lang;
use POMO\MO;
use Phalconmerce\Services\TranslationService;
use ZipArchive;

abstract class AbstractTranslationController extends ControllerBase {

	public function indexAction() {
		$this->setSubtitle('Translations');
		$this->tag->setTitle('Home');

		/** @var \Phalcon\Mvc\Model\Resultset $results */
		$results = Lang::find('status=1');
		$langList = array();
		if ($results && $results->count() > 0) {
			/** @var Lang $currentLang */
			foreach ($results as $currentLang) {
				$langList[] = $currentLang;
			}
		}
		$this->view->setVar('langList', $langList);

		$pomoFiles = array();
		$translationService = new TranslationService();
		$localeDirectory = dirname($translationService->getPoFilename());
		// Ouvre un dossier bien connu, et liste tous les fichiers
		if (is_dir($localeDirectory)) {
			if ($dh = opendir($localeDirectory)) {
				while (($file = readdir($dh)) !== false) {
					if (substr($file, 0, 1) != '.') {
						$pomoFiles[] = array(
							'name' => $file,
							'size' => filesize($localeDirectory.DIRECTORY_SEPARATOR.$file),
							'modified' => date('d/m/Y H:i:s', filemtime($localeDirectory.DIRECTORY_SEPARATOR.$file))
						);;
					}
				}
				closedir($dh);
			}
		}
		$this->view->setVar('pomoFiles', $pomoFiles);
	}

	public function generatePoAction() {
		if (!$this->request->isPost()) {
			$this->redirectToRoute('backend-controller-index', array('controller'=>$this->dispatcher->getControllerName()));
		}

		/** @var \Phalcon\Mvc\Model\Resultset $results */
		$results = Lang::find('status=1');
		if ($results && $results->count() > 0) {
			$translationService = new TranslationService();
			/** @var Lang $currentLang */
			foreach ($results as $currentLang) {
				if ($translationService->updatePoFile($currentLang->code)) {
					$this->flashSession->success($currentLang->code.' PO file generated');
				}
			}
		}
		$this->redirectToRoute('backend-controller-index', array('controller'=>$this->dispatcher->getControllerName()));
	}

	public function downloadPoFilesAction() {
		if (!$this->request->isPost()) {
			$this->redirectToRoute('backend-controller-index', array('controller'=>$this->dispatcher->getControllerName()));
		}

		// Using HTTP Response object
		/** @var \Phalcon\Http\Response $response */
		$response = $this->response;

		// Disable the view
		$this->view->disable();

		// Creating a local zip
		$zip = new ZipArchive();
		$filename = Di::getDefault()->get('config')->cacheDir.md5(uniqid().time()).'.zip';

		if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
			exit("Impossible d'ouvrir le fichier <$filename>\n");
		}

		/** @var \Phalcon\Mvc\Model\Resultset $results */
		$results = Lang::find('status=1');
		if ($results && $results->count() > 0) {
			/** @var Lang $currentLang */
			foreach ($results as $currentLang) {
				if (file_exists($currentLang->getPoFilename())) {
					$zip->addFile($currentLang->getPoFilename(), $currentLang->code . '.po');
				}
				/*if (file_exists($currentLang->getMoFilename())) {
					$zip->addFile($currentLang->getMoFilename(), $currentLang->code . '.mo');
				}*/
			}
		}
		$zip->close();

		// Sending header representing a ZIP archive
		$response->setHeader('Content-Type', 'application/zip');
		$response->setHeader('Content-Disposition', 'attachment; filename=po-files.zip');
		$response->setHeader('Content-length', filesize($filename));
		$response->setHeader('Pragma', 'no-cache');
		$response->setHeader('Expires', '0');
		$response->setContent(file_get_contents($filename));
		$response->send();

		// At the end, delete unecessary zip file
		//@unlink($filename);
		exit;
	}

	public function uploadPomoAction() {
		if (!$this->request->isPost()) {
			$this->redirectToRoute('backend-controller-index', array('controller'=>$this->dispatcher->getControllerName()));
		}

		if (!empty($_FILES)) {
			$langId = isset($_POST['langId']) ? trim($_POST['langId']) : '';
			$poFile = isset($_FILES['po']) ? $_FILES['po'] : array();

			$langObject = Lang::findFirstById($langId);
			if (!empty($langObject)) {
				$extension = strtolower(substr($poFile['name'], strrpos($poFile['name'], '.')+1));
				var_dump($extension);
				if (empty($poFile['error']) && $poFile['size'] > 0 && $extension == 'po') {
					move_uploaded_file($poFile['tmp_name'], $langObject->getPoFilename());
				}
			}
		}

		$this->redirectToRoute('backend-controller-index', array('controller'=>$this->dispatcher->getControllerName()));
	}
}

