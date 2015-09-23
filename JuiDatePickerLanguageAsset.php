<?php

namespace mackiavelly\juidatepicker;

use yii\web\AssetBundle,
    Yii;


class JuiDatePickerLanguageAsset extends AssetBundle
{

    public $sourcePath = '@bower/jquery-ui/ui/i18n';

    public $depends = ['mackiavelly\juidatepicker\JuiDatePickerAsset'];

    public function init()
    {
        $language = Yii::$app->language;
        if ($language != 'en-US') {
            $sourcePath = Yii::getAlias($this->sourcePath);
            $jsFile = 'datepicker-' . $language . '.js';
            if (is_file($sourcePath . DIRECTORY_SEPARATOR . $jsFile)) {
                $this->js[] = $jsFile;
            } elseif (strlen($language) > 2) {
                $jsFile = 'datepicker-' . substr($language, 0, 2) . '.js';
                if (is_file($sourcePath . DIRECTORY_SEPARATOR . $jsFile)) {
                    $this->js[] = $jsFile;
                }
            }
        }
        parent::init();
    }
}
