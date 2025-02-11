<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Templates\Model;

use Gm;
use Gm\Theme\Theme;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля описания шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Generate extends FormModel
{
    /**
     * Тема.
     * 
     * @var Theme|null
     */
    protected ?Theme $theme = null;

    /**
     * Клиентская сторона темы.
     * 
     * Сторона: `BACKEND`, `FRONTEND`.
     * 
     * @var string|null
     */
    protected ?string $themeSide = null;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                // всплывающее сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function saveMessage(bool $isInsert, int $result): array
    {
        if ($result > 0)
            $message = $this->module->t($isInsert ? 'Template description successfully added' : 'Template description successfully update');
        else
            $message = $this->module->t($isInsert ? 'Unable to add template description' : 'Unable to update template description');
        return [
            'success' => $result > 0,
            'message' => $message,
            'title'   => $this->module->t($isInsert ? 'Adding template description' : 'Update template description'),
            'type'    => $result > 0 ? 'accept' : ($isInsert ? 'error' : 'warning')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // определение имени темы и какой стороне она принадлежит
            $theme = Gm::$app->theme->defineThemeFromStr($this->themeName, '::', true);
            if ($theme) {
                $this->themeName = $theme['name'];
                $this->themeSide = $theme['side'];
                $this->theme     = $theme['theme'];
            } else {
                $this->addError($this->t('The theme you selected does not exist'));
                return false;
            }

            // если тема не существует
            if (!$this->theme->exists($this->themeName)) {
                 $this->addError($this->t('The theme you selected does not exist'));
                 return false;
            }

            // файл локализации для описания шаблонов
            $translator = $this->module->getConfigParam('translator');
            if (!isset($translator['patterns']['description'])) {
                 $this->addError($this->t('The module configurator is missing template localization parameters'));
                 return false;
            }
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'themeName' => $this->module->t('Theme'),
            'type'      => $this->module->t('Type description')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            'checkEmpty' => [['themeName', 'type'], 'notEmpty']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'type'      => 'type',
            'themeName' => 'themeName'
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function insertProcess(array $attributes = null): false|int|string
    {
        if (!$this->beforeSave(true)) {
            return false;
        }

        // возвращает атрибуты без псевдонимов (если они были указаны)
        $columns = $this->unmaskedAttributes($this->attributes);
        $this->beforeInsert($columns);

        $this->result = $this->generateDescription($columns);
        $this->afterSave(true, $columns, $this->result);
        return $this->result;
    }

    /**
     * Создаёт описание файлов (шаблонов).
     * 
     * @param array $options Настройки:
     *     - 'type' (string), вид описания;
     *     - 'theme' (string), имя темы.
     * 
     * @return false|int
     */
    public function generateDescription(array $options): false|int
    {
        /** @var \Gm\Theme\Info\ViewsInfo $viewsInfo */
        $viewsInfo = $this->theme->getViewsInfo();
        // удалить информацию о шаблонах
        if ($options['type'] === 'clear')
            $viewsInfo->clear();
        else
            $viewsInfo->generateDescription($options['type'], $this->themeName);
        return !$viewsInfo->save($this->themeName) ? false : 1;
    }
}
