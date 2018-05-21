<?php
/**
 * @link https://github.com/alikdex/yii2-recaptcha
 * @copyright Copyright (c) 2016 AlikDex
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Adx\ReCaptcha;

use Yii;
use yii\di\Instance;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\web\Request;
use yii\helpers\Json;
use yii\validators\Validator;
use ReCaptcha\ReCaptcha;

/**
 * ReCaptcha widget validator.
 *
 * @author AliKDex
 *
 * @package alikdex\recaptcha
 */
class ReCaptchaValidator extends Validator
{
    const CAPTCHA_RESPONSE_FIELD = 'recaptcha';

    /**
     * @var boolean Whether to skip this validator if the input is empty.
     */
    public $skipOnEmpty = false;

    /**
     * @var string The shared key between your site and ReCAPTCHA.
     */
    public $secret;

    public $uncheckedMessage;

    public $request;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->request = Instance::ensure(Request::class);
    }

    public function init()
    {
        parent::init();

        if (null === $this->secret) {
            throw new InvalidConfigException('Required `secret` param isn\'t set.');
        }

        if ($this->message === null) {
            $this->message = Yii::t('yii', 'The verification code is incorrect.');
        }
    }

    /**
     * @param \yii\base\Model $model
     *
     * @param string $attribute
     *
     * @param \yii\web\View $view
     *
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
     *
     * @return array|null
     *
     * @throws Exception
     */
    protected function validateValue($value)
    {
        if (empty($value)) {
            return [$this->message, []];
        }

        $recaptcha = new ReCaptcha($this->secret);

        $response = $recaptcha->verify($value, $this->request->getUserIP());

        return $response->isSuccess() ? null : [$this->message, []];
    }
}
