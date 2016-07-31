Google reCAPTCHA widget for Yii2 (without curl)
================================
Based on google reCaptcha API 2.0.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require --prefer-dist "alikdex/yii2-recaptcha" "dev-master"
```

or add

```json
"alikdex/yii2-recaptcha" : "dev-master"
```

to the `require` section of your application's `composer.json` file.

* [Sign up for an reCAPTCHA API keys](https://www.google.com/recaptcha/admin#createsite).

* Configure the component in your configuration file (web.php). The parameters siteKey and secret are optional.
But if you leave them out you need to set them in every validation rule and every view where you want to use this widget.
If a siteKey or secret is set in an individual view or validation rule that would overrule what is set in the config.

```php
'components' => [
    'reCaptcha' => [
        'name' => 'reCaptcha',
        'class' => 'alikdex\recaptcha\ReCaptcha',
        'siteKey' => 'your siteKey',
        'secret' => 'your secret key',
    ],
    ...
```

* Add `ReCaptchaValidator` in your model, for example:

```php
public $reCaptcha;

public function rules()
{
  return [
      // ...
      [['reCaptcha'], \alikdex\recaptcha\ReCaptchaValidator::className(), 'secret' => 'your secret key']
  ];
}
```

or just

```php
public function rules()
{
  return [
      // ...
      [[], \alikdex\recaptcha\ReCaptchaValidator::className(), 'secret' => 'your secret key']
  ];
}
```

or simply

```php
public function rules()
{
  return [
      // ...
      [[], \alikdex\recaptcha\ReCaptchaValidator::className()]
  ];
}
```

Usage
-----
For example:

```php
<?= \alikdex\recaptcha\ReCaptcha::widget([
    'name' => 'reCaptcha', // optional
    'widgetOptions' => [
      'class' => 'col-sm-offset-3',
      'data-theme' => 'dark',
      // ...  see google recaptcha2 manual
    ]
]) ?>
```

or

```php
<?= $form->field($model, 'reCaptcha')->widget(\alikdex\recaptcha\ReCaptcha::className()) ?>
```

or simply

```php
<?= \alikdex\recaptcha\ReCaptcha::widget() ?>
```

Resources
---------
* [Google reCAPTCHA](https://developers.google.com/recaptcha)
