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


	public function run()
	{
		echo 'recaptcha';
	}
}
