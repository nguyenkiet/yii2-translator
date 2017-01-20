<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\FileHelper;
use yii\bootstrap\ButtonGroup;
use yii\base\Widget;
use app\modules\translator\models\LanguageSearch;

/* @var $this yii\web\View */
/* @var $this app\modules\translator\helper\LanguageScanInfo */

$form = ActiveForm::begin([
    'id' => 'translated-message-search-form',
    'method' => 'GET',
    'layout' => 'horizontal',
    'action' => Yii::$app->getUrlManager()->createUrl("translator/language"),
    'fieldConfig' => [
        'options' => [
            'class' => ''
        ]
    ]
]);
?>

<!-- Keyword -->
<div class="form-group row">
	<div class="row">
		<div class="col-md-6">			
			<?php
echo $form->field($model, "search_key", [
    'options' => [
        'class' => 'translate-search-key'
    ]
])
    ->textInput([
    'placeholder' => Yii::t('app', 'Type a keyword to search...'),
    'class' => 'form-control translated-search-keyword'
])
    ->label(Yii::t('app', 'Language Key'));
?>
		</div>
		<div class="col-md-6">
            <?=Html::a(Yii::t('app', 'Export'), ['export'], ['class' => 'btn btn-success center-button pull-right voffset2','id' => 'translate-scan']);?>                
            <?=Html::a(Yii::t('app', 'Re-scan'), ['scan-message'], ['class' => 'btn btn-warning center-button pull-right voffset2 translate-scan-button','id' => 'translate-scan']);?>
         </div>
	</div>
</div>
<!-- Translate status -->
<div class="form-group row">
	<div class="row">
		<div class="col-md-6">
			<div>
				<label class="col-xs-2 col-sm-4 col-md-3 translated-filter-title"><?= Yii::t("app", "Translation Status")?></label>
			</div>
			<div class="btn-group" data-toggle="buttons">
                    	<?php
                    $buttons = array();
                    $status_array = [
                        [
                            'name' => Yii::t('app', 'All'),
                            'value' => LanguageSearch::TRANSLATED_STATUS_ALL
                        ],
                        [
                            'name' => Yii::t('app', 'Translated'),
                            'value' => LanguageSearch::TRANSLATED_STATUS_TRANSLATED
                        ],
                        [
                            'name' => Yii::t('app', 'Not Translated'),
                            'value' => LanguageSearch::TRANSLATED_STATUS_NOTYET
                        ]
                    ];
                    foreach ($status_array as $item) {
                        $buttons[] = [
                            'label' => Yii::t('app', $item['name']),
                            'options' => [
                                'value' => $item['value'],
                                'name' => (($model->search_status == $item['value']) ? "" : "btn_status"),
                                'class' => 'btn btn-default' . (($model->search_status == $item['value']) ? ' active' : '')
                            ]
                        ];
                    }
                    echo $form->field($model, "search_status")
                        ->hiddenInput()
                        ->label(false);
                    echo ButtonGroup::widget([
                        'buttons' => $buttons
                    ]);
                    ?>
                    </div>
		</div>
	</div>
</div>
<!-- Category -->
<div class="form-group row">
	<div class="row">
		<div class="col-md-6">
			<div>
				<label class="col-xs-2 col-sm-4 col-md-3 translated-filter-title"><?= Yii::t("app", "Category")?></label>
			</div>
			<div class="btn-group" data-toggle="buttons">
                        <?php
                        $buttons = array(
                            [
                                'label' => ucfirst(Yii::t('app', LanguageSearch::TRANSLATED_CATEGORY_ALL)),
                                'options' => [
                                    'value' => LanguageSearch::TRANSLATED_CATEGORY_ALL,
                                    'name' => (($model->search_category == LanguageSearch::TRANSLATED_CATEGORY_ALL) ? "" : "btn_categories"),
                                    'class' => 'btn btn-default' . (($model->search_category == LanguageSearch::TRANSLATED_CATEGORY_ALL) ? ' active' : '')
                                ]
                            ]
                        );
                        foreach ($scan_info->categories as $cat) {
                            $buttons[] = [
                                'label' => ucfirst(Yii::t('app', $cat)),
                                'options' => [
                                    'value' => $cat,
                                    'name' => (($model->search_category == $cat) ? "" : "btn_categories"),
                                    'class' => 'btn btn-default' . (($model->search_category == $cat) ? ' active' : '')
                                ]
                            ];
                        }
                        echo $form->field($model, "search_category")
                            ->hiddenInput()
                            ->label(false);
                        echo ButtonGroup::widget([
                            'buttons' => $buttons
                        ]);
                        ?>
                    </div>
		</div>
	</div>
</div>
<?php
ActiveForm::end();

$js = <<<JS
$("[name=btn_languages]").on("click", function()
{
   $("#languagesearch-search_language").val($(this).val());
   $("#translated-message-search-form").submit();
});
$("[name=btn_status]").on("click", function()
{
   $("#languagesearch-search_status").val($(this).val());
   $("#translated-message-search-form").submit();
});
$("[name=btn_categories]").on("click", function()
{
   $("#languagesearch-search_category").val($(this).val());
   $("#translated-message-search-form").submit();
});
$("#languagesearch-search_key").on("keypress", function(e) {
    if (e.keyCode == 13) {
        $("#translated-message-search-form").submit();
    }
});
JS;
$this->registerJs($js);
?>
