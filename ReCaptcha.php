<?php
/**
 * @link https://github.com/AlikDex/yii2-recaptcha
 * @copyright Copyright (c) 2016 AlikDex
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace alikdex\recaptcha;

use Yii;
use yii\base\InvalidConfigException;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

use yii\widgets\InputWidget;
/**
 * Yii2 Google reCAPTCHA widget.
 *
 * For example:
 *
 * ```php
 * <?= $form->field($model, 'reCaptcha')->widget(
 *  ReCaptcha::className(),
 *  ['siteKey' => 'your siteKey']
 * ) ?>
 * ```
 *
 * or
 *
 * ```php
 * <?= ReCaptcha::widget([
 *  'name' => 'reCaptcha',
 *  'siteKey' => 'your siteKey',
 *  'widgetOptions' => ['class' => 'col-sm-offset-3']
 * ]) ?>
 * ```
 *
 * @see https://developers.google.com/recaptcha
 * @author AlikDex
 * @package alikdex\recaptcha
 */
class ReCaptcha extends InputWidget
{
	const JS_API_URL = '//www.google.com/recaptcha/api.js';

	const THEME_LIGHT = 'light';
	const THEME_DARK = 'dark';

	const TYPE_IMAGE = 'image';
	const TYPE_AUDIO = 'audio';

	const SIZE_NORMAL = 'normal';
	const SIZE_COMPACT = 'compact';

	/**
	 *	@var string validation input name.
	 */
	public $name = 'ReCaptcha';

	/**
	 *	@var string Your sitekey.
	 */
	public $siteKey;

	/**
	 *	@var string Your secret.
	 */
	public $secret;

	/**
	 *	@var array Additional html widget options, such as `class`.
	 */
	public $widgetOptions = [];

	/**
	 * @throws \yii\base\InvalidConfigException
	 */
	public function init()
	{
		parent::init();

		if (null === $this->siteKey) {
			if (null !== Yii::$app->reCaptcha->siteKey) {
				$this->siteKey = Yii::$app->reCaptcha->siteKey;
			} else {
				throw new InvalidConfigException('Required `siteKey` param isn\'t set.');
			}
		}
	}

	public function run()
	{
		$this->registerClientScript();

		$this->customFieldPrepare();

		$divOptions = [
			'class' => 'g-recaptcha',
			'data-sitekey' => $this->siteKey
		];
		
		if (isset($this->widgetOptions['class'])) {
			Html::addCssClass($divOptions, $this->widgetOptions);
			unset($this->widgetOptions['class']);
		}

		$divOptions = ArrayHelper::merge($divOptions, $this->widgetOptions);

		echo Html::tag('div', '', $divOptions);
	}

	/**
	 * Registers required script for the plugin to work as jQuery File Uploader
	 */
	public function registerClientScript()
	{
		$this->getView()->registerJsFile(
			self::JS_API_URL . '?hl=' . $this->getLanguageSuffix(),
			[
				'position' => \yii\web\View::POS_HEAD,
				'async' => true,
				'defer' => true
			]
		);
	}

	/**
	 *	Check application language
	 *	@return string
	 */
	protected function getLanguageSuffix()
	{
		$currentAppLanguage = Yii::$app->language;
		$langsExceptions = ['zh-CN', 'zh-TW', 'zh-TW'];

		if (strpos($currentAppLanguage, '-') === false) {
			return $currentAppLanguage;
		}

		if (in_array($currentAppLanguage, $langsExceptions)) {
			return $currentAppLanguage;
		} else {
			return substr($currentAppLanguage, 0, strpos($currentAppLanguage, '-'));
		}
	}

	protected function customFieldPrepare()
	{
		$view = $this->view;

		if ($this->hasModel()) {
			$inputName = Html::getInputName($this->model, $this->attribute);
			$inputId = Html::getInputId($this->model, $this->attribute);
		} else {
			$inputName = $this->name;
			$inputId = 'recaptcha-' . $this->name;
		}

		if (empty($this->widgetOptions['data-callback'])) {
			$jsCode = "var recaptchaCallback = function(response){jQuery('#{$inputId}').val(response);};";
		} else {
			$jsCode = "var recaptchaCallback = function(response){jQuery('#{$inputId}').val(response); {$this->widgetOptions['data-callback']}(response);};";
		}

		$this->widgetOptions['data-callback'] = 'recaptchaCallback';

		if (empty($this->widgetOptions['data-expired-callback'])) {
			$jsExpCode = "var recaptchaExpiredCallback = function(){jQuery('#{$inputId}').val('');};";
		} else {
			$jsExpCode = "var recaptchaExpiredCallback = function(){jQuery('#{$inputId}').val(''); {$this->widgetOptions['data-expired-callback']}(response);};";
		}

		$this->widgetOptions['data-expired-callback'] = 'recaptchaExpiredCallback';

		$view->registerJs($jsCode, $view::POS_BEGIN);
		$view->registerJs($jsExpCode, $view::POS_BEGIN);

		echo Html::input('hidden', $inputName, null, ['id' => $inputId]);
	}
}
