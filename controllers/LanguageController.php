<?php
namespace app\modules\translator\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\modules\translator\helper\LanguageScanInfo;
use app\modules\translator\models\LanguageSearch;
use app\modules\translator\models\LanguageModel;
use yii\helpers\FileHelper;
use yii\web\Controller;

class LanguageController extends Controller
{

    /**
     *
     * {@inheritdoc}
     *
     * @see \yii\web\Controller::beforeAction()
     */
    public function beforeAction($action)
    {
        // Register environment for this module.
        $this->module->requestedAction = $action;
        return parent::beforeAction($action);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'index',
                    'scan-message',
                    'export'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ],
                        'matchCallback' => function ($rule, $action) {
                            // Only configured users can access this module
                            if (empty($this->module->userAccess) || in_array(Yii::$app->user->identity->email, $this->module->userAccess)) {
                                return true;
                            }
                            return false;
                        }
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => []
            ]
        ];
        // 'delete' => ['post'],
    }

    /**
     * Index page
     *
     * @return string
     */
    public function actionIndex()
    {
        $post = Yii::$app->request->post();
        if (isset($post['hasEditable'])) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $language = $post['editableAttribute'];
            $message = $post['translated_msg'];
            list ($category, $key) = explode(';', $post['editableKey']);
            $result = LanguageModel::modifyMessage($language, $category, $key, $message, $this->module);
            return [
                'output' => ($result) ? $message : '',
                'message' => ($result) ? '' : Yii::t('posys', 'New message is required.')
            ];
        } else {
            $scan_info = LanguageScanInfo::getScanInfo($this->module, true);
            $model = new LanguageSearch($this->module);
            return $this->render('index', [
                'scan_info' => $scan_info,
                'model' => $model,
                'provider' => $model->search(Yii::$app->request->queryParams)
            ]);
        }
    }

    /**
     * Scan the message directory
     */
    public function actionScanMessage()
    {
        if (isset(Yii::$app->request->queryParams['action'])) {
            LanguageScanInfo::getScanInfo($this->module, true);
            $this->redirect("index");
        }
        return $this->render('_scanning');
    }

    /**
     * Export the translated message to zip file
     */
    public function actionExport()
    {
        $zipFile = LanguageScanInfo::getScanInfo($this->module, true)->createTranslatedZip();
        \Yii::$app->response->sendFile($zipFile)->send();
        @unlink($zipFile);
    }
}
