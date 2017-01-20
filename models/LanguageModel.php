<?php
namespace app\modules\translator\models;

use Yii;
use yii\base\Model;
use app\modules\translator\helper\LanguageScanInfo;
use yii\helpers\FileHelper;

class LanguageModel extends Model
{

    /**
     * The path to origin language file
     *
     * @var unknown
     */
    public $origin_file_path;

    /**
     * The path to destination language file
     *
     * @var unknown
     */
    public $dest_file_path;

    /**
     * The language code
     *
     * @var unknown
     */
    public $language;

    /**
     * The language key
     *
     * @var unknown
     */
    public $key;

    /**
     * The translated value
     *
     * @var unknown
     */
    public $translated_value;

    /**
     * The language category
     *
     * @var unknown
     */
    public $category;

    /**
     * Update language to new value
     *
     * @param unknown $language            
     * @param unknown $category            
     * @param unknown $key            
     * @param unknown $new_message            
     */
    public static function modifyMessage($language, $category, $key, $new_message, $module)
    {
        $scanInfo = LanguageScanInfo::getScanInfo($module);
        $dest_file = "$scanInfo->destination_message_path/$language/$category.php";
        if (! empty($new_message)) {
            if (! file_exists("$scanInfo->destination_message_path/$language")) {
                FileHelper::createDirectory("$scanInfo->destination_message_path/$language");
            }
            try {
                $dest_array = array();
                if (is_file($dest_file) && file_exists($dest_file)) {
                    $dest_array = require ($dest_file);
                }
                // Modify/add lanugages key
                $dest_array[$key] = $new_message;
                // Write array to file
                file_put_contents($dest_file, '<?php return ' . var_export($dest_array, true) . ';');
                LanguageScanInfo::getScanInfo($module, true);
                return true;
            } catch (\IOException $ex) {
                throw new \Exception("The language file is not writable " . $dest_file);
            }
        }
        return false;
    }
}
