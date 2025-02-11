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
use Gm\Filesystem\Filesystem;

/**
 * Модель изменения текст шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Text extends FileModel
{
    /**
     * {@inheritdoc}
     */
    public function saveMessage(bool $isInsert, int $result): array
    {
        if ($result > 0)
            $message = $this->module->t('Template saved successfully');
        else
            $message = $this->module->t('Unable to save template');
        return [
            'success'  => $result > 0,
            'message'  => $message,
            'title'    => $this->module->t('Template editing'),
            'type'     => $result > 0 ? 'accept' : 'error'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'text' => $this->module->t('Template text')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            'checkEmpty' => [['text'], 'notEmpty']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'text' => 'text',
            'name' => 'name'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFileAttributes(string $filename, array $attributes = []): static
    {
        $attributes = [
            'text' => Filesystem::get($this->viewPath . $filename),
            'name' => pathinfo($filename, PATHINFO_BASENAME)
        ];
        return parent::getFileAttributes($filename, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord(array $columns, Where|Closure|string|array|null $where = null): false|int
    {
        $filename = $this->viewPath . $this->getIdentifier();
        return Filesystem::put($filename, $columns['text']);
    }
}
