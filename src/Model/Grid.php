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
use Gm\Exception;
use Gm\Filesystem\Filesystem;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Data\Model\TreeGridModel;
use Symfony\Component\Finder\Finder;

/**
 * Модель данных дерева сетки шаблонов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Model
 * @since 1.0
 */
class Grid extends TreeGridModel
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Templates\Module
     */
    public BaseModule $module;

    /**
     * Значки файлов с соответствующем расширением.
     * 
     * @var array
     */
    protected array $extensionIcons = [
        'html'  => 'html',
        'xml'   => 'xml', 
        'json'  => 'json',
        'pjson' => 'json',
        'js'    => 'js',
        'css'   => 'css',
        'php'   => 'php',
        'phtml' => 'php',
        'xaml'  => 'xaml'
    ];

    /**
     * Тема.
     * 
     * @var null|\Gm\Theme\Theme
     */
    protected $theme;

    /**
     * Назначение темы (backend, frontend).
     * 
     * @var null|string
     */
    protected $themeSide;

    /**
     * Имя темы указанной в фильтре.
     * 
     * @var null|string
     */
    protected $themeName;

    /**
     * Конфигурация установленных модулей.
     * 
     * @var null|array
     */
    protected $modules;

    /**
     * Конфигурация установленных расшириений модулей.
     * 
     * @var null|array
     */
    protected $extensions;

    /**
     * Конфигурация установленных виджетов.
     * 
     * @var null|array
     */
    protected $widgets;

    /**
     * Абсолютный путь к шаблонам указанной темы.
     * 
     * @var null|string
     */
    protected $viewPath;

    /**
     * URL-путь к значкам файлов шаблонов.
     * 
     * @var null|string
     */
    protected $iconUrl;

    /**
     * Описание шаблонов темы.
     * 
     * @var null|\Gm\Theme\Info\ViewsInfo
     */
    protected $viewsInfo;

    /**
     * Описание шаблонов темы.
     * 
     * @var null|\Gm\Theme\Info\Translator
     */
    protected $infoTranstalor;

    /**
     * Менеджер обозначений ISO.
     * Для определения локали файла шаблона.
     * 
     * @var null|\Gm\I18n\ISO\ISO
     */
    protected $iso;

    /**
     * Перевод имён видов компонентов.
     * 
     * @var array
     */
    protected array $componentTypesText = []; 

    /**
     * Перевод заголовка в контекстном меню записи.
     * 
     * @var array
     */
    protected array $titlesText = [];

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'primaryKey' => 'id',
            'fields'     => [
                ['name'],
                ['description'],
                ['type'],
                ['typeName'],
                ['path'],
                ['filename'],
                ['view'],
                ['permissions'],
                ['accessTime'],
                ['modifTime'],
                ['locale'],
                ['componentType'],
                ['component'],
                ['use'],
                ['useName'],
                ['exists']
            ],
            'useAudit' => false,
            'filter' => [
                'themeName' => ['operator' => '='],
                'use'       => ['operator' => '='],
                'type'      => ['operator' => '=']
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        // "прямой" фильтр по запросу, где: ["property" => "value",...]
        $this->directFilter = $this->defineDirectFilter();

        // конфигурации установленных модулей
        $this->modules = Gm::$app->modules->getRegistry()->getListInfo(true, false, 'id', false);
        // конфигурации установленных виджетов
        $this->widgets = Gm::$app->widgets->getRegistry()->getListInfo(true, false, 'id', false);
        // конфигурации установленных расширений модулей
        $this->extensions = Gm::$app->extensions->getRegistry()->getListInfo(true, false, 'id', false);

        // менеджер обозначений ISO
        $this->iso = Gm::$app->locale->getISO();

        // определение имени темы и какой стороне она принадлежит
        $theme = Gm::$app->theme->defineThemeFromStr($this->directFilter['themeName'] ?? '', '::', true);
        if ($theme) {
            $this->themeName = $theme['name'];
            $this->themeSide = $theme['side'];
            $this->theme = $theme['theme'];
            if ($this->theme === null) {
                // Невозможно определить имя класса темы
                throw new Exception\CreateObjectException('Unable to determine theme class name.');
            }
            // описание шаблонов темы
            $this->viewsInfo = $this->theme->getViewsInfo();
            $this->infoTranstalor = $this->viewsInfo->getTranslator();
            // абсолютный путь к шаблонам указанной темы
            $this->viewPath = $this->theme->getViewPath($this->themeName);
        }

        // URL-путь к значкам файлов шаблонов
        $this->iconUrl = $this->module->getAssetsUrl() . '/images/extension/';

        // перевод заголовка в контекстном меню записи
        $this->titlesText = [$this->t('directory: %s'), $this->t('file: %s'),];

        $this
            ->on(self::EVENT_BEFORE_DELETE, function ($someRecords, &$canDelete) {
                if ($this->themeName === null) {
                    // всплывающие сообщение
                    $this->response()
                        ->meta
                            ->cmdPopupMsg($this->t('You must specify a subject in the filter'), $this->t('Attention'), 'warning');
                    $canDelete = false;
                }
            })
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']) // всплывающие сообщение
                        ->cmdReloadTreeGrid($this->module->viewId('grid')); // обновить дерево
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                $this->response()
                    ->meta
                        ->cmdReloadTreeGrid($this->module->viewId('grid'), 'root'); // обновить дерево
            });
    }

    /**
     * {@inheritdoc}
     */
    public function defineDirectFilter(): array
    {
       /** @var \Gm\Session\Container $store */
        $store = $this->module->getStorage();
        if ($store->directFilter !== null) {
            $modelName = $this->getModelName();
            // если есть фильтр для конкретной модели данные (т.к. в настройках компонента, может быть несколько списков с фильтрами)
            if (isset($store->directFilter[$modelName])) {
                $filter = $store->directFilter[$modelName];
                if ($filter) {
                    $result = [];
                    foreach ($filter as $field) {
                        $result[$field['property']] = $field['value'];
                    }

                    return $result;
                }
            }
        }
        return [];
    }

    /**
     * Возвращает дерева сформированное из массива записей.
     * 
     * @param array $rows Массив записей.
     * 
     * @return array
     */
    public function buildTreeFromRows(array $rows): array
    {
        $childs = [];
        foreach ($rows as &$row) $childs[$row['parent_id']][] = &$row;
        unset($row);

        foreach ($rows as &$row) { 
            if (isset($childs[$row['item_id']])) {
                $row['leaf'] = false;
                $row['children'] = $childs[$row['item_id']];
            } else
                $row['leaf'] = true;
        }
        return $childs[0] ?? [];
    }

    /**
     * Возвращает ветку узла дерева.
     * 
     * Ветка узла дерева - файл шаблона.
     * 
     * @param array $node Узел с базовыми параметрами.
     * 
     * @return array
     */
    public function fetchLeafNode(array $node): array
    {
        /** @var string $filename Имя файла шаблона с абсолютным путём */
        $filename = $this->viewPath . $node['filename'];

        /** @var null|array $info Информация (описание) файла шаблона */
        $info = $this->viewsInfo->get($node['id']);

        /**
         * Права доступа к файлу (если он существует).
         */
        if (file_exists($filename)) {
            $node['permissions'] = Filesystem::permissions($filename);
            // время доступа
            $fileatime = @fileatime($filename);
            if ($fileatime !== false) {
                $node['accessTime'] = Gm::$app->formatter->toDateTime($fileatime, 'php:Y-m-d H:i:s');
            }
            // время правки
            $filectime = @filectime($filename);
            if ($filectime !== false) {
                $node['modifTime'] = Gm::$app->formatter->toDateTime($filectime, 'php:Y-m-d H:i:s');
            }
        }

        /**
         * Значок шаблона.
         */
        // расширение файла
        $extension = $filename ? pathinfo($filename, PATHINFO_EXTENSION) : 'none';
        $node['icon'] = $this->iconUrl . ($this->extensionIcons[$extension] ?? 'none') . '.svg';
        $node['iconCls'] = ' g-icon-svg_size_16 ';
    
        // если описание файла шаблона отсутсвует
        if (empty($info)) return $node;

        /**
         * Определение вида и названия компонента.
         */
        $componenType = $info['componentType'] ?? ''; // вид компонента
        $componentId = $info['component'] ?? ''; // идентификатор компонента
        if ($componenType) {
            $node['componentType'] = $this->infoTranstalor->translate(ucfirst($componenType));

            if ($componentId) {
                if ($componenType === 'module') {
                    $node['component'] = $this->modules[$componentId]['name'] ?? '';
                } else
                if ($componenType === 'widget') {
                    $node['component'] = $this->widgets[$componentId]['name'] ?? '';
                } else
                if ($componenType === 'extension') {
                    $node['component'] = $this->extensions[$componentId]['name'] ?? '';
                }
            }
        } else
            $node['componentType'] = $this->t('Autonomous');

        /**
         * Вид файла шаблона.
         */
        if (empty($info['type'])) {
            $info['type'] = 'file';
        }
        $node['type'] = $info['type'];
        $node['typeName'] = $this->infoTranstalor->translate(ucfirst($info['type']));

        /** @var string $name Название компонента */
        if (empty($info['name']))
            $name = $node['typeName'];
        else
            $name = $this->infoTranstalor->translate($info['name']);

        /**
         * Описание шаблона.
         */
        if (empty($info['description'])) {
            // если шаблон не принадлежит компоненту
            if (empty($componenType)) {
                if (empty($info['name']))
                    $description = $name;
                else
                    $description = $node['typeName'] . ' "' . $name . '"';
            // если шаблон принадлежит компоненту
            } else {
                if (empty($info['name']))
                    $description = $node['typeName'];
                else
                    $description = $node['component'] . ' "' .  $name . '"';
            }

            // добавления к описанию имени языка
            if ($info['locale']) {
                $description .= ', ' . $info['locale'];
            }
            $node['description'] = $description;
        } else
            $node['description'] = $info['description'];

        /**
         * Назначение файла шаблона.
         */
        if (!empty($info['use'])) {
            $node['use'] = $info['use'];
            $node['useName'] = Gm::t('app', ucfirst($info['use']));
        }

        /**
         * Язык шаблона.
         */
        if (!empty($info['language'])) {
            $node['language'] = $info['language'];
        }

        /**
         * Название в коде.
         */
        if (!empty($info['view'])) {
            $node['view'] = $info['view'];
        }
        return $node;
    }

    /**
     * Возвращает узел дерева.
     * 
     * Узел дерева - каталога шаблонов.
     * 
     * @param array $node Узел с базовыми параметрами.
     * 
     * @return array
     */
    protected function fetchNode(array $node): array
    {
        /** @var string $path Абсолютныый путь к файлам шаблона */
        $path = $this->viewPath . $node['path'];

        /** @var null|array $info Информация (описание) каталога шаблона */
        $info = $this->viewsInfo->get($node['id']);

        // если описание каталога шаблона отсутсвует
        if (empty($info)) return $node;

        /**
         * Права доступа к каталогу (если он существует).
         */
        if (file_exists($path)) {
            $node['permissions'] = Filesystem::permissions($path);
            // время доступа
            $fileatime = @fileatime($path);
            if ($fileatime !== false) {
                $node['accessTime'] = Gm::$app->formatter->toDateTime($fileatime, 'php:Y-m-d H:i:s');
            }
            // время правки
            $filectime = @filectime($path);
            if ($filectime !== false) {
                $node['modifTime'] = Gm::$app->formatter->toDateTime($filectime, 'php:Y-m-d H:i:s');
            }
        }

        /**
         * Вид каталога шаблонов.
         */
        if (empty($info['type'])) {
            $info['type'] = 'folder';
        }
        $node['type'] = $info['type'];
        $node['typeName'] = $this->infoTranstalor->translate(ucfirst($info['type']));

        /** @var string $name Название каталога шаблонов */
        if (empty($info['name']))
            $name = $node['typeName'];
        else
            $name = $this->infoTranstalor->translate($info['name']);

        /**
         * Описание каталога шаблонов.
         */
        if (empty($info['description'])) {
            if (empty($info['name']))
                $node['description'] = $name;
            else
                $node['description'] = $node['typeName'] . ' "' . $name . '"';
        } else
            $node['description'] = $info['description'];
        return $node;
    }

    /**
     * Возвращает все узлы дерева.
     * 
     * @return array
     */
    public function fetchNodes(): array
    {
        $index = 1; // идентификатор узла дерева
        $files = []; // все файлы шаблонов темы

        /** @var string $viewPath Абсолютный путь к шаблонам темы */
        $viewPath  = $this->theme->getPath($this->themeName) . DS . 'views';

        /** @var \Symfony\Component\Finder\Finder $finder  */
        $finder = Finder::create();
        $finder->files()->in($viewPath);
        foreach ($finder as $info) {
            /** @var string $relativePath Относительный путь к файлу шаблона */
            $relativePath = $info->getRelativePath();
            if ($relativePath) {
                $relativePath = '/' . str_replace('\\', '/', $relativePath);
            }

            $filename = $info->getFilename();
            /** @var string $nodeId Уникальный идентификатор узла дерева */
            $nodeId = $relativePath . '/' . $filename;

            /** @var array $leafNode Узел дерева */
            $leafNode = $this->fetchLeafNode([
                'id'             => $nodeId,
                'item_id'        => $index++,
                'name'           => $filename,
                'filename'       => $nodeId,
                'path'           => $relativePath ? $relativePath : '/',
                'popupMenuTitle' => sprintf($this->titlesText[1], $filename),
                'popupMenuItems' => []
            ]);

            // т.к. фильтр ориентирован на описание файла, то так:
            foreach ($this->directFilter as $property => $value) {
                if (isset($leafNode[$property])) {
                    if ($leafNode[$property] !== $value) continue 2;
                }
            }
            $files[] = $leafNode;
        }

        // определение каталогов и указание файлам идент-а каталога
        $dirs = [];
        foreach ($files as &$file) {
            $path = $file['path'];
            // если описание для пути файла еще не создано
            if (!isset($dirs[$path])) {
                $parts = explode('/', trim($path, '/'));
                $parentId = 0;
                $subpath  = '';
                foreach ($parts as $part) {
                    $subpath .= '/' . $part;
                    if (!isset($dirs[$subpath])) {
                        /** @var array $node Узел дерева */
                        $node = $this->fetchNode([
                            'id'             => $subpath,
                            'item_id'        => $index++,
                            'name'           => $part,
                            'filename'       => $path,
                            'path'           => $subpath,
                            'expanded'       => true,
                            'parent_id'      => $parentId,
                            'popupMenuTitle' => sprintf($this->titlesText[0], $part),
                            'popupMenuItems' => [[0, 'disabled'], [1, 'disabled']]
                        ]);
                        $dirs[$subpath] = $node; 
                    }
                    $parentId = $dirs[$subpath]['item_id'];
                }
            }
            // если файл не имеет каталога, то parent_id = 0
            $file['parent_id'] = $path === '/' ? 0 : $dirs[$path]['item_id'];
        }

        // убираем корневой каталог "/" если есть
        unset($dirs['/']);

        if ($files)
            return array_merge($files, array_values($dirs));
        else
            return [];
    }

    /**
     * {@inheritdoc}
     */
    public function selectNodes($parentId = null): array
    {
        $tree = [];
        // если в фильтре выбрана тема и она существует
        if ($this->themeName && $this->theme->exists($this->themeName)) {
            // описание шаблонов темы
            $this->viewsInfo->load($this->themeName);

            $rows = $this->fetchNodes();
            $tree = $this->buildTreeFromRows($rows);
            if ($tree === null)
                $tree = [];
        }
        return $this->afterSelect($tree);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): array
    {
        if ($this->identifier === null) {
            $identifier = Gm::$app->request->getPost('id');
            if ($identifier) {
                $identifier = explode(',', $identifier);
                $items = [];
                foreach ($identifier as $item) {
                    // $filename = Gm::$app->encrypter->decryptString($item);
                    $filename = $item;
                    if (file_exists(!$this->viewPath . $filename)) {
                        throw new Exception\FileNotFoundException($filename);
                    }
                    $items[] = $filename;
                }
                $this->identifier = $items;
            } else
                $this->identifier = [];
        }
        return $this->identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function hasIdentifier(): bool
    {
        $identifier = $this->getIdentifier();
        return !empty($identifier);
    }

    /**
     * Удаляет все узлы дерева.
     * 
     * @return array
     */
    public function deleteAllNodes(): array
    {
        Filesystem::$throwException = false;
        Filesystem::deleteDirectory($this->viewPath, true);

        $missedFiles = $missedDirs = 0;
        // поиск всех файлов
        $finder = Finder::create();
        $finder->files()->in($this->viewPath);
        foreach ($finder as $info) {
            $missedFiles++;
        }
        // поиск всех каталогов
        $finder->directories()->in($this->viewPath);
        foreach ($finder as $info) {
            $missedDirs++;
        }
        // описание шаблонов темы
        $this->viewsInfo->load($this->themeName);
        $this->viewsInfo->clear();
        $this->viewsInfo->save();
        return [
            'files' => [
                'missed' => $missedFiles
            ],
            'dirs' => [
                'missed' => $missedDirs
            ]
        ];
    }

    /**
     * Удаляет выбранные узлы дерева.
     * 
     * @return array
     */
    public function deleteNode(): array
    {
        $items = $this->getIdentifier();
        $selectedDirs = $selectedFiles = [];
        // определить файлы и каталоги по выделенным элементам
        foreach ($items as $item) {
            $filename = $this->viewPath . $item;
            if (is_dir($filename))
                $selectedDirs[$item] = true;
            else
                if (is_file($filename))
                    $selectedFiles[$item] = true;
        }

        $foundDirs = $foundFiles = [];
        foreach ($selectedDirs as $dir => $value) {
            // если в выделенном каталоге были еще выделены подкаталоги, то их пропускаем
            if (isset($foundDirs[$dir])) continue;
            // поиск всех файлов
            $finder = Finder::create();
            $finder->files()->in($this->viewPath . $dir);
            foreach ($finder as $info) {
                $relativePath = $info->getRelativePath();
                if ($relativePath)
                    $path = '/' . str_replace('\\', '/', $relativePath) . '/';
                else
                    $path = '/';
                $filename = $info->getFilename();
                $foundFiles[$dir . $path . $filename] = true;
            }
            // поиск всех каталогов
            $foundDirs[$dir] = true;
            $finder->directories()->in($this->viewPath . $dir);
            foreach ($finder as $info) {
                $relativePath = $info->getRelativePath();
                if ($relativePath)
                    $path = '/' . str_replace('\\', '/', $relativePath) . '/';
                else
                    $path = '/';
                $filename = $info->getFilename();
                $foundDirs[$dir . $path . $filename] = true;
            }
        }

        // описание шаблонов темы
        $this->viewsInfo->load($this->themeName);
        // удаление всех найденых файлов в каталогах
        $foundFiles  = array_merge($selectedFiles, $foundFiles);
        $missedFiles = [];
        foreach ($foundFiles as $name => $value) {
            if (!@unlink($this->viewPath . $name)) {
                $missedFiles[] = $name;
            }
            // удаляем описание шаблона
            $this->viewsInfo->set($name, null);
        }
        // удаление всех найденых каталогов
        $missedDirs  = [];
        Filesystem::$throwException = false;
        foreach ($foundDirs as $name => $value) {
            if (!file_exists($this->viewPath . $name)) {
                continue;
            }
            if (!Filesystem::deleteDirectory($this->viewPath . $name))
                $missedDirs[] = $name;
            
        }
        $this->viewsInfo->save($this->themeName);
        return [
            'files' => [
                'count'  => sizeof($foundFiles),
                'missed' => sizeof($missedFiles)
            ],
            'dirs' => [
                'count'  => sizeof($foundDirs),
                'missed' => sizeof($missedDirs)
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAll(string $tableName = null): false|int
    {
        $result = false;
        if ($this->beforeDelete(false)) {
            $result = $this->deleteAllNodes();
            $this->afterDelete(false, $result);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(): false|int
    {
        $result = false;
        if ($this->beforeDelete()) {
            $result = $this->deleteNode();
            $this->afterDelete(true, $result);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMessage(bool $someRecords, int $result): array
    {
        $type    = 'accept';
        $message = '';
        $success = true;
        // удаление выбранных файлов и каталогов
        if ($someRecords) {
                $files = &$result['files'];
                // если файлы найдены для удаления
                if ($files['count'] > 0) {
                    // файлы удалены частично
                    if ($files['missed'] > 0) {
                        $type    = 'warning';
                        $success = false;
                        if ($files['count'] == $files['missed']) {
                            $type = 'error';
                            $message = Gm::t(BACKEND, 'Unable to delete files');
                        } else {
                            $message = Gm::t(
                                BACKEND,
                                'Files have been partially deleted, {nDeleted} deleted, {nSkipped} {skipped, plural, =1{file} other{files}} skipped',
                                [
                                    'deleted'  => $files['count'] - $files['missed'],
                                    'nDeleted' => $files['count'] - $files['missed'],
                                    'skipped'  => $files['missed'],
                                    'nSkipped' => $files['missed']
                                ]
                            );
                        }
                    // файлы удалены полностью
                    } else {
                        $message = Gm::t(
                            BACKEND,
                            'Successfully deleted {N} {n, plural, =1{file} other{files}}',
                            [
                                'N' => $files['count'],
                                'n' => $files['count']
                            ]
                        );
                    }
                }
                $dirs = &$result['dirs'];
                // если каталоги найдены для удаления
                if ($dirs['count'] > 0) {
                    if (strlen($message) > 0)
                        $message .= '<br>';
                    // каталоги удалены частично
                    if ($dirs['missed'] > 0) {
                        $type    = 'warning';
                        $success = false;
                        if ($dirs['count'] == $dirs['missed']) {
                            $type = 'error';
                            $message .= Gm::t(BACKEND, 'Unable to delete directories');
                        } else {
                            $message .= Gm::t(
                                BACKEND,
                                'Directories have been partially deleted, {nDeleted} deleted, {nSkipped} {skipped, plural, =1{directory} other{directories}} skipped',
                                [
                                    'deleted'  => $dirs['count'] - $dirs['missed'],
                                    'nDeleted' => $dirs['count'] - $dirs['missed'],
                                    'skipped'  => $dirs['missed'],
                                    'nSkipped' => $dirs['missed']
                                ]
                            );
                        }
                    // каталоги удалены полностью
                    } else {
                        $message .= Gm::t(
                            BACKEND,
                            'Successfully deleted {N} {n, plural, =1{directory} other{directories}}',
                            [
                                'N' => $dirs['count'],
                                'n' => $dirs['count']
                            ]
                        );
                    }
                }
        // файлов и каталогов
        } else {
            $files = &$result['files'];
            $dirs  = &$result['dirs'];
            if ($files['missed'] == 0 && $dirs['missed'] == 0) {
                $message = Gm::t(BACKEND, 'Files and directories deleted successfully');
            } else {
                $type    = 'warning';
                $success = false;
                // каталоги удалены частично
                if ($dirs['missed'] > 0) {
                    $message = Gm::t(
                        BACKEND,
                        'Directories have been partially deleted, {N} {n, plural, =1{directory} other{directories}} skipped',
                        [
                            'N' => $dirs['missed'],
                            'n' => $dirs['missed']
                        ]
                    );
                }
                // файлы удалены частично
                if ($files['missed'] > 0) {
                    $message = Gm::t(
                        BACKEND,
                        'Files have been partially deleted, {N} {n, plural, =1{file} other{files}} skipped',
                        [
                            'N' => $files['missed'],
                            'n' => $files['missed']
                        ]
                    );
                }
            }
        }
        return [
            'success'  => $success, // успех удаления записей
            'message'  => $message, // сообщение
            'title'    => Gm::t(BACKEND, 'Deletion'), // загаловок сообщения
            'type'     => $type // тип сообщения
        ];
    }
}
