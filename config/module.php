<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c] 2015 Этот файл является частью модуля веб-приложения GearMagic. Web-студия
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'   => 'text-%s.php'
            ],
            'description' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'   => 'description-%s.php',
                // локали для создания описания шаблонов
                'locales' => ['ru_RU', 'en_GB']
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: view, read, add, edit, delete, clear)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Grid'        => ['data', 'view', 'delete', 'clear', 'filter'], // сетка шаблонов
                'Trigger'     => ['combo'], // выпадающий список
                'Search'      => ['data', 'view'], // поиск
                'Generate'    => ['view', 'add'], // создание описания шаблона
                'Copy'        => ['view', 'add'], // создание копии шаблона
                'Permission'  => ['data', 'view', 'update'], // права доступа к файлу шаблона
                'Rename'      => ['data', 'view', 'update'], // переименование файла шаблона
                'Description' => ['data', 'view', 'update', 'delete'], // редактирование описания шаблона
                'Text'        => ['data', 'view', 'update'] // редактирование текста шаблона
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Просмотр" (view)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Grid'        => ['data', 'view', 'filter'], // сетка шаблонов
                'Trigger'     => ['combo'], // выпадающий список
                'Search'      => ['data', 'view'], // поиск
                'Permission'  => ['data', 'view'], // права доступа к файлу шаблона
                'Description' => ['data', 'view'], // редактирование описания шаблона
                'Text'        => ['data', 'view'] // редактирование текста шаблона
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Чтение" (read)
            'allow',
            'permission'  => 'read',
            'controllers' => [
                'Grid'        => ['data', 'filter'], // сетка шаблонов
                'Trigger'     => ['combo'], // выпадающий список
                'Description' => ['data'], // редактирование описания шаблона
                'Text'        => ['data'] // редактирование текста шаблона
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Добавление" (add)
            'allow',
            'permission'  => 'add',
            'controllers' => [
                'Generate' => ['add'], // создание описания шаблона
                'Copy'     => ['add'], // создание копии шаблона
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Изменение" (edit)
            'allow',
            'permission'  => 'edit',
            'controllers' => [
                'Permission'  => ['update'], // права доступа к файлу шаблона
                'Rename'      => ['update'], // переименование файла шаблона
                'Description' => ['update'], // редактирование описания шаблона
                'Text'        => ['update'] // редактирование текста шаблона
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Удаление" (delete)
            'allow',
            'permission'  => 'delete',
            'controllers' => [
                'Grid'        => ['delete'],
                'Description' => ['delete'] // редактирование описания шаблона
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Очистка" (clear)
            'allow',
            'permission'  => 'clear',
            'controllers' => [
                'Grid' => ['clear'],
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'viewManager' => [
        'id'          => 'gm-template-{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
            'permission'  => '/permission.json', // права доступа к файлу шаблона
            'rename'      => '/rename.json', // переименование файла шаблона
            'text'        => '/text.json', // редактирование текста шаблона
            'description' => '/description.json', // редактирование описания шаблона
            'generate'    => '/generate.json', // создание описания шаблона
            'copy'        => '/copy.json' // создание копии шаблона
        ]
    ]
];
