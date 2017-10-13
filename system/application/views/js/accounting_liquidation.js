var dataRecordProv = new Ext.data.Record.create([
    {
        name: 'province_id'
    },
    {
        name: 'province_name'
    }
]);

var dataReaderProv = new Ext.data.JsonReader({
    root: 'data'
}, dataRecordProv);

var dataProxyProv = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_provinces/setDataGrid',
    method: 'POST'
});

var dataStoreProv = new Ext.data.Store({
    proxy: dataProxyProv,
    reader: dataReaderProv,
    autoLoad: true
});

var dataStoreAdvanceLiquidation, sm2;


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
    Ext.namespace('Liquidation');

    var xg = Ext.grid;

    var p = new Ext.Panel({
        title: 'Contabilidad -> Liquidacion de Anticipo ',
        collapsible: false,
        renderTo: 'panel-basic',
        width: 750,
        bodyCfg: {
        }
    });


    function state(val) {
        if (val === 't') {
            return '<span style="color:green;">LIQUIDADO</span>';
        } else {
            return '<span style="color:red;">POR LIQUIDAR</span>';
        }
        return val;
    }

    var printButton = new Ext.Button({
        text: 'Generar Pdf Control de Anticipo',
        ref: '../printButton',
        handler: function () {
            Liquidation.filterForm.getForm().getEl().dom.action = baseUrl + 'index.php/accounting/accounting_advanceliquidation/reportControlAdvanceLiquidation/';
            Liquidation.filterForm.getForm().getEl().dom.method = 'POST';
            Liquidation.filterForm.getForm().submit();
        }
    });

    Liquidation.dataRecordAdvanceLiquidation = new Ext.data.Record.create([
        {
            name: 'id'
        },
        {
            name: 'request_id'
        },
        {
            name: 'request_details'
        },
        {
            name: 'center_name'
        },
        {
            name: 'person_worker'
        },
        {
            name: 'request_area'
        },
        {
            name: 'lodging_entrancedate'
        },
        {
            name: 'lodging_exitdate'
        },
        {
            name: 'amount_estimated'
        },
        {
            name: 'liquidation_used'
        },
        {
            name: 'liquidation_repay'
        },
        {
            name: 'liquidation_given'
        },
        {
            name: 'center_consecutive'
        }
    ]);
    /*
     * Creamos el reader para el Grid 
     */
    Liquidation.dataReaderAdvanceLiquidation = new Ext.data.JsonReader({
            root: 'data',
            totalProperty: 'count',
            id: 'id'
        },
        Liquidation.dataRecordAdvanceLiquidation
    );

    var expander = new Ext.ux.grid.RowExpander({
        tpl: new Ext.Template(
            '<p><b>Detalle:</b> {request_details}</p>'
        )
    });

    Liquidation.dataProxyAdvanceLiquidation = new Ext.data.HttpProxy({
        url: baseUrl + 'index.php/accounting/accounting_advanceliquidation/getLiquidationGrid',
        method: 'POST'
    });

    dataStoreAdvanceLiquidation = new Ext.data.Store({
        id: 'editDS',
        proxy: Liquidation.dataProxyAdvanceLiquidation,
        reader: Liquidation.dataReaderAdvanceLiquidation
    });

    /*
     * Creamos el columnModel para el grid
     */
    Liquidation.advanceLiquidationColumnMode = new xg.ColumnModel(
        [new xg.RowNumberer(),
            expander,
            {
                id: 'id',
                name: 'd',
                dataIndex: 'id',
                hidden: true
            }, {
            id: 'request_id',
            name: 'request_id',
            dataIndex: 'request_id',
            hidden: true
        }, {
            id: 'center_consecutive',
            name: 'center_consecutive',
            header: '#',
            width: 30,
            dataIndex: 'center_consecutive',
            sortable: true
        }, {
            id: 'lodging_entrancedate',
            name: 'lodging_entrancedate',
            header: 'Desde',
            width: 80,
            dataIndex: 'lodging_entrancedate',
            sortable: true
        }, {
            id: 'lodging_exitdate',
            name: 'lodging_exitdate',
            header: 'Hasta',
            width: 80,
            dataIndex: 'lodging_exitdate',
            sortable: false
        }, {
            id: 'person_worker',
            name: 'person_worker',
            header: "Trabajador",
            width: 170,
            dataIndex: 'person_worker',
            sortable: true
        }, {
            id: 'request_area',
            name: 'request_area',
            header: "Area",
            width: 100,
            dataIndex: 'request_area',
            sortable: true
        }, {
            id: 'amount_estimated',
            name: 'amount_estimated',
            header: "Importe",
            width: 70,
            dataIndex: 'amount_estimated',
            sortable: false
        }, {
            id: 'liquidation_used',
            name: 'liquidation_used',
            header: "Utilizado",
            width: 50,
            dataIndex: 'liquidation_used',
            sortable: false
        }, {
            id: 'liquidation_repay',
            name: 'liquidation_repay',
            header: 'Devuelto',
            width: 50,
            dataIndex: 'liquidation_repay',
            sortable: false
        }, {
            id: 'liquidation_given',
            name: 'liquidation_given',
            header: 'Entregado',
            width: 50,
            dataIndex: 'liquidation_given',
            sortable: false
        }]
    );

    /*
     * Creamos el grid 
     */
    Liquidation.editGrid = new xg.GridPanel({
        id: 'ctr-edits-grid',
        store: dataStoreAdvanceLiquidation,
        cm: Liquidation.advanceLiquidationColumnMode,
        plugins: expander,
        stripeRows: true,
        frame: true,
        width: 750,
        height: 500,
        tbar: [printButton],
        bbar: new Ext.PagingToolbar({
            pageSize: 100,
            store: dataStoreAdvanceLiquidation,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    });

    Liquidation.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit: true,
        monitorValid: true,
        labelWidth: 140,
        height: 140,
        width: 750,
        items: [
            {
                xtype: 'datefield',
                width: 200,
                allowBlank: true,
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
                allowBlank: true,
                fieldLabel: 'Hasta',
                name: 'enddt',
                id: 'enddt',
                vtype: 'daterange',
                invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                format: 'Y-m-d',
                startDateField: 'startdt'
            }
        ]
    });

    Liquidation.filterForm.addButton({
        text: 'Borrar filtro',
        disabled: false,
        formBind: true,
        handler: function () {
            Liquidation.filterForm.getForm().reset();
            dataStoreAdvanceLiquidation.baseParams = {
                dateStart: '1900-01-01',
                dateEnd: '1900-01-01'
            };
            dataStoreAdvanceLiquidation.load({
                params: {
                    start: 0,
                    limit: 100
                }
            });
        }
    });

    /*
     * A�adimos el bot�n para filtrar
     */
    Liquidation.filterForm.addButton({
        text: 'Filtrar',
        disabled: false,
        formBind: true,
        handler: function () {
            var startDate = Ext.getCmp('startdt').getValue();
            var endDate = Ext.getCmp('enddt').getValue();
            if (endDate == "" || startDate == "") {
                Ext.Msg.alert('Error', 'Debe seleccionar un un intervalo de fecha para realizar la busqueda');
                return false;
            }
            dataStoreAdvanceLiquidation.baseParams = {
                dateStart: startDate.dateFormat('Y-m-d'),
                dateEnd: endDate.dateFormat('Y-m-d')
            };
            dataStoreAdvanceLiquidation.load({
                params: {
                    start: 0,
                    limit: 100
                }
            });
        }
    });


    /*
     * Anadimos el evento doble click en una fila para editar el registro correspondiente
     */

    Liquidation.editGrid.on('rowdblclick', function (grid, row, evt) {
        var selectedId = dataStoreAdvanceLiquidation.getAt(row).data.id;
        update_ventana(selectedId);
    });

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Liquidation.filterForm.render(Ext.get('advance_grid'));
    Liquidation.editGrid.render(Ext.get('advance_grid'));

    function update_ventana(id) {

        liquidationRecordUpdate = new Ext.data.Record.create([
            {name: 'id'},
            {name: 'center_name'},
            {name: 'person_worker'},
            {name: 'lodging_entrancedate'},
            {name: 'lodging_exitdate'},
            {name: 'amount_estimated'},
            {name: 'liquidation_used'},
            {name: 'liquidation_repay'},
            {name: 'liquidation_given'},
            {name: 'center_consecutive'},
            {name: 'lodging_entrancedate_real'},
            {name: 'lodging_exitdate_real'},
            {name: 'liquidation_date'}
        ]);

        /*
         * Creamos el reader para el formulario de alta/modificaci�n
         */
        liquidationFormReader = new Ext.data.JsonReader({
                root: 'data',
                successProperty: 'success',
                totalProperty: 'count',
                id: 'id'
            }, liquidationRecordUpdate
        );

        /*
         * Creamos el formulario de alta/modificacion de request
         */
        var updateForm = new Ext.FormPanel({
            id: 'form-requests',
            region: 'west',
            split: false,
            collapsible: true,
            frame: true,
            labelWidth: 150,
            width: 400,
            minWidth: 400,
            height: 350,
            waitMsgTarget: true,
            monitorValid: true,
            reader: liquidationFormReader, //ver referencia de nombres en el requestFormReader y a su vez este depende de requestRecordUpdate
            items: [
                {
                    fieldLabel: 'Nombre y Apellidos',
                    id: 'upd_person_worker',
                    name: 'person_worker',
                    readOnly: true,
                    disabled: true,
                    width: 220,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Centro de Costo',
                    id: 'upd_center_name',
                    name: 'center_name',
                    readOnly: true,
                    disabled: true,
                    width: 220,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Desde',
                    id: 'upd_lodging_entrancedate',
                    name: 'lodging_entrancedate',
                    hiddenName: 'lodging_entrancedate',
                    width: 100,
                    readOnly: true,
                    disabled: true,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Hasta',
                    id: 'upd_lodging_exitdate',
                    name: 'lodging_exitdate',
                    hiddenName: 'lodging_exitdate',
                    width: 100,
                    readOnly: true,
                    disabled: true,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Desde Real',
                    id: 'upd_lodging_entrancedate_real',
                    name: 'lodging_entrancedate_real',
                    hiddenName: 'lodging_entrancedate_real',
                    width: 100,
                    allowBlank: false,
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    xtype: 'datefield'
                },
                {
                    fieldLabel: 'Hasta Real',
                    id: 'upd_lodging_exitdate_real',
                    name: 'lodging_exitdate_real',
                    hiddenName: 'lodging_exitdate_real',
                    width: 100,
                    allowBlank: false,
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    xtype: 'datefield'
                },
                {
                    fieldLabel: 'Importe',
                    id: 'upd_amount_estimated',
                    name: 'amount_estimated',
                    hiddenName: 'amount_estimated',
                    readOnly: true,
                    disabled: true,
                    width: 90,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Utilizado',
                    id: 'upd_liquidation_used',
                    name: 'liquidation_used',
                    hiddenName: 'liquidation_used',
                    width: 100,
                    allowBlank: false,
                    xtype: 'textfield',
                    listeners: {
                        'blur': function () {
                            var usado = Ext.getCmp('upd_liquidation_used').getValue();
                            if (isNaN(usado) && usado.toLocaleLowerCase() != "cancelado") {
                                Ext.MessageBox.alert('Error', 'Por favor, debe introducir un valor numerico o la palabra Cancelado');
                                Ext.getCmp('upd_liquidation_used').setValue();
                                return false;
                            }
                            if (usado.toLocaleLowerCase() != "cancelado" && usado != "") {
                                Ext.getCmp('upd_liquidation_repay').setValue(Ext.getCmp('upd_amount_estimated').getValue() - usado);
                            } else {
                                Ext.getCmp('upd_liquidation_repay').setValue();
                                Ext.getCmp('upd_liquidation_used').setValue(usado.toLocaleUpperCase());
                            }
                        }
                    }
                },
                {
                    fieldLabel: 'Devuelto',
                    id: 'upd_liquidation_repay',
                    name: 'liquidation_repay',
                    hiddenName: 'liquidation_repay',
                    allowNegative: false,
                    width: 100,
                    xtype: 'numberfield'
                },
                {
                    fieldLabel: 'Entregado',
                    id: 'upd_liquidation_given',
                    name: 'liquidation_given',
                    hiddenName: 'liquidation_given',
                    allowNegative: false,
                    width: 100,
                    xtype: 'numberfield'
                },
                {
                    fieldLabel: 'Fecha Liquidacion',
                    id: 'upd_liquidation_date',
                    name: 'liquidation_date',
                    hiddenName: 'liquidation_date',
                    width: 100,
                    allowBlank: false,
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    xtype: 'datefield'
                },
                {
                    id: 'upd_id',
                    name: 'id',
                    hiddenName: 'id',
                    xtype: 'hidden'
                }
            ]
        });

        /*
         * A�adimos el bot�n para guardar los datos del formulario
         */
        updateForm.addButton({
            text: 'Guardar',
            disabled: false,
            formBind: true,
            handler: function () {
                Ext.getCmp('upd_liquidation_repay').enable();
                updateForm.getForm().submit({
                    url: baseUrl + 'index.php/accounting/accounting_advanceliquidation/insertLiquidation',
                    waitMsg: 'Salvando datos...',
                    failure: function (form, action) {
                        if (action.failureType == 'server') {
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.alert('Fall&oacute; el registro!', obj.errors.reason);
                        }
                        dataStoreAdvanceLiquidation.load({
                            params: {
                                start: 0,
                                limit: 100
                            }
                        });
                    },
                    success: function (form, request) {
                        Ext.MessageBox.show({
                            title: 'Datos salvados correctamente',
                            msg: 'Datos salvados correctamente',
                            width: 300,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.INFO
                        });
                        responseData = Ext.util.JSON.decode(request.response.responseText);
                        updateForm.getForm().reset();
                        updateWindow.destroy();
                        dataStoreAdvanceLiquidation.load({
                            params: {
                                start: 0,
                                limit: 100
                            }
                        });
                    }
                });

            }
        });

        /*
         * A�adimos el bot�n para borrar el formulario
         */
        updateForm.addButton({
            text: 'Cancelar',
            disabled: false,
            handler: function () {
                updateForm.getForm().reset();
                updateWindow.destroy();
            }
        });

        var updateWindow;

        updateForm.load({
            url: baseUrl + 'index.php/accounting/accounting_advanceliquidation/getLiquidationById/' + id
        });

        if (!updateWindow) {

            updateWindow = new Ext.Window({
                title: 'Editar Liquidacion de Anticipo',
                layout: 'form',
                top: 200,
                width: 425,
                height: 380,
                resizable: false,
                modal: true,
                bodyStyle: 'padding:5px;',
                items: updateForm  //adicionamos la forma dentro de la ventana

            });
        }
        updateWindow.show(this);

    }

});

///////fin del onReady




