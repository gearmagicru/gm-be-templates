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
 * Виджет для формирования интерфейса окна изменения имени файла или директории.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Widget
 * @since 1.0
 */
class RenameWindow extends \Gm\Panel\Widget\EditWindow
{
    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // панель формы (Gm.view.form.Panel GmJS)
        $this->form->router->setAll([
            'id'    => Gm::$app->request->get('f'),
            'route' => Gm::alias('@match', '/rename'),
            'state' => Form::STATE_CUSTOM,
            'rules' => [
                'update' => '{route}/update/?f={id}',
                'data'   => '{route}/data/?f={id}'
            ]
        ]);
        $this->form->bodyPadding = 5;
        $this->form->buttons = ExtForm::buttons([
            'help' => ['subject' => 'rename'], 'save', 'cancel'
        ]);
        $this->form->loadJSONFile('/rename', 'items');

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $this->width = 400;
        $this->autoHeight = true;
        $this->layout = 'fit';
        $this->title = '#{rename.title}';
        $this->titleTpl = '#{rename.titleTpl}';
        $this->iconCls = 'g-icon-svg gm-templates__icon-rename';
        $this->responsiveConfig = [
            'width < 400' => ['width' => '99%']
        ];
    }
}
