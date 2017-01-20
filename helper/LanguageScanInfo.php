<?php
namespace app\modules\translator\helper;

use Yii;
use yii\helpers\FileHelper;

/**
 * This class is used to scan all the original message in all languages and write to temporary file
 * for listing on translating page
 */
final class LanguageScanInfo
{

    /**
     * The last messages scanning date
     *
     * @var unknown
     */
    public $scan_date;

    /**
     * The languages in the systems
     *
     * @var unknown
     */
    public $languages;

    /**
     * Languages categories
     *
     * @var unknown
     */
    public $categories;

    /**
     * Language file list
     * To get a language file: path/to/message_folder/language/[category].php
     *
     * @var unknown
     */
    public $files;

    /**
     * The source message folder
     *
     * @var unknown
     */
    public $source_message_path;

    /**
     * The destination message folder
     *
     * @var unknown
     */
    public $destination_message_path;

    /**
     * Singleton private contructor
     */
    private function __construct()
    {}

    /**
     * Create translated message zip file
     *
     * @return string
     */
    public function createTranslatedZip()
    {
        $export_path = Yii::getAlias("@runtime/files/export");
        if (! file_exists($export_path)) {
            FileHelper::createDirectory($export_path);
        }
        $zip_file = $export_path . "/messages_" . time() . ".zip";
        HZip::zipDir($this->destination_message_path, $zip_file);
        return $zip_file;
    }

    /**
     * Get scan info of messages
     *
     * @param unknown $module            
     * @param string $re_scan            
     * @throws \Exception
     * @return \app\common\modules\translate\models\ScanInfo
     */
    public static function getScanInfo($module, $re_scan = false)
    {
        $source_message_path = Yii::getAlias($module->originLanguagePath);
        $destination_message_path = Yii::getAlias($module->targetLanguagePath);
        $scan_info_file = Yii::getAlias('@runtime/logs') . "/scan_info.data";
        static $scan_obj = null;
        if (! is_dir($destination_message_path)) {
            FileHelper::createDirectory($destination_message_path);
        }
        if (file_exists($scan_info_file) && ! $re_scan) {
            // Deserialize scan info file
            $file_content = file_get_contents($scan_info_file);
            $scan_obj = unserialize($file_content);
        } else {
            $scan_obj = new LanguageScanInfo();
            $scan_obj->scan_date = time();
            $scan_obj->source_message_path = $source_message_path;
            $scan_obj->destination_message_path = $destination_message_path;
            // Read all language folders
            $dirs = array_filter(glob($scan_obj->source_message_path . "/*"), 'is_dir');
            // Read directory info and save to scan_info.data file
            $scan_obj->files = FileHelper::findFiles($scan_obj->source_message_path);
            foreach ($dirs as $dir) {
                $scan_obj->languages[] = str_replace($scan_obj->source_message_path . "/", "", $dir);
            }
            foreach ($scan_obj->files as $file) {
                $scan_obj->categories[] = str_replace(".php", "", basename($file));
            }
            $scan_obj->categories = array_unique($scan_obj->categories);
            $obj_content = serialize($scan_obj);
            // Put to content file
            file_put_contents($scan_info_file, $obj_content);
        }
        if ($scan_obj == null) {
            throw new \Exception("Can't read language directory at $destination_message_path");
        }
        return $scan_obj;
    }
}
