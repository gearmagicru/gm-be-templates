<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Шаблоны',
    '{description}' => 'Шаблоны страниц сайта и расширений системы',
    '{permissions}' => [
        'any'    => ['Полный доступ', 'Просмотр и внесение изменений в шаблоны'],
        'view'   => ['Просмотр', 'Просмотр шаблонов'],
        'read'   => ['Чтение', 'Чтение шаблонов'],
        'add'    => ['Добавление', 'Добавление шаблонов'],
        'edit'   => ['Изменение', 'Изменение шаблонов'],
        'delete' => ['Удаление', 'Удаление шаблонов'],
        'clear'  => ['Очистка', 'Удаление всех шаблонов']
    ],

    // Permission
    '{permission.title}' => 'Настройка доступа',
    '{permission.titleTpl}' => 'Настройка доступа для "{name}"',
    // Permission: поля
    'Owner permission' => 'Права владельца',
    'Group permission' => 'Групповые права',
    'World permission' => 'Публичные права',
    'Read' => 'Чтение',
    'Write' => 'Запись',
    'Execution' => 'Выполнение',
    'Numerical value' => 'Числовое значение',
    // Permission: сообщения
    'Access permissions' => 'Права доступа',
    'Access permissions set successfully' => 'Права доступа успешно установлены.',
    'Unable to set permissions on the specified resource' => 'Невозможно установить права доступа на указанный ресурс.',

    // Rename
    '{rename.title}' => 'Переименовать ',
    '{rename.titleTpl}' => 'Переименовать "{name}"',
    // Rename: поля
    'New name' => 'Новое имя',
    // Rename: сообщения
    'Incorrect file name' => 'Неправильно указано имя файл',
    'Incorrect directory name' => 'Неправильно указано имя каталога',
    'Invalid file extension' => 'Недопустимое расширение файла',
    'A file with the same name already exists' => 'Файл с таким именем уже существует',
    'A directory with the same name already exists' => 'Каталог с таким именем уже существует',
    'Renaming' => 'Переименовывание',
    'The name of the specified resource cannot be changed' => 'Невозможно изменить имя указанному ресурсу.',
    'Name has been successfully changed' => 'Имя было успешно изменено.',

    // Text
    '{text.title}' => 'Редактирование шаблона ',
    '{text.titleTpl}' => 'Редактирование шаблона "{name}"',
    // Text: сообщения
    'Template editing' => 'Редактирование шаблона',
    'Unable to save template' => 'Невозможно выполнить сохранение шаблона',
    'Template saved successfully' => 'Шаблон успешно сохранен',

    // Description
    '{description.title}' => 'Редактирование описания ',
    '{description.titleTpl}' => 'Редактирование описания "{name}"',
    // Description: поля
    'Folder' => 'Название каталога',
    'Template affiliation' => 'Шаблон принадлежит',
    'To module' => 'Модулю',
    'To widget' => 'Виджету',
    'To extension' => 'Расширению модуля',
    'Autonomous' => 'Автономный',
    // Description: сообщения
    'if the description is changed, it will be displayed the same for all languages' => 'если описание будет изменено, то оно будет одинаково отображаться для всех языков',
    'Description was successfully changed' => 'Описание было успешно изменено',
    'The description cannot be changed' => 'Невозможно изменить описание',
    'Template description successfully deleted' => 'Описание шаблона успешно удалено',
    'Unable to delete template description' => 'Невозможно выполнить удаление описание шаблона',

    // Generate
    '{generate.title}' => 'Создание описание шаблонов',
    // Generate: поля
    'Type description' => 'Тип описания',
    'description for all templates in the theme directory' => 'описание для всех шаблонов в каталоге темы',
    'descriptions for templates belonging to components' => 'описание только для шаблонов принадлежащих компонентам',
    'description only for templates not belonging to components' => 'описание только для шаблонов не принадлежащих компонентам',
    'remove description for all theme templates' => 'удалить описание для всех шаблонов темы',
    'if the template description file was created earlier, it will be replaced' => 'если файл описания шаблонов был ранее создан, он будет заменён',
    'Create' => 'Создать',
    'Execute' => 'Выполнить',

    // Copy
    '{copy.title}' => 'Создание копии шаблонов',
    // Copy: поля
    'replace existing templates in the theme directory' => 'заменить существующие шаблоны в каталоге темы',
    'add description to copies of templates' => 'добавить описание к копиям шаблонов',
    'when adding a description of templates, the previous description will be replaced' => 'при добавлении описания шаблонов, предыдущие описание будет заменено',
    'only for the frontend' => 'только для Сайта',
    'only for the backend' => 'только для Панели управления',
    'Modules' => 'Модулей',
    'Widgets' => 'Виджетов',
    'Extensions' => 'Расширений модулей',
    'Copies of templates' => 'Копии шаблонов',
    // Copy: сообщения
    'Copying module' => 'Копирование шаблонов',
    'Copies of templates have been successfully created' => 'Копии шаблонов успешно созданы.',
    'Unable to copy templates' => 'Невозможно выполнить копирование шаблонов.',

    // Grid: контекстное меню записи
    'Edit template description' => 'Редактировать описание',
    'Edit template' => 'Редактировать шаблон',
    'Rename' => 'Переименовать',
    'Permissions' => 'Права доступа',
    'Theme' => 'Тема',
    'Template' => 'Шаблон',
    'directory: %s' => 'каталог: %s',
    'file: %s' => 'файл: %s',
    // Grid: панель инструментов
    'Filter' => 'Фильтр',
    'Filtering records in the list' => 'Фильтрация записей',
    'Perform' => 'Выполнить',
    'Perform an action on template elements' => 'Выполнить действие над элементами шаблона',
    'Create a description of templates' => 'Создать описание шаблонов',
    'Create copies of module templates' => 'Создать копии шаблонов',
    'Delete all directories and files (templates) with their description' => 'Удаление всех каталогов и файлов (шаблонов) темы с их описанием',
    'Delete selected directories and files (templates) with their description' => 'Удаление выделенных каталогов и файлов (шаблонов) темы с их описанием',
    'Are you sure you want to delete selected elements?' => 'Вы уверены, что хотите удалить выделенные элемент(ы) - <b>{0}</b> ?',
    'You need to select elements' => 'Вам необходимо выделить элементы.',
    'Do you really want to delete all directories and files (templates) of the theme?' => 'Вы действительно хотите удалить все каталоги и файлы (шаблоны) темы?',
    'active' => 'активна',
    // Grid: столбцы
    'Name' => 'Название',
    'Description' => 'Описание',
    'Type' => 'Вид',
    'Belongs to component' => 'Принадлежит компоненту',
    'File name' => 'Имя файла',
    'Path / filename' => 'Путь / файл',
    'Use' => 'Назначение',
    'Language' => 'Язык',
    'Permissions' => 'Права доступа',
    'Accessed time' => 'Время доступа',
    'View in code' => 'Название в коде',
    'File last accessed time' => 'Время последнего доступа к файлу',
    'Modified time' => 'Время правки',
    'File last modified time' => 'Время последнего изменения файла',
    'The template file is present in the theme' => 'Файл шаблона присутствует в теме',
    'Template file' => 'Файл шаблона',
    'Frontend' => 'Сайт',
    'Backend' => 'Панель управления',
    'Belongs to' => 'Принадлежность',
    // Grid: сообщения
    'You must specify a subject in the filter' => 'Необходимо указать тему в фильтре.',
    'Attention' => 'Внимание',

    // Типы
    'form' => 'Форма',
    'grid' => 'Список',
    'post' => 'Статья',
    'path' => 'Каталог',
    'page' => 'Страница',
    'widget' => 'Виджет',
    'module' => 'Модуль',
    'mail'   => 'Письмо',

    // Сообщения
    'none' => '[ без выбора ]',
    'Yes' => 'Да',
    'No' => 'Нет',
    'main theme' => 'основная тема',
    'Template theme not specified' => 'Не указана тема шаблона',
    'The theme you selected does not exist' => 'Выбранная вами тема не существует',
    'The theme you selected is missing a template description file' => 'В выбранной вами теме отсутствует файл описания шаблонов',
    'Unable to load description file for selected theme' => 'Невозможно загрузить файл описания выбранной темы.',
    'Unable to save description file for selected theme' => 'Невозможно сохранить файл описания выбранной темы.',
    'Adding template description' => 'Добавление описание шаблона',
    'Template description successfully added' => 'Описание шаблона успешно добавлено.',
    'Unable to add template description' => 'Невозможно добавить описание шаблона.',
    'Update template description' => 'Обновление описания шаблона',
    'Template description successfully update' => 'Описание шаблона успешно изменено.',
    'Unable to update template description' => 'Невозможно сохранить описание шаблона.',
    'Template description successfully deleted' => 'Описание шаблона успешно удалено',
    'Unable to delete template description' => 'Невозможно удалить описание шаблона',
    'The module configurator is missing template localization parameters' => 'В конфигураторе модуля отсутствует параметры локализации шаблонов'
];
