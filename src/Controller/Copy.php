<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Templates\Controller;

use Gm;
use Gm\Panel\Widget\Form;
use Gm\Panel\Helper\ExtForm;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер формы создания копии шаблонов модулей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Controller
 * @since 1.0
 */
class Copy extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'Copy';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 420;
        $window->autoHeight = true;
        $window->padding = 0;
        $window->layout = 'fit';
        $window->resizable = false;
        $window->title = '#{copy.title}';
        $window->titleTpl = '#{copy.title}';
        $window->iconCls = 'gm-templates__icon-create-copy_small';

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->router->route = Gm::alias('@match', '/copy');
        $window->form->router->state = Form::STATE_CUSTOM;
        $window->form->loadJSONFile('/copy', 'items');
        $window->form->buttons = ExtForm::buttons([
            'info',
            'add' => [
                'iconCls' => 'g-icon-svg g-icon_size_14 g-icon-m_execute',
                'text'    => $this->t('Create')
            ],
            'cancel'
        ]);
        return $window;
    }
}
