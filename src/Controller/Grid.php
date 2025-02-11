<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Templates\Controller;

use Gm;
use Gm\Panel\Http\Response;
use Gm\Panel\Helper\ExtCombo;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Panel\Widget\TabTreeGrid;
use Gm\Panel\Helper\ExtGridTree as ExtGrid;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;
use Gm\Panel\Controller\TreeGridController;

/**
 * Контроллер дерева списка шаблонов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Templates\Controller
 * @since 1.0
 */
class Grid extends TreeGridController
{
    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabTreeGrid
    {
        /** @var string $dateTimeFormat Формат даты и времени */
        $dateTimeFormat = Gm::$app->formatter->formatWithoutPrefix('dateTimeFormat');

       /** @var TabTreeGrid $tab Сетка данных в виде дерева (Gm.view.grid.Tree Gm JS) */
        $tab = parent::createWidget();

        // столбцы (Gm.view.grid.Tree.columns GmJS)
        $tab->treeGrid->columns = [
            ExtGrid::columnAction(),
            [
                'xtype'     => 'treecolumn',
                'text'      => ExtGrid::columnInfoIcon($this->t('Name')),
                'cellTip'   => HtmlGrid::tags([
                     HtmlGrid::header('{name}'),
                     HtmlGrid::fieldLabel($this->t('Description'), '{description}'),
                     HtmlGrid::fieldLabel($this->t('Path / filename'), '{filename}'),
                     HtmlGrid::fieldLabel($this->t('Permissions'), '{permissions}'),
                     HtmlGrid::fieldLabel($this->t('Accessed time'), '{accessTime:date("' . $dateTimeFormat . '")}'),
                     HtmlGrid::fieldLabel($this->t('Modified time'), '{modifTime:date("' . $dateTimeFormat . '")}'),
                     HtmlGrid::fieldLabel($this->t('Belongs to'), '{componentType}'),
                     HtmlGrid::fieldLabel($this->t('Belongs to component'), '{component}'),
                     HtmlGrid::fieldLabel($this->t('Use'), '{useName}'),
                     HtmlGrid::fieldLabel($this->t('Language'), '{language}')
                ]),
                'dataIndex' => 'name',
                'filter'    => ['type' => 'string'],
                'width'     => 300
            ],
            [
                'text'      => $this->t('Description'),
                'dataIndex' => 'description',
                'cellTip'   => '{description}',
                'filter'    => ['type' => 'string'],
                
                'hidden'    => true,
                'width'     => 250
            ],
            [
                'text'      => $this->t('Type'),
                'dataIndex' => 'typeName',
                'filter'    => ['type' => 'string'],
                'width'     => 130
            ],
            [
                'text'      => $this->t('Path / filename'),
                'dataIndex' => 'filename',
                'cellTip'   => '{filename}',
                'filter'    => ['type' => 'string'],
                'width'     => 200
            ],
            [
                'text'      => $this->t('View in code'),
                'dataIndex' => 'view',
                'cellTip'   => '{view}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'width'     => 200
            ],
            [
                'text'      => '#Permissions',
                'dataIndex' => 'permissions',
                'cellTip'   => '{permissions}',
                'filter'    => ['type' => 'string'],
                'width'     => 125
            ],
            [
                'xtype'     => 'datecolumn',
                'text'      => '#Accessed time',
                'tooltip'   => '#File last accessed time',
                'dataIndex' => 'accessTime',
                'filter'    => ['type' => 'date', 'dateFormat' => 'Y-m-d'],
                'format'    => $dateTimeFormat,
                'hidden'    => true,
                'width'     => 145
            ],
            [
                'xtype'     => 'datecolumn',
                'text'      => '#Modified time',
                'tooltip'   => '#File last modified time',
                'dataIndex' => 'modifTime',
                'filter'    => ['type' => 'date', 'dateFormat' => 'Y-m-d'],
                'format'    => $dateTimeFormat,
                'hidden'    => true,
                'width'     => 145
            ],
            [
                'text'      => $this->t('Belongs to'),
                'dataIndex' => 'componentType',
                'cellTip'   => '{componentType}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'width'     => 140
            ],
            [
                'text'      => $this->t('Belongs to component'),
                'tooltip'   => $this->t('Belongs to component'),
                'dataIndex' => 'component',
                'cellTip'   => '{component}',
                'filter'    => ['type' => 'string'],
                'hidden'    => true,
                'width'     => 140
            ],
            [
                'text'      => '#Use',
                'dataIndex' => 'useName',
                'filter'    => ['type' => 'string'],
                'cellTip'   => '{useName}',
                'width'     => 110
            ],
            [
                'text'      => '#Language',
                'dataIndex' => 'language',
                'cellTip'   => '{language}',
                'filter'    => ['type' => 'string'],
                'width'     => 130
            ]
        ];

        // панель инструментов (Gm.view.grid.Tree.tbar GmJS)
        $tab->treeGrid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit' => [
                    'items' => [
                        // инструмент "Удалить" (удаление выделенных каталогов и файлов (шаблонов))
                        'delete' => [
                            'iconCls'       => 'g-icon-svg gm-templates__icon-delete',
                            'tooltip'       => $this->t('Delete selected directories and files (templates) with their description'),
                            'msgConfirm'    => $this->t('Are you sure you want to delete selected elements?'),
                            'msgMustSelect' => $this->t('You need to select elements')
                        ],
                        // инструмент "Очистить"
                        'cleanup' => [
                            'tooltip'    => '#Delete all directories and files (templates) with their description',
                            'msgConfirm' => '#Do you really want to delete all directories and files (templates) of the theme?',
                        ],
                        '-',
                        'edit',
                        'select',
                        '-',
                        'refresh',
                        '-',
                        // инструмент "Выполнить"
                        ExtGrid::button([
                            'xtype'    => 'g-gridbutton-split',
                            'text'     => '#Perform',
                            'tooltip'  => '#Perform an action on template elements',
                            'iconCls'  => 'g-icon-svg gm-templates__icon-execute',
                            'minWidth' => 73,
                            'menu'     => [
                                'mouseLeaveDelay' => 0,
                                'items' => [
                                    [
                                        'text'        => '#Create a description of templates',
                                        'iconCls'     => 'gm-templates__icon-create-desc_small', 
                                        'handlerArgs' => [
                                              'route' => Gm::alias('@match', '/generate/view')
                                          ],
                                          'handler' => 'loadWidget'
                                    ],
                                    [
                                        'text'        => '#Create copies of module templates',
                                        'iconCls'     => 'gm-templates__icon-create-copy_small',
                                        'handlerArgs' => [
                                              'route' => Gm::alias('@match', '/copy/view')
                                          ],
                                          'handler' => 'loadWidget'
                                    ]
                                ]
                            ]
                        ])
                    ]
                ],
                'columns',
                'search' => [
                    'items' => [
                        'help',
                        // инструмент "Фильтр"
                        'filter' => ExtGrid::popupFilter([
                            ExtCombo::trigger(
                                '#Theme', 'themeName', 'themes', false, ['marketplace/themes/trigger/combo', BACKEND],
                                [
                                    'editable'   => false,
                                    'listConfig' => [
                                        'itemTpl' => 
                                            '<div class="g-boundlist-item gm-templates__boundlist-theme" data-qtip="{name}">' .
                                                '<img class="gm-templates__boundlist-theme-img" src="{thumb}">' .
                                                '<div class="gm-templates__boundlist-theme-name">{name} {subname}</div>' .
                                                '<div class="gm-templates__boundlist-theme-desc">{description}</div>' .
                                                '<div class="gm-templates__boundlist-theme-status">{status}</div>' .
                                            '</div>'
                                    ]
                                ]
                            ),
                            [
                                'xtype'    => 'fieldset',
                                'title'    => $this->t('Template'),
                                'defaults' => [
                                    'labelWidth' => 135,
                                    'labelAlign' => 'right',
                                    'anchor'     => '100%'
                                ],
                                'items' => [
                                    ExtCombo::viewTypes('#Type', 'type'),
                                    ExtCombo::side('#Use', 'use', true)
                                ]
                            ]
                        ], [
                            'defaults' => ['labelWidth' => 40]
                        ])
                    ]
                ]
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $tab->treeGrid->popupMenu = [
            'cls'        => 'g-gridcolumn-popupmenu',
            'titleAlign' => 'center',
            'items'      => [
                [
                    'text'        => '#Edit template description',
                    'iconCls'     => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/description/view/?f={id}'),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ],
                [
                    'text'        => '#Edit template',
                    'iconCls'     => 'g-icon-svg gm-templates__icon-text g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/text/view/?f={id}'),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ],
                '-',
                [
                    'text'        => '#Rename',
                    'iconCls'     => 'g-icon-svg gm-templates__icon-rename g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/rename/view/?f={id}'),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ],
                '-',
                [
                    'text' => '#Permissions',
                    'iconCls'     => 'g-icon-svg gm-templates__icon-shield g-icon-m_color_default',
                    'handlerArgs' => [
                          'route'   => Gm::alias('@match', '/permission/view/?f={id}'),
                          'pattern' => 'grid.popupMenu.activeRecord'
                      ],
                      'handler' => 'loadWidget'
                ]
            ]
        ];

        // поле аудита записи
        $tab->treeGrid->logField = 'name';
        // количество строк в сетке
        $tab->treeGrid->store->pageSize = 1000;
        $tab->treeGrid->store->proxy['reader']['rootProperty'] = 'children';
        // для локального поиска и сортировки записей
        $tab->treeGrid->store->filterer = 'bottomup';
        $tab->treeGrid->store->remoteSort = false;
        $tab->treeGrid->store->remoteFilter = false;
        $tab->treeGrid->store->fields = [
            ['name' => 'name', 'type' => 'string'],
            ['name' => 'description', 'type' => 'string'],
            ['name' => 'type', 'type' => 'string'],
            ['name' => 'typeName', 'type' => 'string'],
            ['name' => 'path', 'type' => 'string'],
            ['name' => 'filename', 'type' => 'string'],
            ['name' => 'permissions', 'type' => 'string'],
            ['name' => 'accessTime', 'type' => 'date'],
            ['name' => 'modifTime', 'type' => 'date'],
            ['name' => 'componentType', 'type' => 'string'],
            ['name' => 'use', 'type' => 'string'],
            ['name' => 'useName', 'type' => 'string'],
            ['name' => 'view', 'type' => 'string'],
            ['name' => 'language', 'type' => 'string'],
            ['name' => 'exists', 'type' => 'bool']
        ];
        $tab->treeGrid->viewConfig = ['blockRefresh' => false];
        // плагины сетки
        $tab->treeGrid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->treeGrid->bodyCls = 'g-grid_background';
        $tab->treeGrid->columnLines  = true;
        $tab->treeGrid->rowLines     = true;
        $tab->treeGrid->lines        = true;
        $tab->treeGrid->singleExpand = false;
        // удалить пагинатор сетки
        unset($tab->treeGrid->pagingtoolbar);

        // панель навигации (Gm.view.navigator.Info GmJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::header('{name}'),
            ['div', '{description}', ['align' => 'center']],
            ['fieldset',
                [
                    HtmlNav::fieldLabel($this->t('Type'), '{typeName}'),
                    HtmlNav::fieldLabel($this->t('Path / filename'), '{filename}'),
                    HtmlNav::fieldLabel($this->t('Permissions'), '{permissions}'),
                    HtmlNav::fieldLabel($this->t('Accessed time'), '{accessTime:date("' . $dateTimeFormat . '")}'),
                    HtmlNav::fieldLabel($this->t('Modified time'), '{modifTime:date("' . $dateTimeFormat . '")}'),
                    HtmlNav::fieldLabel($this->t('Belongs to'), '{componentType}'),
                    HtmlNav::fieldLabel($this->t('Belongs to component'), '{component}'),
                    HtmlNav::fieldLabel($this->t('Use'), '{useName}'),
                    HtmlNav::fieldLabel($this->t('Language'), '{language}')
                ]
            ],
            ['fieldset',
                [
                    HtmlGrid::tpl(
                        [
                            HtmlNav::widgetButton(
                                $this->t('Edit template description'),
                                ['route' => Gm::alias('@match', '/description/view/?f={id}'), 'long' => true],
                                ['title' => $this->t('Edit template description')]
                            ),
                            HtmlNav::widgetButton(
                                $this->t('Edit template'),
                                ['route' => Gm::alias('@match', '/text/view/?f={id}'), 'long' => true],
                                ['title' => $this->t('Edit template')]
                            )

                        ],
                        ['if' => 'leaf']
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Rename'),
                        ['route' => Gm::alias('@match', '/rename/view/?f={id}'), 'long' => true],
                        ['title' => $this->t('Rename')]
                    ),
                    HtmlNav::widgetButton(
                        $this->t('Permissions'),
                        ['route' => Gm::alias('@match', '/permission/view/?f={id}'), 'long' => true],
                        ['title' => $this->t('Permissions')]
                    )
                ]
            ]
        ]);

        $tab->addCss('/grid.css');
        return $tab;
    }

    /**
     * Действие "view" выводит интерфейс дерева.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Gm\Http\Request $request */
        $request = Gm::$app->request;

        /** @var TabTreeGrid $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании модели представления
        if ($widget === false) {
            return $response;
        }
        /** @var \Gm\Panel\Data\Model\TreeGridModel $model */
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }
        // сброс "dropdown" фильтра таблицы
        $store = $this->module->getStorage();
        $store->directFilter = null;

        $themeName = $request->get('themeName');
        if ($themeName) {
            $store->directFilter = [
                $model->getModelName() => [
                    [
                        'value' => $themeName,
                        'property' => 'themeName',
                        'operator' => '=',
                        'where' => null
                    ]
                ]
            ];
   
        }
        
        // если в конфигурации модели данных указан аудит записей "useAudit" и есть
        // разрешение на просмотр аудита записей, то в вывод списка добавляются соответствующие столбцы
        $manager = $model->getDataManager();
        if ($manager->useAudit && $manager->canViewAudit()) {
            $widget->treeGrid->addAuditColumns();
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function dataAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Panel\Data\Model\TreeGridModel $model модель данных*/
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }
        // получение всех узлов дерева
        $tree = $model->getTreeNodes();
        $response->meta->contentProperty = 'children';
        $response->meta->total = $tree['total'];
        $response->meta->isRootNode = true;
        return $response->setContent($tree['nodes']);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var \Gm\Panel\Data\Model\TreeGridModel $model модель данных*/
        $model = $this->getModel($this->defaultModel);
        if ($model === false) {
            $response
                ->meta->error(Gm::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }
        // удаление записей
        if ($model->deleteAll() === false) {
            $response
                ->meta->error(Gm::t(BACKEND, 'Could not delete record'));
            return $response;
        }
        return $response;
    }
}
