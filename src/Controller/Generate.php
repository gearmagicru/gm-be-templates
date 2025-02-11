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
 * Контроллер формы создания описания шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Controller
 * @since 1.0
 */
class Generate extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'Generate';

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
        $window->title = '#{generate.title}';
        $window->titleTpl = '#{generate.title}';
        $window->iconCls = 'gm-templates__icon-create-desc_small';

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->router->route = Gm::getAlias('@match/generate');
        $window->form->router->state = Form::STATE_CUSTOM;
        $window->form->loadJSONFile('/generate', 'items');
        $window->form->buttons = ExtForm::buttons([
            'info',
            'add' => [
                'iconCls' => 'g-icon-svg g-icon_size_14 g-icon-m_execute',
                'text'    => $this->t('Execute'),
            ],
            'cancel'
        ]);
        return $window;
    }
}
