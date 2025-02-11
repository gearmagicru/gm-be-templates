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
use Gm\Panel\Data\Model\Combo\ComboModel;

/**
 * Модель данных выпадающего списка видов шаблонов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class TypeCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $rows = [];
        /** @var \Gm\Theme\Info\ViewsInfo $info */
        $info = Gm::$app->theme->getViewsInfo();
        if ($info) {
            $types = $info->getTypes(true);
            foreach ($types as $type => $typeName) {
                $rows[] = [$type, $typeName];
            }
        }

        return [
            'total' => sizeof($rows),
            'rows'  => $rows
        ];
    }
}
