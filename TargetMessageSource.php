<?php
namespace app\modules\translator;

use Yii;
use yii\i18n\PhpMessageSource;

class TargetMessageSource extends PhpMessageSource
{

    /**
     * The path to translated messages which used to merge with origin message
     *
     * @var string
     */
    public $translatedPath = "@runtime/messages";

    /**
     * Loads the message translation for the specified $language and $category.
     * If translation for specific locale code such as `en-US` isn't found it
     * tries more generic `en`. When both are present, the `en-US` messages will be merged
     * over `en`. See [[loadFallbackMessages]] for details.
     * If the $language is less specific than [[sourceLanguage]], the method will try to
     * load the messages for [[sourceLanguage]]. For example: [[sourceLanguage]] is `en-GB`,
     * $language is `en`. The method will load the messages for `en` and merge them over `en-GB`.
     *
     * @param string $category
     *            the message category
     * @param string $language
     *            the target language
     * @return array the loaded messages. The keys are original messages, and the values are the translated messages.
     * @see loadFallbackMessages
     * @see sourceLanguage
     */
    protected function loadMessages($category, $language)
    {
        $message_resource = array();
        $messageFiles = [
            $this->getMessageFilePath($category, $language),
            $this->getTargetMessageFilePath($category, $language)
        ];
        foreach ($messageFiles as $messageFile) {
            $messages = $this->loadMessagesFromFile($messageFile);
            
            $fallbackLanguage = substr($language, 0, 2);
            $fallbackSourceLanguage = substr($this->sourceLanguage, 0, 2);
            
            if ($language !== $fallbackLanguage) {
                $messages = $this->loadFallbackMessages($category, $fallbackLanguage, $messages, $messageFile);
            } elseif ($language === $fallbackSourceLanguage) {
                $messages = $this->loadFallbackMessages($category, $this->sourceLanguage, $messages, $messageFile);
            } else {
                if ($messages === null) {
                    Yii::error("The message file for category '$category' does not exist: $messageFile", __METHOD__);
                }
            }
            if (! empty($messages) && is_array($messages)) {
                $message_resource = array_merge($message_resource, $messages);
            }
        }
        return (array) $message_resource;
    }

    /**
     * Returns message file path for the specified language and category.
     *
     * @param string $category
     *            the message category
     * @param string $language
     *            the target language
     * @return string path to message file
     */
    protected function getTargetMessageFilePath($category, $language)
    {
        $messageFile = Yii::getAlias($this->translatedPath) . "/$language/";
        if (isset($this->fileMap[$category])) {
            $messageFile .= $this->fileMap[$category];
        } else {
            $messageFile .= str_replace('\\', '/', $category) . '.php';
        }
        
        return $messageFile;
    }
}
