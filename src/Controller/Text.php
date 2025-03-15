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
use Gm\Stdlib\BaseObject;
use Gm\Panel\Http\Response;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;
use Gm\Backend\Templates\Widget\TextWindow;

/**
 * Контроллер формы текста шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Controller
 * @since 1.0
 */
class Text extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'Text';

    /**
     * Виджет редактора.
     * 
     * @see Text::getEditorWidget()
     * 
     * @var BaseObject
     */
    protected BaseObject $editor;

    /**
     * Возвращает параметры конфигурации редактора.
     * 
     * @return null|BaseObject
     */
    protected function getEditorWidget(): ?BaseObject
    {
        /** @var \Gm\Backend\Templates\Model\Text $model */
        $model = $this->getModel($this->defaultModel);

        $editor = Gm::$app->widgets->get('gm.wd.codemirror', [
            'fileExtension' => $model->getFileExtension()
        ]);
        if ($editor) {
            $editor->initResponse($this->getResponse());
            return $editor;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TextWindow
    {
        /** @var BaseObject|null $editor */
        $editor = $this->getEditorWidget();

        $window = new TextWindow();
        // указаывает редактор для окна
        $window->setEditor($editor ? $editor->run() : []);
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

        // если элемент не выбран
        if ($request->get('f') === null) {
            $response
                ->meta
                    ->error(Gm::t(BACKEND, 'The item you selected does not exist or has been deleted'));
            return $response;
        }

        /** @var EditWindow $widget */
        $widget = $this->getWidget();
        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);

        /** @var object|BaseObject $editor */
        $editor = $this->getEditorWidget();
        // добавление в ответ скриптов 
        if ($editor) {
            if (method_exists($editor, 'initResponse')) {
                $editor->initResponse($response);
            }
        }
        return $response;
    }

    /**
     * Действие "update" изменяет записи по указанному идентификатору.
     * 
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
