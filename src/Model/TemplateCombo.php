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
use Gm\Theme\Info\ViewsInfo;
use Gm\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка шаблонов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class TemplateCombo extends ComboModel
{
    /**
     * Вид шаблона.
     * 
     * @see \Gm\Theme\Info\ViewsInfo::$type
     * 
     * @var string
     */
    protected string $type = 'page';

    /**
     * Cторона BACKEND, FRONTEND.
     * 
     * @var string|null
     */
    protected ?string $side;

    /**
     * Параметр, значение которого отображается в выпадающем списке.
     * 
     * Параметр может иметь значения: desc, view, name, filename.
     * 
     * @var string
     */
    protected string $display = 'desc';

    /**
     * Описание шаблонов темы.
     *
     * @var ViewsInfo
     */
    protected ViewsInfo $info;

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // вид шаблона
        $this->type = Gm::$app->request->getQuery('type', $this->type);
        $this->side = Gm::$app->request->getQuery('side');
        // сторона темы
        if ($this->side === FRONTEND) {
            $theme = 'frontendTheme';
        } else
        if ($this->side === BACKEND) {
            $theme = 'backendTheme';
        // текущая тема
        } else 
            $theme = 'theme';
        /** @var ViewsInfo $info */
        $this->info = Gm::$app->{$theme}->getViewsInfo();
        // отображение в выпадающем списке
        $this->display =  Gm::$app->request->getQuery('display', $this->display);

    }

    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        /** @var array $items Описание шаблонов */
        $items = $this->info->find(['type' => 'page'], true);

        /** @var array $rows */
        $rows = [];
        foreach ($items as $id => $item) {
            // если поиск 
            if ($this->search) {
                if (mb_stripos($item['description'], $this->search) === false) continue;
            }
            $rows[] = [$id, $item[$this->display]];
        }
        return [
            'total' => sizeof($rows),
            'rows'  => $rows
        ];
    }
}
