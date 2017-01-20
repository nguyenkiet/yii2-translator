<?php
use app\modules\translator\assets\TranslatorAsset;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Translation settings');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Message translation')
];
TranslatorAsset::register($this);
?>
<div class="translate-scanning">
	<div class="row">
		<h3><?php echo "Scanning your message directory... Please wait."; ?></h3>
		<div class="progress progress-striped active page-progress-bar">
			<div class="progress-bar" style="width: 100%;"></div>
		</div>
	</div>
</div>
<?php
$js = <<<JS
    window.location = "?action=scan";
JS;
$this->registerJs($js);
?>