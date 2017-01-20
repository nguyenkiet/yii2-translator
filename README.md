# **Install** #
- Add to your `composer.json`
```php
"require": {
    "targetmedia/translator" : "dev-master"
},
```
```php
"repositories": [
    {
        "type": "vcs",
        "url": "git@bitbucket.org:tyson_vietnamcubator/yii2-translator.git"
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
	            'forceTranslation' => true, // This source uses "code" keys, not actual text keys
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
        'userAccess' => ['eveline@vdboom.nl','eveline@vdboom.nl'] 
	],
];
```

# **Usage** #
- Link to access this module http://yourdomain/translator/language