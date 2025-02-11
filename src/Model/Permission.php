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
 * Модель прав доступа к файлу или директории шаблона.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Permission extends FileModel
{
    /**
     * {@inheritdoc}
     */
    public function saveMessage(bool $isInsert, int $result): array
    {
        if ($result > 0) {
            $message = $this->module->t('Access permissions set successfully');
            $type = 'accept';
        } else {
            $message = $this->module->t('Unable to set permissions on the specified resource');
            $type = 'error';
        }
        return [
            'success'  => $result > 0,
            'message'  => $message,
            'title'    => $this->module->t('Access permissions'),
            'type'     => $type
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'pmd' => $this->module->t('Numerical value')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            'checkEmpty' => [['pmd'], 'notEmpty']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'pmd'  => 'permissionDigit',
            'name' => 'name'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        /** @var \Gm\Panel\Http\Response $response */
        $response = $this->response();

        if ($this->pmd) {
            $permissions = Filesystem::permissionsToArray(intval($this->pmd, 8));
            // права владельца
            if ($permissions['owner']['r'])
                $response->meta->cmdComponent('form-prm-or', 'setValue', [true]);
            if ($permissions['owner']['w'])
                $response->meta->cmdComponent('form-prm-ow', 'setValue', [true]);
            if ($permissions['owner']['x'])
                $response->meta->cmdComponent('form-prm-ox', 'setValue', [true]);
            // групповые права
            if ($permissions['group']['r'])
                $response->meta->cmdComponent('form-prm-gr', 'setValue', [true]);
            if ($permissions['group']['w'])
                $response->meta->cmdComponent('form-prm-gw', 'setValue', [true]);
            if ($permissions['group']['x'])
                $response->meta->cmdComponent('form-prm-gx', 'setValue', [true]);
            // публичные права
            if ($permissions['world']['r'])
                $response->meta->cmdComponent('form-prm-wr', 'setValue', [true]);
            if ($permissions['world']['w'])
                $response->meta->cmdComponent('form-prm-ww', 'setValue', [true]);
            if ($permissions['world']['x'])
                $response->meta->cmdComponent('form-prm-wx', 'setValue', [true]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFileAttributes(string $filename, array $attributes = []): static
    {
        $filename = $this->viewPath . $filename;
        $info = pathinfo($filename);
        $attributes = [
            'name'            => $info['basename'],
            'permissionDigit' => Filesystem::permissions($filename, true, false)
        ];
        return parent::getFileAttributes($filename, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord(array $columns, Where|Closure|string|array $where = null): false|int
    {
        $filename = $this->viewPath . $this->getIdentifier();
        Filesystem::$throwException = false;
        return Filesystem::chmod($filename, $columns['permissionDigit']) ? 1 : false;
    }
}
