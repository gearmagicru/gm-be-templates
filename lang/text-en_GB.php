<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Templates',
    '{description}' => 'Site page templates and system extensions',
    '{permissions}' => [
        'any'    => ['Full access', 'Viewing and editing templates'],
        'read'   => ['Reading', 'Reading templates'],
        'add'    => ['Adding', 'Adding templates'],
        'edit'   => ['Editing', 'Editing templates'],
        'delete' => ['Deleting', 'Deleting templates'],
        'clear'  => ['Clear', 'Deleting all templates']
    ],

    // Permission
    '{permission.title}' => 'Permission settings',
    '{permission.titleTpl}' => 'Permission settings for "{name}"',
    // Permission: поля
    'Owner permission' => 'Owner permission',
    'Group permission' => 'Group permission',
    'World permission' => 'World permission',
    'Read' => 'Read',
    'Write' => 'Write',
    'Execution' => 'Execution',
    'Numerical value' => 'Numerical value',
    // Permission: сообщения
    'Access permissions' => 'Access permissions',
    'Access permissions set successfully' => 'Access permissions set successfully.',
    'Unable to set permissions on the specified resource' => 'Unable to set permissions on the specified resource.',

    // Rename
    '{rename.title}' => 'Rename ',
    '{rename.titleTpl}' => 'Rename "{name}"',
    // Rename: поля
    'New name' => 'New name',
    // Rename: сообщения
    'Incorrect file name' => 'Incorrect file name',
    'Incorrect directory name' => 'Incorrect directory name',
    'Invalid file extension' => 'Invalid file extension',
    'A file with the same name already exists' => 'A file with the same name already exists',
    'A directory with the same name already exists' => 'A directory with the same name already exists',
    'Renaming' => 'Renaming',
    'The name of the specified resource cannot be changed' => 'The name of the specified resource cannot be changed.',
    'Name has been successfully changed' => 'Name has been successfully changed.',

    // Text
    '{text.title}' => 'Edit template ',
    '{text.titleTpl}' => 'Edit template "{name}"',
    // Text: сообщения
    'Template editing' => 'Edit template',
    'Unable to save template' => 'Unable to save template',
    'Template saved successfully' => 'Template saved successfully',

    // Description
    '{description.title}' => 'Edit description ',
    '{description.titleTpl}' => 'Edit description "{name}"',
    // Description: поля
    'Folder' => 'Folder',
    'Template affiliation' => 'Template affiliation',
    'To module' => 'To module',
    'To widget' => 'To widget',
    'To extension' => 'To extension',
    'To plugin' => 'To plugin',
    'Autonomous' => 'Autonomous',
    // Description: сообщения
    'if the description is changed, it will be displayed the same for all languages' => 'if the description is changed, it will be displayed the same for all language',
    'Description was successfully changed' => 'Description was successfully changed',
    'The description cannot be changed' => 'The description cannot be changed',
    'Template description successfully deleted' => 'Template description successfully deleted',
    'Unable to delete template description' => 'Unable to delete template description',

    // Generate
    '{generate.title}' => 'Create description templates',
    // Generate: поля
    'Type description' => 'Type description',
    'description for all templates in the theme directory' => 'description for all templates in the theme directory',
    'descriptions for templates belonging to components' => 'descriptions for templates belonging to components',
    'description only for templates not belonging to components' => 'description only for templates not belonging to components',
    'remove description for all theme templates' => 'remove description for all theme templates',
    'if the template description file was created earlier, it will be replaced' => 'if the template description file was created earlier, it will be replaced',
    'Create' => 'Create',
    'Execute' => 'Execute',

    // Copy
    '{copy.title}' => 'Create a copy of the templates',
    // Copy: поля
    'replace existing templates in the theme directory' => 'replace existing templates in the theme directory',
    'add description to copies of templates' => 'add description to copies of templates',
    'when adding a description of templates, the previous description will be replaced' => 'when adding a description of templates, the previous description will be replaced',
    'only for the frontend' => 'only for the frontend',
    'only for the backend' => 'only for the backend',
    'Modules' => 'Modules',
    'Widgets' => 'Widgets',
    'Extensions' => 'Extensions',
    'Copies of templates' => 'Copies of templates',
    // Copy: сообщения
    'Copying module' => 'Copying module',
    'Copies of templates have been successfully created' => 'Copies of templates have been successfully created.',
    'Unable to copy templates' => 'Unable to copy templates.',

    // Grid: контекстное меню записи
    'Edit template description' => 'Edit template description',
    'Edit template' => 'Edit template',
    'Rename' => 'Rename',
    'Permissions' => 'Permissions',
    'Theme' => 'Theme',
    'Template' => 'Template',
    'directory: %s' => 'directory: %s',
    'file: %s' => 'file: %s',
    // Grid: панель инструментов
    'Filter' => 'Filter',
    'Filtering records in the list' => 'Filtering records in the list',
    'Perform' => 'Perform',
    'Perform an action on template elements' => 'Perform an action on template elements',
    'Create a description of templates' => 'Create a description of templates',
    'Create copies of module templates' => 'Create copies of module templates',
    'Delete all directories and files (templates) with their description' => 'Delete all directories and files (templates) with their description',
    'Delete selected directories and files (templates) with their description' => 'Delete selected directories and files (templates) with their description',
    'Are you sure you want to delete selected elements?' => 'Are you sure you want to delete selected elements - <b>{0}</b> ?',
    'You need to select elements' => 'You need to select elements.',
    'Do you really want to delete all directories and files (templates) of the theme?' => 'Do you really want to delete all directories and files (templates) of the theme?',
    'active' => 'active',
    // Grid: столбцы
    'Name' => 'Name',
    'Description' => 'Description',
    'Type' => 'Type',
    'Belongs to component' => 'Belongs to component',
    'File name' => 'File name',
    'Path / filename' => 'Path / filename',
    'Use' => 'Use',
    'Language' => 'Language',
    'Permissions' => 'Permissions',
    'Accessed time' => 'Accessed time',
    'View in code' => 'View in code',
    'File last accessed time' => 'File last accessed time',
    'Modified time' => 'Modified time',
    'File last modified time' => 'File last modified time',
    'The template file is present in the theme' => 'The template file is present in the theme',
    'Template file' => 'Template file',
    'Frontend' => 'Сайт',
    'Backend' => 'Панель управления',
    'Belongs to' => 'Belongs to',
    // Grid: сообщения
    'You must specify a subject in the filter' => 'You must specify a subject in the filter.',
    'Attention' => 'Attention',

    // Типы
    'form' => 'Form',
    'grid' => 'Grid',
    'post' => 'Post',
    'path' => 'Path',
    'page' => 'Page',
    'widget' => 'Widget',
    'module' => 'Module',
    'mail'   => 'Mail',

    // Сообщения
    'none' => '[ none ]',
    'Yes' => 'Yes',
    'No' => 'No',
    'main theme' => 'main theme',
    'Template theme not specified' => 'Template theme not specified',
    'The theme you selected does not exist' => 'The theme you selected does not exist',
    'The theme you selected is missing a template description file' => 'The theme you selected is missing a template description file',
    'Unable to load description file for selected theme' => 'Unable to load description file for selected theme.',
    'Unable to save description file for selected theme' => 'Unable to save description file for selected theme.',
    'Adding template description' => 'Adding template description',
    'Template description successfully added' => 'Template description successfully added.',
    'Unable to add template description' => 'Unable to add template description.',
    'Update template description' => 'Update template description',
    'Template description successfully update' => 'Template description successfully update.',
    'Unable to update template description' => 'Unable to update template description.',
    'Template description successfully deleted' => 'Template description successfully deleted',
    'Unable to delete template description' => 'Unable to delete template description',
    'The module configurator is missing template localization parameters' => 'The module configurator is missing template localization parameters'
];