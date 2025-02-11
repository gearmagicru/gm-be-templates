/*!
 * Контроллер представления виджета формы (права доступа).
 * Модуль "Шаблоны".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.templates.PermissionController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-be-templates-permission',

    /**
     * Срабатывает при клике на один из флагов прав доступа.
     * @param {Ext.form.field.Checkbox} me
     * @param {Boolean} value Значение.
     */
    onCheckPermission: function (me, value) {
        var or, ow, ox, gr, gw, gx, wr, ww, wx;
        or = Ext.getCmp('form-prm-or').checked ? 4 : 0;
        ow = Ext.getCmp('form-prm-ow').checked ? 2 : 0;
        ox = Ext.getCmp('form-prm-ox').checked ? 1 : 0;
        gr = Ext.getCmp('form-prm-gr').checked ? 4 : 0;
        gw = Ext.getCmp('form-prm-gw').checked ? 2 : 0;
        gx = Ext.getCmp('form-prm-gx').checked ? 1 : 0;
        wr = Ext.getCmp('form-prm-wr').checked ? 4 : 0;
        ww = Ext.getCmp('form-prm-ww').checked ? 2 : 0;
        wx = Ext.getCmp('form-prm-wx').checked ? 1 : 0;
        Ext.getCmp('form-prm-digit').setValue('0' + String(or|ow|ox) + String(gr|gw|gx) + String(wr|ww|wx));
    }
});