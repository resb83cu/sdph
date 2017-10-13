Ext.apply(Ext.form.VTypes, {
    daterange: function (val, field) {
        var date = field.parseDate(val);

        if (!date) {
            return;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        }
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        return true;
    }
});

Date.patterns = {
    ISO8601Long: "Y-m-d H:i:s",
    ISO8601Short: "Y-m-d",
    ShortDate: "n/j/Y",
    LongDate: "l, F d, Y",
    FullDateTime: "l, F d, Y g:i:s A",
    MonthDay: "F d",
    ShortTime: "g:i A",
    LongTime: "g:i:s A",
    SortableDateTime: "Y-m-d\\TH:i:s",
    UniversalSortableDateTime: "Y-m-d H:i:sO",
    YearMonth: "F, Y"
};


var dt = new Date();
var today = dt.format(Date.patterns.ISO8601Short);

Ext.onReady(function () {

    Ext.BLANK_IMAGE_URL = baseAppUrl + 'views/images/s.gif';
    Ext.QuickTips.init();

    Ext.form.Field.prototype.msgTarget = 'side';

    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Control');

    var xg = Ext.grid;

    var p = new Ext.Panel({
        title: 'Contabilidad -> Generar Control de Anticipo en Excel',
        collapsible: false,
        renderTo: 'panel-basic',
        width: 750,
        bodyCfg: {
        }
    });

    Control.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit: true,
        monitorValid: true,
        labelWidth: 140,
        height: 100,
        width: 750,
        items: [
            {
                xtype: 'datefield',
                width: 200,
                allowBlank: false,
                fieldLabel: 'Desde',
                name: 'startdt',
                id: 'startdt',
                vtype: 'daterange',
                invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                format: 'Y-m-d',
                endDateField: 'enddt'
            },
            {
                xtype: 'datefield',
                width: 200,
                allowBlank: false,
                fieldLabel: 'Hasta',
                name: 'enddt',
                id: 'enddt',
                vtype: 'daterange',
                invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                format: 'Y-m-d',
                startDateField: 'startdt'
            }/*,
             new Ext.form.ComboBox({
             store: dataStoreCenterSap,
             fieldLabel: 'CC entrega anticipo',
             displayField: 'center_name',
             valueField: 'center_id',
             hiddenName: 'center_idadvance',
             allowBlank: true,
             typeAhead: true,
             mode: 'local',
             triggerAction: 'all',
             emptyText: 'Seleccione un centro de costo...',
             selectOnFocus: true,
             width: 200,
             id: 'frm_center_idadvance',
             name: 'center_idadvance',
             listeners: {
             'blur': function () {
             var flag = dataStoreCenterSap.findExact('center_id', Ext.getCmp('frm_center_idadvance').getValue());
             if (flag == -1) {
             Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
             Ext.getCmp('frm_center_idadvance').reset();
             return false;
             }
             }
             }
             })*/
        ]
    });

    Control.filterForm.addButton({
        text: 'Limpiar Filtro',
        disabled: false,
        formBind: true,
        handler: function () {
            Control.filterForm.getForm().reset();
        }
    });

    Control.filterForm.addButton({
        text: 'Exportar a excel',
        disabled: true,
        formBind: true,
        iconCls: 'xls',
        handler: function () {
            var desde = Ext.getCmp('startdt').getValue();
            var hasta = Ext.getCmp('enddt').getValue();
            if (desde == "" || hasta == "") {
                Ext.MessageBox.alert('Error', 'Por favor, debe seleccionar un intervalo de fecha.');
                return false;
            }
            Control.filterForm.getForm().getEl().dom.action = baseUrl + 'index.php/accounting/accounting_controlexport/reportControlExcel/';
            Control.filterForm.getForm().getEl().dom.method = 'POST';
            Control.filterForm.getForm().submit();
        }
    });

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Control.filterForm.render(Ext.get('control_excel_grid'));

});

///////fin del onReady





