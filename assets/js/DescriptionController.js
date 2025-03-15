/*!
 * Контроллер представления виджета формы (описание шаблона).
 * Модуль "Шаблоны".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.templates.DescriptionController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-be-templates-description',

    /**
     * Выбор принадлежности шаблона.
     * @param {Ext.form.field.Checkbox} me
     * @param {Boolean} value
     */
    onCheckComponent: function (me, newValue, oldValue, eOpts) {
        if (me.checked) {
            let prefix = 'gm-templates__';
            Ext.getCmp(prefix + 'modules').hide();
            Ext.getCmp(prefix + 'extensions').hide();
            Ext.getCmp(prefix + 'widgets').hide();
            Ext.getCmp(prefix + 'plugins').hide();
            if (me.inputValue.length > 0) {
                Ext.getCmp(prefix + me.inputValue + 's').show();
            }
        }
    }
});