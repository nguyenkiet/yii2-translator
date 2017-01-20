<?php
namespace app\modules\translator\models;

use Yii;
use app\modules\translator\helper\LanguageScanInfo;
use yii\base\Model;
use yii\data\ArrayDataProvider;

class LanguageSearch extends LanguageModel
{

    const TRANSLATED_STATUS_ALL = "all";

    const TRANSLATED_STATUS_TRANSLATED = "translated";

    const TRANSLATED_STATUS_NOTYET = "not_translated";

    const TRANSLATED_CATEGORY_ALL = "all";

    /**
     * Language search
     *
     * @var unknown
     */
    public $search_language;

    /**
     * Search by status
     *
     * @var unknown
     */
    public $search_status;

    /**
     * Search category (language files)
     *
     * @var unknown
     */
    public $search_category;

    /**
     * search key
     *
     * @var unknown
     */
    public $search_key;

    /**
     * The module use to search the directory info
     *
     * @var unknown
     */
    private $module;

    /**
     *
     * {@inheritdoc}
     *
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [
                [
                    'search_status',
                    'search_category'
                ],
                'required'
            ],
            [
                [
                    'search_language',
                    'search_status',
                    'search_category',
                    'search_key'
                ],
                'safe'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Contructor to passs the module param to search the message directory
     *
     * @param unknown $_module            
     */
    public function __construct($_module)
    {
        $this->module = $_module;
    }

    /**
     * The default condition for search languages
     */
    public function loadDefaultConditions()
    {
        $scanInfo = LanguageScanInfo::getScanInfo($this->module);
        $this->search_status = self::TRANSLATED_STATUS_ALL;
        $this->search_category = self::TRANSLATED_CATEGORY_ALL;
    }

    /**
     * Search methods
     *
     * @param unknown $params            
     */
    public function search($params)
    {
        $this->load($params);
        if (! $this->validate()) {
            $this->loadDefaultConditions();
            $this->validate();
        }
        $msg_data = array();
        $scanInfo = LanguageScanInfo::getScanInfo($this->module);
        foreach ($scanInfo->languages as $lang) {
            $this->search_language = $lang;
            if (empty($msg_data)) {
                $msg_data = $this->loadMessage();
            } else {
                $other_messages = $this->loadMessage();
                foreach ($msg_data as $key => $value) {
                    if (isset($other_messages[$key]) && isset($other_messages[$key]['data'][0]) && $msg_data[$key]['category'] == $other_messages[$key]['category']) {
                        $msg_data[$key]['data'][] = $other_messages[$key]['data'][0];
                        $msg_data[$key][$lang] = $other_messages[$key]['data'][0]->translated_value;
                    }
                }
            }
        }
        // Process to assign to ArrayDataProvider
        $dataProvider = new ArrayDataProvider([
            'allModels' => $msg_data,
            'key' => 'modify_key',
            'sort' => [
                'attributes' => array_merge($scanInfo->languages, [
                    'key'
                ])
            ],
            'pagination' => [
                'pageSize' => 20
            ]
        ]);
        return $dataProvider;
    }

    /**
     * Load message by setting
     *
     * @return boolean|\app\common\modules\translate\models\LanguageModel[]
     */
    public function loadMessage()
    {
        // Search the languages to return the ArrayDataProvider
        $scanInfo = LanguageScanInfo::getScanInfo($this->module);
        if (! in_array($this->search_language, $scanInfo->languages)) {
            return false;
        }
        $categories = ($this->search_category == self::TRANSLATED_CATEGORY_ALL) ? $scanInfo->categories : [
            $this->search_category
        ];
        $file_to_read = array();
        foreach ($scanInfo->categories as $cat) {
            if (in_array($cat, $categories)) {
                $origin_files = []; // Files in all language to update if missing category in current language
                foreach ($scanInfo->files as $file) {
                    if (strpos($file, "$cat.php") !== false) {
                        $origin_files[] = $file;
                    }
                }
                $source_file = "$scanInfo->source_message_path/$this->search_language/$cat.php";
                $dest_file = "$scanInfo->destination_message_path/$this->search_language/$cat.php";
                $file_to_read[$cat] = [
                    'origin_files' => $origin_files,
                    'source_file' => $source_file,
                    'dest_file' => $dest_file
                ];
            }
        }
        // Process to load message
        $message_array = [];
        foreach ($file_to_read as $key => $value) {
            $message_array[$key] = array();
            if ($this->search_status == self::TRANSLATED_STATUS_TRANSLATED) {
                // Scan destination file only
                if (file_exists($value['dest_file'])) {
                    $message_array[$key] = [
                        'messages' => array_merge($message_array[$key], require ($value['dest_file'])),
                        'translated' => require ($value['dest_file'])
                    ];
                }
            } else {
                // Scan all this category in other languages
                $other_messages = array();
                foreach ($value['origin_files'] as $file) {
                    if (file_exists($file)) {
                        $other_messages = array_merge($other_messages, require ($file));
                    }
                }
                $translated_message = array();
                if (file_exists($value['dest_file'])) {
                    $translated_message = array_merge($translated_message, require ($value['dest_file']));
                }
                // Search for all message not translated yet
                if ($this->search_status == self::TRANSLATED_STATUS_NOTYET) {
                    // Remove all keys exists in destination file
                    $message_array[$key] = [
                        'messages' => array_diff_key($other_messages, $translated_message),
                        'translated' => $translated_message
                    ];
                } else {
                    $message_array[$key] = [
                        'messages' => array_merge($other_messages, $translated_message),
                        'translated' => $translated_message
                    ];
                }
            }
        }
        $message_result = array();
        // Process to parse message to model
        foreach ($message_array as $key => $value) {
            $itm = new LanguageModel();
            $itm->origin_file_path = "$scanInfo->source_message_path/$this->search_language/$key.php";
            $itm->dest_file_path = "$scanInfo->destination_message_path/$this->search_language/$key.php";
            $itm->language = $this->search_language;
            $itm->category = $key;
            if (isset($value['messages']) && ! empty($value['messages'])) {
                foreach ($value['messages'] as $msg_key => $msg_value) {
                    $msg = clone $itm;
                    $msg->key = $msg_key;
                    if (isset($value['translated'][$msg_key])) {
                        $msg->translated_value = $value['translated'][$msg_key];
                    }
                    if (! empty($this->search_key && strpos($msg_key, $this->search_key) !== false) || empty($this->search_key)) {
                        $message_result[$msg_key] = [
                            'key' => $msg_key,
                            'data' => [
                                $msg
                            ],
                            'category' => $itm->category,
                            'modify_key' => "$itm->category;$msg_key",
                            $itm->language => $msg->translated_value
                        ];
                    }
                }
            }
        }
        return $message_result;
    }
}
