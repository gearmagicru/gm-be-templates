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
 * Модель данных выпадающего списка доступных языков.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class LanguageCombo extends ComboModel
{
    /**
     * {@inheritdoc}
     */
    public function getRows(): array
    {
        $rows = [['id' => 'null', 'name' => $this->t('none')]];
        $languages = Gm::$app->language->available->getAll();
        foreach ($languages as $locale => $language) {
            $rows[] = ['id' => $locale, 'name' => $language['shortName'] . ' (' . $language['tag'] . ')'];
        }
        return [
            'total' => sizeof($rows),
            'rows'  => $rows
        ];
    }
}
