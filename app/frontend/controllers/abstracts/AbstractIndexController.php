<?php

namespace Frontend\Controllers\Abstracts;

use Frontend\Controllers\ControllerBase;
use Phalconmerce\Models\Popo\FriendSponsoring;
use Phalconmerce\Models\Utils;
use Phalconmerce\Models\Popo\NewsletterSignup;

abstract class AbstractIndexController extends ControllerBase {

	public function indexAction() {
		$this->setSubtitle('Dashboard');
		$this->tag->setTitle('Home');
	}

	public function newsletterEmailConfirmAction() {
		$token = trim($this->dispatcher->getParam('token'));

		if (strlen($token) == 32) {
			/** @var NewsletterSignup $newsletterSignupObject */
			$newsletterSignupObject = NewsletterSignup::findFirstByToken($token);
			if (!empty($newsletterSignupObject)) {
				if ($newsletterSignupObject->isOptin) {
					$this->flashSession->warning($this->translation->e('Your email address is already confirmed.'));
				}
				else {
					$newsletterSignupObject->isOptin = true;
					$newsletterSignupObject->token = '';
					$newsletterSignupObject->save();

					$this->flashSession->success($this->translation->e('Your email has been validated.'));
				}
			}
			else {
				$this->flashSession->error($this->translation->e('Token is not recognized by our system.'));
			}
		}
		else {
			$this->flashSession->error($this->translation->e('Token is not well formated.'));
		}

		$this->view->pick('index/newsletter_email_confirm');
	}

	public function friendSponsoringConfirmAction() {
		$token = trim($this->dispatcher->getParam('token'));

		if (strlen($token) == 32) {
			/** @var AbstractFriendSponsoring $friendSponsoringObject */
			$friendSponsoringObject = FriendSponsoring::findFirstByToken($token);
			if (!empty($friendSponsoringObject)) {
				$this->view->setVar('friendSponsoringObject', $friendSponsoringObject);
				$this->view->pick('index/friend_sponsoring_confirm');
			}
			else {
				$this->flashSession->error($this->translation->e('Token is not recognized by our system.'));
				$this->view->pick('index/friend_sponsoring_fail');
			}
		}
		else {
			$this->flashSession->error($this->translation->e('Token is not well formated.'));
			$this->view->pick('index/friend_sponsoring_fail');
		}
	}
}

