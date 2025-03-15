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

/**
 * Виджет для формирования интерфейса окна описания шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Widget
 * @since 1.0
 */
class DescriptionWindow extends \Gm\Panel\Widget\EditWindow
{
    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Gm.view.form.Panel GmJS)
        $this->form->controller = 'gm-be-templates-description';
        $this->form->defaults = [
            'xtype'      => 'textfield',
            'anchor'     => '100%',
            'labelAlign' => 'right',
            'labelWidth' => 90
        ];
        $this->form->router->setAll([
            'id'    => Gm::$app->request->get('f'),
            'route' => Gm::alias('@match', '/description'),
            'state' => Form::STATE_UPDATE,
            'rules' => [
                'update' => '{route}/update/?f={id}',
                'data'   => '{route}/data/?f={id}',
                'delete' => '{route}/delete/?f={id}',
            ]
        ]);
        $this->form->setStateButtons(
            Form::STATE_UPDATE,
            ['help' => ['subject' => 'description'], 'reset', 'save', 'delete', 'cancel']
        );
        $this->form->loadJSONFile('/description', 'items', [
            // назначение
            '@use' => [
                ['', Gm::t(BACKEND, '[None]')],
                [BACKEND, Gm::t(BACKEND, BACKEND_NAME)],
                [FRONTEND, Gm::t(BACKEND, FRONTEND_NAME)]
            ]
        ]);
        $this->form->autoScroll = true;

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $this->resizable = false;
        $this->width = 500;
        $this->padding = 0;
        $this->autoHeight = true;
        $this->layout = 'fit';
        $this->title = '#{description.title}';
        $this->titleTpl = '#{description.titleTpl}';
        $this->iconCls = 'g-icon-svg g-icon-m_edit';
        $this->resizable = false;
        $this->responsiveConfig = [
            'height < 560' => ['height' => '99%'],
            'width < 500' => ['width' => '99%'],
        ];

        $this
            ->setNamespaceJS('Gm.be.templates')
            ->addRequire('Gm.be.templates.DescriptionController');
    }
}
