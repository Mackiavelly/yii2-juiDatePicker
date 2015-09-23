<?php

namespace mackiavelly\juidatepicker;

use yii\helpers\BaseFormatConverter;


class FormatConverter extends BaseFormatConverter
{

    public static function convertDatePhpOrIcuToJui($pattern, $type = 'date', $locale = null)
    {
        if (strncmp($pattern, 'php:', 4) === 0) {
            return static::convertDatePhpToJui(substr($pattern, 4));
        } else {
            return static::convertDateIcuToJui($pattern, $type, $locale);
        }
    }
}
