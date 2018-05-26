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
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Backend\Models\Menu;
use Backend\Models\MenuControllerIndexLink;
use Backend\Models\MenuNamedRouteLink;
use Backend\Models\SubMenu;
use Phalconmerce\Models\Utils;

abstract class AbstractEmailService extends MainService {
	/** @var \PHPMailer\PHPMailer\PHPMailer */
	protected $mailer;

	/** @var  string */
	protected $sender;

	/** @var  int */
	protected $debugLevel;

	public function __construct() {
		/** @var \Phalcon\Config $config */
		$config = Di::getDefault()->get('config');
		$this->sender = $config->mailer->sender;
		$this->debugLevel = isset($config->mailer->debugLevel) ? $config->mailer->debugLevel : 0;

		$this->mailer = new PHPMailer(true);
		try {
			//Server settings
			$this->mailer->SMTPDebug = $this->debugLevel;
			$this->mailer->isSMTP();
			$this->mailer->Host = $config->mailer->host;
			$this->mailer->Port = $config->mailer->port;

			// If authentification is configured
			if (!empty($config->mailer->username) && !empty($config->mailer->password)) {
				$this->mailer->SMTPAuth = true;
				$this->mailer->Username = $config->mailer->username;
				$this->mailer->Password = $config->mailer->password;
				$this->mailer->SMTPSecure = $config->mailer->smtp_secure;
			}
			else {
				$this->mailer->SMTPAuth = false;
			}

			// If SMTP secure is configured
			if (!empty($config->mailer->smtp_secure)) {
				$this->mailer->SMTPSecure = $config->mailer->smtp_secure;
			}

			// Getting options
			if (isset($config->mailer->options)) {
				$options = $config->mailer->options->toArray();
				$this->mailer->SMTPOptions = $options;
			}

			// Charset
			if (!empty($config->mailer->charset)) {
				$this->mailer->CharSet = $config->mailer->charset;
			}

			// Debug output
			$this->mailer->Debugoutput = function($e) {
				Di::getDefault()->get('logger')->error($e);
			};
		} catch (PHPMailerException $e) {
			Di::getDefault()->get('logger')->error('EmailService error : ' . $this->mailer->ErrorInfo);
			Di::getDefault()->get('logger')->error($e->getTraceAsString());
		}
	}

	/**
	 * @param string $email
	 * @return bool
	 */
	public static function checkEmailValidity($email) {
		return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
	}

	/**
	 * @param string|array $recipient
	 * @param string $subject
	 * @param string $htmlContent
	 * @param string $textContent
	 * @param array $options
	 * @return bool
	 */
	public function send($recipient, $subject, $htmlContent, $textContent='', $options=array()) {
		try {
			// clear addresses of all types
			$this->mailer->clearAddresses();
			$this->mailer->clearCCs();
			$this->mailer->clearBCCs();
			$this->mailer->clearAllRecipients();
			$this->mailer->clearAttachments();
			$this->mailer->clearCustomHeaders();
			$this->mailer->clearReplyTos();

			// Change cases of options
			$options = array_change_key_case($options);
			
			// Sender
			$this->mailer->setFrom($this->sender);

			// Recipient
			$this->addEmail($recipient);

			// CC
			if (array_key_exists('cc', $options)) {
				$this->addEmail($options['cc'], 'CC');
			}
			// BCC
			if (array_key_exists('bcc', $options)) {
				$this->addEmail($options['bcc'], 'BCC');
			}

			//Attachments
			if (array_key_exists('attachment', $options)) {
				// If one attachment
				if (is_array($options['attachment']) && isset($options['attachment']['file'])) {
					$this->addAttachment($options['attachment']);
				}
				// If multiple attachments
				else if (is_array($options['attachment'])) {
					foreach ($options['attachment'] as $currentAttachmentOption) {
						$this->addAttachment($currentAttachmentOption);
					}
				}
				else if (is_string($options['attachment'])) {
					$this->addAttachment($options['attachment']);
				}
			}

			//Content
			$this->mailer->isHTML(true);
			$this->mailer->Subject = $subject;
			$this->mailer->Body    = $htmlContent;
			$this->mailer->AltBody = !empty($textContent) ? $textContent : strip_tags($htmlContent);

			return $this->mailer->send() !== false;
		} catch (PHPMailerException $e) {
			Di::getDefault()->get('logger')->error('EmailService error : ' . $this->mailer->ErrorInfo);
			Di::getDefault()->get('logger')->error($e->getTraceAsString());
			return false;
		} catch (\Exception $e) {
			Di::getDefault()->get('logger')->error('EmailService format error : '.$e->getMessage());
			Di::getDefault()->get('logger')->error($e->getTraceAsString());
		}
	}

	/**
	 * @param string|array $attachmentOption
	 * @return bool
	 * @throws PHPMailerException
	 * @throws \Exception
	 */
	protected function addAttachment($attachmentOption) {
		if (is_array($attachmentOption)) {
			if (!empty($attachmentOption['filename'])) {
				$file = $attachmentOption['file'];
			}
			else {
				throw new \Exception('Attachment file is missing');
			}

			if (!empty($attachmentOption['filename'])) {
				$filename = $attachmentOption['filename'];
			}
			else {
				$filename = '';
			}
		}
		else if (is_string($attachmentOption)) {
			$file = $attachmentOption;
			$filename = '';
		}

		if (!empty($file)) {
			if (!file_exists($file)) {
				throw new \Exception('Attachment file is not found on disk');
			}

			if (!empty($filename)) {
				return $this->mailer->addAttachment($file, $filename);
			}
			else {
				return $this->mailer->addAttachment($file);
			}
		}
		return false;
	}

	/**
	 * @param string $email
	 * @param string $type
	 * @throws \Exception
	 */
	protected function addEmail($email, $type='Address') {
		$method = 'add'.$type;
		if (!method_exists($this->mailer, $method)) {
			throw new \Exception('Method '.$method.' does not exists');
		}

		$errorType = $type == 'Address' ? 'Recipient' : $type;

		if (is_array($email)) {
			$email = array_values($email);
			if (self::checkEmailValidity($email[0])) {
				$this->mailer->$method($email[0], $email[1]);
			}
			else {
				throw new \Exception($errorType.' email address not valid');
			}
		}
		else if (is_string($email)) {
			if (self::checkEmailValidity($email)) {
				$this->mailer->$method($email);
			}
			else {
				throw new \Exception($errorType.' email address not valid');
			}
		}
	}

	/**
	 * @return int
	 */
	public function getDebugLevel() {
		return $this->debugLevel;
	}

	/**
	 * @param int $debugLevel
	 */
	public function setDebugLevel($debugLevel) {
		$this->debugLevel = $debugLevel;
	}
}