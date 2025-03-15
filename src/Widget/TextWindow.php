<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Templates\Widget;

use Gm;
use Gm\Panel\Widget\Form;
use Gm\Panel\Helper\ExtForm;

/**
 * Виджет для формирования интерфейса окна текста шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Widget
 * @since 1.0
 */
class TextWindow extends \Gm\Panel\Widget\EditWindow
{
    /**
     * Параметры виджета редактора текста.
     * 
     * @see TextWindow::setEditor()
     * 
     * @var array
     */
    protected array $editor = [];

    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Gm.view.form.Panel GmJS)
        $this->form->router->setAll([
            'id'    => Gm::$app->request->get('f'),
            'route' => Gm::alias('@match', '/text'),
            'state' => Form::STATE_CUSTOM,
            'rules' => [
                'update' => '{route}/update/?f={id}',
                'data'   => '{route}/data/?f={id}'
            ]
        ]);
        $this->form->buttons = ExtForm::buttons([
            'help' => ['subject' => 'template'], 'save', 'cancel'
        ]);

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $this->width = 900;
        $this->height = 500;
        $this->bodyPadding = 1;
        $this->maximizable = true;
        $this->layout = 'fit';
        $this->title = '#{text.title}';
        $this->titleTpl = '#{text.titleTpl}';
        $this->iconCls = 'g-icon-svg gm-templates__icon-text';
        $this->responsiveConfig = [
            'width < 900'  => ['width' => '99%'],
            'height < 500' => ['height' => '99%']
        ];
    }

    /**
     * Устанавливает редактор тексту.
     * 
     * @param array $editor Редактор.
     * 
     * @return $this
     */
    public function setEditor(array $editor): static
    {
        if (empty($editor)) {
            $editor = [
                'xtype'  => 'textarea',
                'anchor' => '100% 100%'
            ];
        }
        $editor['name'] = 'text';
        $this->editor = $editor;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRender(): bool
    {
        parent::beforeRender();

        $this->form->items = $this->editor;
        return true;
    }
}
