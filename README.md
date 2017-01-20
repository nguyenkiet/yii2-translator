# **Install** #
- Add to your `composer.json`
```php
"require": {
    "yii2-translator/translator" : "dev-master"
},
```
```php
"repositories": [
    {
        "type": "vcs",
        "url": "git@github.com:nguyenkiet/yii2-translator.git"
    }
]
```

# **Config module** #
- Add to `config/web.php` (for basic app) or `common/config/web.php` (for advanced app)
```php
'components' => [
	'i18n' => [
	    'translations' => [
	        'yii' => [
	            'class' => 'app\modules\translator\TargetMessageSource',
	            'basePath' => '@app/frontend/messages',				
	            'translatedPath' => "@runtime/messages",
	            'sourceLanguage' => 'en-US',
	            'forceTranslation' => true,
	        ],
	        'app' => [
	            'class' => 'app\modules\translator\TargetMessageSource',
	            'basePath' => '@app/frontend/messages',
	            'translatedPath' => "@runtime/messages",
	            'sourceLanguage' => 'en-US',
	            'forceTranslation' => true,
	        ],
	        'content' => [
	            'class' => 'app\modules\translator\TargetMessageSource',
	            'basePath' => '@app/frontend/messages',
	            'translatedPath' => "@runtime/messages",
	            'forceTranslation' => true,
	        ],
	        '*' => [
	            'class' => 'app\modules\translator\TargetMessageSource',
	            'basePath' => '@app/frontend/messages',
	            'translatedPath' => "@runtime/messages",
	            'sourceLanguage' => 'en-US',
	            'forceTranslation' => true,
	        ],
	    ]
	]
];
```

```php
.....
'modules' => [
	'translator' => [
	    'class' => 'app\modules\translator\Module', 
	    'originLanguagePath' => "@frontend/messages", // The origin languages messages
        'targetLanguagePath' => "@runtime/messages",  // The target of translated messages
        'userAccess' => ['admin@gmail.com', 'manager@gmail.com'] 
	],
];
```

# **Usage** #
- Link to access this module http://yourdomain/translator/language
