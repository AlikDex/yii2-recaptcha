Google reCAPTCHA widget for Yii2 (without curl)
================================
Based on google reCaptcha API 2.0.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require --prefer-dist "alikdex/yii2-recaptcha" "1.0.0"
```

or add

```json
"alikdex/yii2-recaptcha" : "2.0.0"
```

to the `require` section of your application's `composer.json` file.

* [Sign up for an reCAPTCHA API keys](https://www.google.com/recaptcha/admin#createsite).

* Configure the component in your configuration file (web.php). The parameters siteKey and secret are optional.
But if you leave them out you need to set them in every validation rule and every view where you want to use this widget.
If a siteKey or secret is set in an individual view or validation rule that would overrule what is set in the config.

```php
'components' => [
    'container' => [
        'definitions' => [
            Adx\ReCaptcha\ReCaptcha::class => [
                'siteKey' => 'Your site key',
            ],
            Adx\ReCaptcha\ReCaptchaValidator::class => [
                'secret' => 'Your secret key',
            ],
        ],
    ],
    ...
],
```

* Add `ReCaptchaValidator` in your model, for example:

```php
public $reCaptcha;

public function rules()
{
  return [
      // ...
      [[captcha'], \Adx\ReCaptcha\ReCaptchaValidator::class],
  ];
}
```

or just

```php
public function rules()
{
  return [
      // ...
      [[], \Adx\ReCaptcha\ReCaptchaValidator::class],
  ];
}
```
Usage
-----
For example:

```php
<?= \Adx\ReCaptcha\ReCaptcha::widget([
    'name' => 'captcha', // optional
    'widgetOptions' => [
      'class' => 'col-sm-offset-3',
      'data-theme' => 'dark',
      // ...  see google recaptcha2 manual
    ]
]) ?>
```

or

```php
<?= $form->field($model, 'captcha')->widget(\Adx\ReCaptcha\ReCaptcha::class) ?>
```

or simply

```php
<?= \Adx\ReCaptcha\ReCaptcha::widget() ?>
```

Resources
---------
* [Google reCAPTCHA](https://developers.google.com/recaptcha)
* [Github ReCaptcha](https://github.com/google/recaptcha)
