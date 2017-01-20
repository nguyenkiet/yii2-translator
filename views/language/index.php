<?php
use yii\base\Widget;
use kartik\grid\GridView;
use app\modules\translator\assets\TranslatorAsset;

/* @var $this yii\web\View */
/* @var $this app\modules\translator\helper\LanguageScanInfo */
$this->title = Yii::t('app', 'Translation settings');
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Message translation')
];
TranslatorAsset::register($this);
?>
<div class="translated-module-content">
	<section class="">
		<fieldset class="col-xs-12 with-icons">      
       	<?= $this->render('_search', ['model' => $model, 'scan_info' => $scan_info]); ?>
        </fieldset>
		<!-- Grid -->
        <?php
        $column_percent = (int) 96 / (1 + count($scan_info->languages));
        $cols_arr = [
            [
                'class' => 'kartik\grid\SerialColumn',
                'contentOptions' => [
                    'style' => 'width: 10px;'
                ]
            ],
            [
                'class' => '\kartik\grid\DataColumn',
                'label' => Yii::t("app", "Key"),
                'attribute' => 'key',
                'contentOptions' => [
                    'style' => 'width: ' . $column_percent . '%;'
                ],
                'value' => function ($model, $key) {
                    return $model['key'];
                }
            ]
        ];
        foreach ($scan_info->languages as $language) {
            $cols_arr[] = [
                'class' => '\kartik\grid\EditableColumn',
                'label' => Yii::t("app", $language),
                'attribute' => $language,
                'contentOptions' => [
                    'style' => 'width: ' . $column_percent . '%;'
                ],
                'value' => function ($model, $key) use ($language) {
                    foreach ($model['data'] as $item) {
                        if ($item->language == $language) {
                            return $item->translated_value;
                        }
                    }
                },
                'editableOptions' => function ($model, $key) use ($language) {
                    $msg_val = "";
                    foreach ($model['data'] as $item) {
                        if ($item->language == $language) {
                            $msg_val = $item->translated_value;
                        }
                    }
                    return [
                        'header' => Yii::t('app', "Message"),
                        'inputType' => \kartik\editable\Editable::INPUT_TEXTAREA,
                        'options' => [
                            'class' => 'form-control',
                            'rows' => 5,
                            'placeholder' => 'Enter new message...'
                        ],
                        'submitOnEnter' => false,
                        'size' => 'lg',
                        'asPopover' => true,
                        'name' => 'translated_msg',
                        'value' => $msg_val
                    ];
                }
            ];
        }
        echo GridView::widget([
            'dataProvider' => $provider,
            'export' => false,
            'columns' => $cols_arr
        ]);
        ?>
 </section>
</div>


