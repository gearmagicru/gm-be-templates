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
 * Модель данных создания копии шаблонов модулей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Copy extends FormModel
{
    /**
     * Тема.
     * 
     * @var Theme|null
     */
    protected ?Theme $theme = null;

    /**
     * Назначение темы (backend, frontend).
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
                // всплывающие сообщение
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
        if ($result === false)
            $message = $this->module->t('Unable to copy templates');
        else
            $message = $this->module->t('Copies of templates have been successfully created');
        return [
            'success' => $result !== false,
            'message' => $message,
            'title'   => $this->t('Copying module'),
            'type'    => $result === false ? 'error' : 'accept'
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
            /*$patterns = $this->module->config->translator['patterns'];
            if (!isset($patterns['description'])) {
                 $this->addError($this->t('The module configurator is missing template localization parameters'));
                 return false;
            }*/
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'themeName'  => $this->module->t('Theme')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function formatterRules(): array
    {
        return [
            [['replaceTemplates', 'addDescription', 'backend', 'frontend', 'modules', 'extensions', 
              'widgets'], 'logic' => [true, false]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            'checkEmpty' => [['themeName'], 'notEmpty']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'replaceTemplates' => 'replaceTemplates', // заменить сществующие шаблоны
            'addDescription'   => 'addDescription', // добавить описание
            'themeName'        => 'themeName', // имя темы
            'backend'          => 'backend', // для сайта
            'frontend'         => 'frontend', // для Панели управления
            'modules'          => 'modules', // для модулей
            'extensions'       => 'extensions', // для расширений модулей
            'widgets'          => 'widgets', // для виджетов
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function insertProcess(array $attributes = null): false|int|string
    {
        if (!$this->beforeSave(true))
            return false;
        // возвращает атрибуты без псевдонимов (если они были указаны)
        $columns = $this->unmaskedAttributes($this->attributes);
        $this->beforeInsert($columns);
        $this->result = $this->copyViewFiles($columns);
        $this->afterSave(true, $columns, $this->result);
        return $this->result;
    }

    /**
     * Копирует файлы шаблонов.
     * 
     * @param string $options Настройки:
     *     - 'replaceTemplates' (bool), заменить существующие шаблоны в каталоге темы;
     *     - 'addDescription' (bool), добавить описание к копиям шаблонов;
     *     - 'themeName' (string), имя темы,
     *     - 'frontend' (bool), только для  frontend;
     *     - 'backend' (bool), только для  backend;
     *     - 'modules' (bool), шаблоны модулей;
     *     - 'extensions' (bool), шаблоны расширений модулей;
     *     - 'widgets' (bool), шаблоны виджетов.
     * 
     * @return int|bool
     */
    public function copyViewFiles(array $options)
    {
        $replaceTemplates = boolval($options['replaceTemplates'] ?? false);
        $addDescription   = boolval($options['addDescription'] ?? false);
        $useBackend       = boolval($options['backend'] ?? false);
        $useFrontend      = boolval($options['frontend'] ?? false);
        $useModules       = boolval($options['modules'] ?? false);
        $useExtensions    = boolval($options['extensions'] ?? false);
        $useWidgets       = boolval($options['widgets'] ?? false);

        /** @var array $path Локальные пути компонентов  */
        $path = [];
        if ($useModules) {
            // конфигурации установленных модулей
            $modules = Gm::$app->modules->getRegistry()->getAll();
            foreach ($modules as $params) {
                // для BACKEND
                if ($params['use'] === BACKEND) {
                    if (!$useBackend) continue;
                // дя FRONTEND
                } else {
                    if (!$useFrontend) continue;
                }
                if (isset($params['path'])) {
                    $path[] = $params['path'];
                }
            }
        }

        if ($useWidgets) {
            // конфигурации установленных виджетов
            $widgets = Gm::$app->widgets->getRegistry()->getAll();
            foreach ($widgets as $params) {
                // для BACKEND
                if ($params['use'] === BACKEND) {
                    if (!$useBackend) continue;
                // дя FRONTEND
                } else {
                    if (!$useFrontend) continue;
                }
                if (isset($params['path'])) {
                    $path[] = $params['path'];
                }
            }
        }

        if ($useExtensions) {
            // конфигурации установленных расширений модулей
            $extensions = Gm::$app->extensions->getRegistry()->getAll();
            foreach ($extensions as $params) {
                // для BACKEND
                if ($params['use'] === BACKEND) {
                    if (!$useBackend) continue;
                // дя FRONTEND
                } else {
                    if (!$useFrontend) continue;
                }
                if (isset($params['path'])) {
                    $path[] = $params['path'];
                }
            }
        }

        /** @var int|string $result  */
        $result = $this->theme->copyViewFiles($path, $options['themeName'], $replaceTemplates);
        if (is_string($result)) {
            $this->addError($result);
            return false;
        } else
            $count = $result;

        // если необходимо добавить описание к шаблонам
        if ($addDescription) {
            $viewsInfo = $this->theme->getViewsInfo();
            $viewsInfo->generateDescription('all', $options['themeName']);
            return !$viewsInfo->save($options['themeName']) ? false : $count;
        }
        return $count;
    }
}
