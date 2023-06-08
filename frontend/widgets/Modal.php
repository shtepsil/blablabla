<?php

namespace frontend\widgets;

use common\components\Debugger as d;
use frontend\assets\ModalPluginAsset;
use shadow\helpers\Json;
use Yii;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Widget;
use yii\bootstrap\Html;

/**
 * Modal renders a modal window that can be toggled by clicking on a button.
 *
 * The following example will show the content enclosed between the [[begin()]]
 * and [[end()]] calls within the modal window:
 *
 * ~~~php
 * Modal::begin([
 *     'header' => '<h2>Hello world</h2>',
 *     'toggleButton' => ['label' => 'click me'],
 * ]);
 *
 * echo 'Say hello...';
 *
 * Modal::end();
 * ~~~
 *
 * @see http://getbootstrap.com/javascript/#modals
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Modal extends Widget
{
    const SIZE_LARGE = "modal-lg";
    const SIZE_SMALL = "modal-sm";
    const SIZE_DEFAULT = "";

    /**
     * @var string the header content in the modal window.
     */
    public $header;
    public $test = false;
    /**
     * @var string additional header options
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @since 2.0.1
     */
    public $headerOptions;
    /**
     * @var array body options
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $bodyOptions = [];
    public $body = '';

    /**
     * @var array Атрибуты самого плавающего модального окна
     */
    public $windowOptions = [];

    /**
     * Текст описания под заголовокм
     * @var string
     */
    public $description = '';
    /**
     * Атрибуты блока текста под заголовоком
     * @var
     */
    public $descriptionOptions;
    /**
     * @var string the footer content in the modal window.
     */
    public $footer;
    /**
     * @var string additional footer options
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     * @since 2.0.1
     */
    public $footerOptions = [];
    /**
     * @var string the modal size. Can be [[SIZE_LARGE]] or [[SIZE_SMALL]], or empty for default.
     */
    public $size;
    /**
     * @var array|false the options for rendering the close button tag.
     * The close button is displayed in the header of the modal window. Clicking
     * on the button will hide the modal window. If this is false, no close button will be rendered.
     *
     * The following special options are supported:
     *
     * - tag: string, the tag name of the button. Defaults to 'button'.
     * - label: string, the label of the button. Defaults to '&times;'.
     *
     * The rest of the options will be rendered as the HTML attributes of the button tag.
     * Please refer to the [Modal plugin help](http://getbootstrap.com/javascript/#modals)
     * for the supported HTML attributes.
     */
    public $closeButton = [];
    /**
     * @var array the options for rendering the toggle button tag.
     * The toggle button is used to toggle the visibility of the modal window.
     * If this property is false, no toggle button will be rendered.
     *
     * The following special options are supported:
     *
     * - tag: string, the tag name of the button. Defaults to 'button'.
     * - label: string, the label of the button. Defaults to 'Show'.
     *
     * The rest of the options will be rendered as the HTML attributes of the button tag.
     * Please refer to the [Modal plugin help](http://getbootstrap.com/javascript/#modals)
     * for the supported HTML attributes.
     */
    public $toggleButton = false;

    public $toggleElement = false;


    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();

        $this->initOptions();

        echo $this->renderToggleButton() . "\n";
        echo Html::beginTag('div', $this->options) . "\n";
        echo Html::beginTag('div', $this->windowOptions) . "\n";
        echo $this->renderClose();
        echo $this->renderHeader() . "\n";
        echo $this->renderDescription() . "\n";
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
//        echo "\n" . $this->renderBody();
        echo "\n" . $this->renderBody();
        echo "\n" . $this->renderFooter();
        echo "\n" . Html::endTag('div');
        echo "\n" . Html::endTag('div');

        $this->test = false;

        $this->registerPlugin('popup');
    }

    /**
     * Registers a specific Bootstrap plugin and the related events
     * @param string $name the name of the Bootstrap plugin
     */
    protected function registerPlugin($name)
    {
        ModalPluginAsset::register($this->view);

        $id = $this->options['id'];

        if ($this->clientOptions !== false) {
            $this->clientOptions['block_id'] = "#$id";
            $options = empty($this->clientOptions) ? '' : Json::htmlEncode($this->clientOptions);
            $js = "jQuery('#$id').$name($options);";
            $this->view->registerJs($js);
        }

        $this->registerClientEvents();
    }

    /**
     * Renders the header HTML markup of the modal
     * @return string the rendering result
     */
    protected function renderHeader()
    {
        if ($this->header !== null) {
            Html::addCssClass($this->headerOptions, ['popupTitle']);
            return Html::tag('div', $this->header, $this->headerOptions);
        } else {
            return null;
        }
    }

    /**
     * Renders the header HTML markup of the modal
     * @return string the rendering result
     */
    protected function renderDescription()
    {
        if ($this->description != '') {
            Html::addCssClass($this->descriptionOptions, ['popupDescription']);
            return Html::tag('div', "\n" . $this->description . "\n", $this->descriptionOptions);
        } else {
            return null;
        }
    }

    /**
     * Renders the opening tag of the modal body.
     * @return string the rendering result
     */
    protected function renderBodyBegin()
    {
        return Html::beginTag('div', ['class' => 'modal-body']);
    }

    /**
     * Renders the closing tag of the modal body.
     * @return string the rendering result
     */
    protected function renderBodyEnd()
    {
        return Html::endTag('div');
    }

    protected function renderBody()
    {
        Html::addCssClass($this->bodyOptions, 'popupBody');
        return Html::tag('div', $this->body, $this->bodyOptions);
    }

    /**
     * Renders the HTML markup for the footer of the modal
     * @return string the rendering result
     */
    protected function renderFooter()
    {
        if ($this->footer !== null) {
            Html::addCssClass($this->footerOptions, ['popupBottom']);
            return Html::tag('div', "\n" . $this->footer . "\n", $this->footerOptions);
        } else {
            return null;
        }
    }

    /**
     * Renders the toggle button.
     * @return string the rendering result
     */
    protected function renderToggleButton()
    {
        if (
            (($toggleElement = $this->toggleButton) !== false)
            OR (($toggleElement = $this->toggleElement) !== false)
        ) {
            $tag = ArrayHelper::remove($toggleElement, 'tag', 'div');
            $label = ArrayHelper::remove($toggleElement, 'label', 'Show');
            if ($tag === 'button' && !isset($toggleElement['type'])) {
                $toggleElement['type'] = 'button';
            }
            $result = Html::tag($tag, $label, $toggleElement);
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * Renders the close button.
     * @return string the rendering result
     */
    protected function renderClose()
    {
        if (($closeButton = $this->closeButton) !== false) {
            $tag = ArrayHelper::remove($closeButton, 'tag', 'div');
//            $label = ArrayHelper::remove($closeButton, 'label', '&times;');
            $label = '';
            if ($tag === 'button' && !isset($closeButton['type'])) {
                $closeButton['type'] = 'button';
            }
            return Html::tag($tag, $label, $closeButton) . "\n";
        } else {
            return null;
        }
    }

    /**
     * Initializes the widget options.
     * This method sets the default values for various options.
     */
    protected function initOptions()
    {
        $this->options = array_merge([
            'class' => 'overlay-modal',
//            'role' => 'dialog',
//            'tabindex' => -1,
        ], $this->options);
//        Html::addCssClass($this->options, ['widget' => 'modal']);

//        $this->windowOptions = array_merge();

        if ($this->windowOptions !== false) {
            $windowClasses = ['class' => 'popup window'];
            if(isset($this->windowOptions['class'])){
                Html::addCssClass($windowClasses, $this->windowOptions['class']);
            }
            $this->windowOptions = array_merge($this->windowOptions, $windowClasses);
        }

        if ($this->clientOptions !== false) {
            $this->clientOptions = array_merge(['show' => false], $this->clientOptions);
        }

        if ($this->closeButton !== false) {
            $id = $this->options['id'];
            $this->closeButton = array_merge([
                'data-dismiss' => "#$id",
//                'aria-hidden' => 'true',
                'class' => 'popupClose',
            ], $this->closeButton);
        }

        if ($this->toggleButton !== false) {
            $this->toggleButton = array_merge([
                'data-toggle' => 'popup',
            ], $this->toggleButton);
            if (!isset($this->toggleButton['data-target']) && !isset($this->toggleButton['href'])) {
                $this->toggleButton['data-target'] = '#' . $this->options['id'];
            }
        }

        /**
         * Атрибуты для элемента, по клику которого
         * произойдёт вызов модального окна
         */
        if ($this->toggleElement !== false) {
            $this->toggleElement = array_merge([
                'data-toggle' => 'popup',
            ], $this->toggleElement);
            if (!isset($this->toggleElement['data-target']) && !isset($this->toggleElement['href'])) {
                $this->toggleElement['data-target'] = '#' . $this->options['id'];
            }
            if(isset($this->toggleElement['tag']) AND $this->toggleElement['tag'] == 'a'){
                $this->toggleElement['href'] = '#' . $this->options['id'];
            }
        }
    }
}
