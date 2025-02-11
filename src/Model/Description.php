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
use Closure;
use Gm\Db\Sql\Where;

/**
 * Модель описания шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Description extends FileModel
{
    /**
     * {@inheritdoc}
     */
    public function saveMessage(bool $isInsert, int $result): array
    {
        if ($result > 0)
            $message = $this->module->t('Description was successfully changed');
        else
            $message = $this->module->t('The description cannot be changed');
        return [
            'success' => $result > 0,
            'message' => $message,
            'title'   => $this->module->t('Description'),
            'type'    => $result > 0 ? 'accept' : 'error'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMessage(int $result): array
    {
        if ($result > 0)
            $message = $this->module->t('Template description successfully deleted');
        else
            $message = $this->module->t('Unable to delete template description');
        return [
            'success' => $result > 0,
            'message' => $message,
            'title'   => Gm::t(BACKEND, 'Deletion'),
            'type'    => $result > 0 ? 'accept' : 'error'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name'   => $this->module->t('Name'),
            'view'   => $this->module->t('View in code'),
            'type'   => $this->module->t('Type'),
            'use'    => $this->module->t('Use'),
            'component' => $this->module->t('Template affiliation') // Принадлежность шаблона
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'type'          => 'type', // вид шаблона
            'name'          => 'name', // название
            'description'   => 'description', // описание
            'view'          => 'view', // название в коде
            'locale'        => 'locale', // слаг языка
            'language'      => 'language', // название языка шаблона
            'use'           => 'use', // назначение
            'component'     => 'component', // идентификатор компонента
            'componentType' => 'componentType', // вид компонента: widget, module, extension
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFileAttributes(string $filename, array $attributes = []): static
    {
        /** @var \Gm\Theme\Info\ViewsInfo $info */
        $info = $this->theme->getViewsInfo();
        if ($info->load($this->themeName)) {
            $attributes = $info->get($filename);
            if ($attributes === null) {
                $attributes = [];
            }
        }
        return parent::getFileAttributes($filename, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave(bool $isInsert): bool
    {
        // вид компонента: widget, module, extension
        if ($this->componentType) {
            $this->component = $this->getUnsafeAttribute($this->componentType . 'Id');
        } else 
            $this->component = '';

        // язык шаблона (т.к. $this->language содержит слаг языка из запроса)
        if ($this->language === 'null') {
            $this->locale = '';
            $this->language = '';
        } else {
            $language = Gm::$app->language->available->getBy($this->language, 'code');
            if ($language === null) {
                $this->locale = '';
                $this->language = '';
            } else {
                $this->locale = $language['locale'];
                $this->language = $language['shortName'] . ' (' . $language['slug'] . ')';
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord(array $columns, Where|Closure|string|array $where = null): false|int
    {
        /** @var \Gm\Theme\Info\ViewsInfo $info */
        $info = $this->theme->getViewsInfo();
        if (!$info->load($this->themeName)) {
            return false;
        }

        $info->set($this->getIdentifier(), $columns);
        return $info->save($this->themeName) === true ? 1 : 0;
    }

    /**
     * {@inheritdoc}
     */
    protected function deleteProcess(): false|int
    {
        if ($this->beforeDelete()) {
            // условие запроса удаления записи
            $this->result = $this->deleteRecord([]);
            // сброс атрибутов записи
            $this->attributes = [];
            $this->oldAttributes = [];
            $this->afterDelete($this->result);
        }
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteRecord(Where|Closure|string|array $where): false|int
    {
        /** @var \Gm\Theme\Info\ViewsInfo $info */
        $info = $this->theme->getViewsInfo();
        if (!$info->load($this->themeName)) {
            return false;
        }

        $info->set($this->getIdentifier(), null);
        return $info->save($this->themeName) === true ? 1 : 0;
    }

    /**
     * Возвращает значение для выпадающего списка видов шаблонов.
     * 
     * @param string|null $itemId Идентификатор элемента выпадающего списка.
     * 
     * @return array|null
     */
    protected function getTypeComboItem($itemId): ?array
    {
        if ($itemId) {
            /** @var \Gm\Theme\Info\ViewsInfo $info */
            $info = Gm::$app->theme->getViewsInfo();
            if ($info) {
                $types = $info->getTypes(true);
                if (isset($types[$itemId])) {
                    return ['value' => $itemId, 'text' => $types[$itemId]];
                }
            }
        }
        return null;
   }

    /**
     * Возвращает значение для выпадающего списка языков шаблонов.
     * 
     * @param string|null $itemId Идентификатор элемента выпадающего списка.
     * 
     * @return array|null
     */
    protected function getLanguageComboItem($itemId): ?array
    {
        if ($itemId) {
            /** @var array|null Язык */
            $language = Gm::$app->language->available->getBy($itemId, 'locale');
            if ($language) {
                return [
                    'value' => $itemId,
                    'text'  => $language['shortName'] . ' (' . $language['slug'] . ')'
                ];
            }
        }
        return null;
   }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        parent::processing();

        /** @var array|null $item Вид шаблона */
        $item = $this->getTypeComboItem($this->type);
        if ($item) {
            $this->type = [
                'type'  => 'combobox',
                'value' => $item['value'],
                'text'  => $item['text']
            ];
        }

        /** @var array|null $item Язык шаблона */
        $item = $this->getLanguageComboItem($this->locale);
        if ($item) {
            $this->language = [
                'type'  => 'combobox',
                'value' => $item['value'],
                'text'  => $item['text']
            ];
        }

        // вид компонента: widget, module, extension
        if ($this->componentType) {
            $this->{$this->componentType . 'Id'} = $this->component;
        } else 
            $this->component = '';
    }
}
