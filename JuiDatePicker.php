<?php

namespace mackiavelly\juidatepicker;

use yii\helpers\Html,
    yii\widgets\InputWidget,
    yii\base\InvalidParamException,
    yii\web\JsExpression,
    yii\helpers\Json,
    Yii;


class JuiDatePicker extends InputWidget
{

    public $options = ['class' => 'form-control'];

    public $dateFormat = null;

    public $altDateFormat = null;

    public $numberOfMonths = 1;

    public $showButtonPanel = false;

    public $clientOptions = [];
    
    public $ignoreReadonly = false;
    
    public $disableAlt = false;

    public function init()
    {
        if (is_null($this->dateFormat)) {
            $this->dateFormat = Yii::$app->getFormatter()->dateFormat;
            if (is_null($this->dateFormat)) {
                $this->dateFormat = 'medium';
            }
        }
        if (is_null($this->altDateFormat)) {
            $this->altDateFormat = $this->dateFormat;
        }
        parent::init();
    }

    public function run()
    {
        $inputId = $this->options['id'];
        $altInputId = $inputId . '-alt';
        $hasModel = $this->hasModel();
        if (array_key_exists('value', $this->options)) {
            $value = $this->options['value'];
        } elseif ($hasModel) {
            $value = Html::getAttributeValue($this->model, $this->attribute);
        } else {
            $value = $this->value;
        }
        $altOptions = [
            'id' => $altInputId,
            'name' => $altInputId,
        ];
        if (!is_null($value) && ($value !== '')) {
            $formatter = Yii::$app->getFormatter();
            try {
                $this->options['value'] = $formatter->asDate($value, $this->dateFormat);
                $altOptions['value'] = $formatter->asDate($value, $this->altDateFormat);
            } catch (InvalidParamException $e) {
                // ignore exception and keep original value if it is not a valid date
            }
        }
        if ($hasModel) {
            $output = Html::activeTextInput($this->model, $this->attribute, $this->options)
                .((!$this->disableAlt) ? Html::activeHiddenInput($this->model, $this->attribute, $altOptions) : null);
        } else {
            $output = Html::textInput($this->name, $this->value, $this->options)
                .((!$this->disableAlt) ? Html::hiddenInput($this->name, $this->value, $altOptions) : null);
        }
        $this->clientOptions = array_merge([
            'numberOfMonths' => $this->numberOfMonths,
            'showButtonPanel' => $this->showButtonPanel
        ], $this->clientOptions, [
            'dateFormat' => FormatConverter::convertDatePhpOrIcuToJui($this->dateFormat),
            'altFormat' => FormatConverter::convertDatePhpOrIcuToJui($this->altDateFormat),
            'altField' => '#' . $altInputId
        ]);
        if (!$this->ignoreReadonly && array_key_exists('readonly', $this->options) && $this->options['readonly']) {
            $this->clientOptions['beforeShow'] = new JsExpression('function (input, inst) { return false; }');
        }
        $js = 'jQuery(\'#' . $inputId . '\').datepicker(' . Json::htmlEncode($this->clientOptions) . ');';
        if (Yii::$app->getRequest()->getIsAjax()) {
            $output .= Html::script($js);
        } else {
            $view = $this->getView();
            JuiDatePickerAsset::register($view);
            JuiDatePickerLanguageAsset::register($view);
            $view->registerJs($js);
        }
        return $output;
    }
}
