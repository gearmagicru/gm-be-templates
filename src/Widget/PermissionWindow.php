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
 * Виджет для формирования интерфейса окна прав доступа.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Widget
 * @since 1.0
 */
class PermissionWindow extends \Gm\Panel\Widget\EditWindow
{
    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Gm.view.form.Panel GmJS)
        $this->form->controller = 'gm-be-templates-permission';
        $this->form->router->setAll([
            'id'    => Gm::$app->request->get('f'),
            'route' => Gm::alias('@match', '/permission'),
            'state' => Form::STATE_CUSTOM,
            'rules' => [
                'update' => '{route}/update/?f={id}',
                'data'   => '{route}/data/?f={id}'
            ]
        ]);
        $this->form->bodyPadding = 5;
        $this->form->buttons = ExtForm::buttons([
            'help' => ['subject' => 'permissions'], 'save', 'cancel'
        ]);
        $this->form->loadJSONFile('/permission', 'items');

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $this->resizable = false;
        $this->width = 500;
        $this->autoHeight = true;
        $this->layout = 'fit';
        $this->title = '#{permission.title}';
        $this->titleTpl = '#{permission.titleTpl}';
        $this->iconCls = 'g-icon-svg gm-templates__icon-shield';
        $this->resizable = false;
        $this->responsiveConfig = [
            'width < 500' => ['width' => '99%']
        ];

        $this
            ->setNamespaceJS('Gm.be.templates')
            ->addRequire('Gm.be.templates.PermissionController');
    }
}
