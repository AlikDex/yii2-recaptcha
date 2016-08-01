<?php
/**
 * @link https://github.com/alikdex/yii2-recaptcha
 * @copyright Copyright (c) 2016 AlikDex
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace alikdex\recaptcha;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\validators\Validator;
/**
 * ReCaptcha widget validator.
 *
 * @author AliKDex
 * @package alikdex\recaptcha
 */
class ReCaptchaValidator extends Validator
{
	const SITE_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
	const CAPTCHA_RESPONSE_FIELD = 'g-recaptcha-response';

	/**
	 *	@var boolean Whether to skip this validator if the input is empty.
	 */
	public $skipOnEmpty = false;

	/**
	 *	@var string The shared key between your site and ReCAPTCHA.
	 */
	public $secret;

	public $uncheckedMessage;

	public function init()
	{
		parent::init();

		if (null === $this->secret) {
			if (null !== Yii::$app->reCaptcha->secret) {
				$this->secret = Yii::$app->reCaptcha->secret;
			} else {
				throw new InvalidConfigException('Required `secret` param isn\'t set.');
			}
		}

		if ($this->message === null) {
			$this->message = Yii::t('yii', 'The verification code is incorrect.');
		}
	}

	/**
	 * @param \yii\base\Model $model
	 * @param string $attribute
	 * @param \yii\web\View $view
	 * @return string
	 */
	public function clientValidateAttribute($model, $attribute, $view)
	{
		$message = $this->uncheckedMessage ? $this->uncheckedMessage : Yii::t(
			'yii',
			'{attribute} cannot be blank.',
			['attribute' => $model->getAttributeLabel($attribute)]
		);

		return "(function(messages){if(!grecaptcha.getResponse()){messages.push('{$message}');}})(messages);";
	}

	/**
	 * @param string $value
	 * @return array|null
	 * @throws Exception
	 */
	protected function validateValue($value)
	{
		if (empty($value)) {
			if (!($value = Yii::$app->request->post(self::CAPTCHA_RESPONSE_FIELD))) {
				return [$this->message, []];
			}
		}

		$response = $this->_submitHttpGet([
			'secret' => $this->secret,
			'remoteip' => Yii::$app->request->userIP,
			'response' => $value,
		]);

		if ($response === false)
			throw new Exception('Unable connection to the captcha server.');
		else
			$response = Json::decode($response, true);

		if (!isset($response['success'])) {
			throw new Exception('Invalid recaptcha verify response.');
		}

		return $response['success'] ? null : [$this->message, []];
	}

	/**
	 * HTTP GET to communicate with reCAPTCHA server
	 * @param array $data Array of params
	 * @return string JSON response from reCAPTCHA server
	 */
	private function _submitHTTPGet($data)
	{
		/**
		 * PHP 5.6.0 changed the way you specify the peer name for SSL context options.
		 * Using "CN_name" will still work, but it will raise deprecated errors.
		 */
		$peer_key = version_compare(PHP_VERSION, '5.6.0', '<') ? 'CN_name' : 'peer_name';
		$options = [
			'http' => [
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query($data, '', '&'),
					// Force the peer to validate (not needed in 5.6.0+, but still works
				'verify_peer' => true,
					// Force the peer validation to use www.google.com
				$peer_key => 'www.google.com',
			],
		];

		$context = stream_context_create($options);
		return file_get_contents(self::SITE_VERIFY_URL, false, $context);
	}
}
