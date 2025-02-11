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
 * Модель изменения имени файла или директории.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Rename extends FileModel
{
    /**
     * {@inheritdoc}
     */
    public function saveMessage(bool $isInsert, int $result): array
    {
        if ($result > 0) {
            $message = $this->module->t('Name has been successfully changed');
        } else {
            $message = $this->module->t('The name of the specified resource cannot be changed');
        }
        return [
            'success'  => $result > 0,
            'message'  => $message,
            'title'    => $this->module->t('Renaming'),
            'type'     => $result > 0 ? 'accept' : 'error'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'name' => $this->module->t('New name')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validationRules(): array
    {
        return [
            'checkEmpty' => [['name'], 'notEmpty']
        ];
    }

    protected function extensionValidate($extension)
    {
        $allowable = ['php', 'phtml', 'html', 'xml', 'json', 'pjson'];
        return in_array($extension, $allowable);
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            $filename = $this->viewPath . $this->getIdentifier();
            $isFile = is_file($filename);

            $this->name = trim($this->name);
            // проверки имени файла
            if ($isFile) {
                if (!preg_match('/^[\w\-. ]+$/', $this->name)) {
                    $this->setError($this->t('Incorrect file name'));
                    return false;
                }
                // должно быть расширение файла
                if (mb_strpos($this->name, '.') === false) {
                    $this->setError($this->t('Incorrect file name'));
                    return false;
                }
                // проверка допустимого расширения
                $info = pathinfo($this->name);
                if (!$this->extensionValidate($info['extension'])) {
                    $this->setError($this->t('Invalid file extension'));
                    return false;
                }
            // проверки имени директории
            } else {
                if (!preg_match('/^[\w\- ]+$/', $this->name)) {
                    $this->setError($this->t('Incorrect directory name'));
                    return false;
                }
            }
            // проверка существования файла или директории
            $info = pathinfo($filename);
            $dirname = $info['dirname'];
            if (file_exists($dirname . DS . $this->name)) {
                $this->setError($this->t($isFile ? 'A file with the same name already exists' : 'A directory with the same name already exists'));
                return false;
            }
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function maskedAttributes(): array
    {
        return [
            'name' => 'name'
        ];
    }

    /**
     * Возвращает новый идентификатор (новое имя ресурса).
     * 
     * @return string
     */
    public function getNewIdentifier()
    {
        return pathinfo($this->getIdentifier(), PATHINFO_DIRNAME) . '/' . $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileAttributes(string $filename, array $attributes = []): static
    {
        $info = pathinfo($filename);
        $attributes = [
            'name' => $info['basename']
        ];
        return parent::getFileAttributes($filename, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function updateRecord(array $columns, Where|Closure|string|array $where = null): false|int
    {
        $identifier = $this->getIdentifier();
        $oldname = $this->viewPath . $identifier;
        $newname = pathinfo($oldname, PATHINFO_DIRNAME) . DS . $this->name;
        
        if (rename($oldname, $newname) === true) {
            $viewsInfo = $this->theme->getViewsInfo();
            if ($viewsInfo->load($this->themeName)) {
                // если есть описание файла или директории
                if ($viewsInfo->has($identifier)) {
                    $viewsInfo->moveKey($identifier, $this->getNewIdentifier());
                    $viewsInfo->save($this->themeName);
                }
            }
            return 1;
        } else
            return false;
    }
}
