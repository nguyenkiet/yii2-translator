<?php
namespace app\modules\translator;

class Module extends \yii\base\Module
{

    public $requestedAction = null;

    public $controllerNamespace = 'app\modules\translator\controllers';

    public $originLanguagePath = "@frontend/messages";

    public $targetLanguagePath = "@runtime/messages";

    public $layout = null;

    public $userAccess = [];

    public function init()
    {
        parent::init();
        
        // custom initialization code goes here
        \Yii::configure($this, require (__DIR__ . '/config.php'));
    }
}
