<?php
namespace app\modules\translator\assets;

use yii\web\AssetBundle;

class TranslatorAsset extends AssetBundle
{

    public $sourcePath = '@vendor/targetmedia/translator/assets/';

    public $css = [
        'css/translate.css'
    ];

    public $js = [];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset'
    ];

    public $publishOptions = [
        'forceCopy' => true
    ];
}
