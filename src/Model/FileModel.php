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
use Gm\Exception;
use Gm\Theme\Theme;
use Gm\Panel\Data\Model\FormModel;

/**
 * Базовая модель файла шаблона темы.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class FileModel extends FormModel
{
    /**
     * Абсолютный путь к шаблонам указанной темы.
     * 
     * @var string
     */
    protected string $viewPath = '';

    /**
     * Имя темы.
     * 
     * @var string
     */
    protected string $themeName = '';

    /**
     * Назначение темы (backend, frontend).
     * 
     * @var string
     */
    protected string $themeSide = '';

    /**
     * Тема.
     * 
     * @var Theme
     */
    protected ?Theme $theme = null;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // определение имени темы и какой стороне она принадлежит
        $theme = Gm::$app->theme->defineThemeFromStr($this->getThemeName(), '::', true);
        if ($theme) {
            $this->themeName = $theme['name'];
            $this->themeSide = $theme['side'];
            $this->theme     = $theme['theme'];
        } else
            throw new Exception\InvalidArgumentException('Theme name was incorrectly passed or not selected in the filter.');
        // абсолютный путь к шаблонам указанной темы
        $this->viewPath = $this->theme->getViewPath($this->themeName);
        $this
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\FormController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            })
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
     * Возвращает имя темы из фильтра списка шаблонов.
     * 
     * @return string Если знчение '', имя темы в фильтре отсутствует.
     */
    public function getThemeName(): string
    {
        $store = $this->module->getStorage();
        if ($store->directFilter !== null) {
            $filter = $store->directFilter['Grid'] ?? [];
            foreach ($filter as $field) {
                if ($field['property'] === 'themeName')
                    return $field['value'];
            }
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): mixed
    {
        if ($this->identifier === null) {
            $filename = Gm::$app->request->get('f');
            if ($filename === null || $this->viewPath === null)
                return null;
            //$filename = Gm::$app->encrypter->decryptString($filename);
            if (!file_exists($this->viewPath . $filename))
                return null;
            $this->identifier = $filename;
        }
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function get(mixed $identifier = null): ?static
    {
        if ($identifier === null)
            $identifier = $this->getIdentifier();
        if ($identifier)
            return $this->getFileAttributes($identifier);
        return null;
    }

    /**
     * Возвращает ррасширения файла из идентификатора.
     * 
     * @return string|null
     */
    public function getFileExtension(): ?string
    {
        $filename = $this->getIdentifier();
        if ($filename !== null)
            return pathinfo($filename, PATHINFO_EXTENSION);
        else
            return null;
    }

    /**
     * Устанавливает атрибутам модели имя указанного ресурса.
     * 
     * @params string $filename Имя файла, директория.
     * 
     * @return $this
     */
    public function getFileAttributes(string $filename, array $attributes = []): static
    {
        $this->reset();
        $this->afterSelect();
        $this->populate($this, $attributes);
        $this->afterPopulate();
        return $this;
    }

    /**
     * Возвращает режим редактора из указанного расширения файла.
     * 
     * @param string $extension Расширение файла.
     * 
     * @return string|null
     */
    public function getModeEditor(string $extension): ?string
    {
        $mode = [
            'apl'   => 'text/apl',
            'h'     => 'text/x-csrc',
            'php'   => 'application/x-httpd-php',
            'phtml' => 'application/x-httpd-php',
            'scss'  => 'text/x-scss',
            'gss'   => 'text/x-gss',
            'less'  => 'text/x-less',
            'css'   => 'text/x-gss',
            'html'  => 'text/html',
            'json'  => 'application/ld+json',
            'jsx'   => 'jsx',
            'js'    => 'text/typescript',
            'sql'   => 'text/x-mariadb',
            'xml'   => 'text/html'
        ];
        return $mode[strtolower($extension)] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function save(bool $useValidation = false,  array $attributeNames = null): bool|int|string
    {
        return $this->update($useValidation, $attributeNames) !== false;
    }
}
