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
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtForm;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер формы изменения имени файла или директории.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Controller
 * @since 1.0
 */
class Rename extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'Rename';

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->xtype = 'g-window';
        $window->width = 400;
        $window->autoHeight = true;
        $window->layout = 'fit';
        $window->title = '#{rename.title}';
        $window->titleTpl = '#{rename.titleTpl}';
        $window->iconCls = 'g-icon-svg gm-templates__icon-rename';

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->router->setAll([
            'id'    => Gm::$app->request->get('f'),
            'route' => Gm::alias('@match', '/rename'),
            'state' => Form::STATE_CUSTOM,
            'rules' => [
                'update' => '{route}/update/?f={id}',
                'data'   => '{route}/data/?f={id}'
            ]
        ]);
        $window->form->bodyPadding = 5;
        $window->form->buttons = ExtForm::buttons(['help' => ['subject' => 'rename'], 'save', 'cancel']);
        $window->form->loadJSONFile('/rename', 'items');
        return $window;
    }

    /**
     * {@inheritdoc}
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        // если элемент выбран
        if ($request->get('f') === null) {
            $response
                ->meta->error(Gm::t(BACKEND, 'The item you selected does not exist or has been deleted'));
            return $response;
        }

        /** @var \Gm\Panel\Widget\EditWindow $widget */
        $widget = $this->getWidget();
        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }

    /**
     * Действие "update" изменяет записи по указанному идентификатору.
     * 
     * @return Response
     */
    public function updateAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        /** @var \Gm\Panel\Data\Model\FormModel $model модель данных */
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }
        // получение записи по идентификатору в запросе
        $form = $model;
        // загрузка атрибутов в модель из запроса
        if (!$form->load($request->getPost())) {
            $response
                ->meta->error(Gm::t(BACKEND, 'No data to perform action'));
            return $response;
        }
        // валидация атрибутов модели
        if (!$form->validate()) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Error filling out form fields: {0}', [$form->getError()]));
            return $response;
        }
        // сохранение атрибутов модели
        if (!$form->save()) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Could not save data'));
            return $response;
        }
        return $response;
    }
}
