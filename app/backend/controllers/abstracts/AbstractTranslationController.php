<?php

namespace Backend\Controllers\Abstracts;

use Backend\Controllers\ControllerBase;
use Phalconmerce\Models\Utils;
use Phalconmerce\Models\Popo\Lang;
use Phalconmerce\Services\POMO\MO;
use Phalconmerce\Services\TranslationService;

abstract class AbstractTranslationController extends ControllerBase {

	public function indexAction() {
		$this->setSubtitle('Translations');
		$this->tag->setTitle('Home');
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

	public function generateMoAction() {
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
}

