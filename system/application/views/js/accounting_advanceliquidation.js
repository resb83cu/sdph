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
    daterange: function(val, field) {
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

Ext.onReady(function() {


    Ext.BLANK_IMAGE_URL = baseAppUrl + 'views/images/s.gif';
    Ext.QuickTips.init();

    Ext.form.Field.prototype.msgTarget = 'side';

    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Advance');

    var xg = Ext.grid;
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Advance.editGrid.printButton.enable();
                } else {
                    Advance.editGrid.printButton.disable();
                }
            }
        }
    });

    var p = new Ext.Panel({
        title: 'Contabilidad -> Solicitud Anticipo Individual',
        collapsible: false,
        renderTo: 'panel-basic',
        width: 750,
        bodyCfg: {
        }
    });


    function state(val) {
        if (val === 't') {
            return '<span style="color:green;">SOLICITADO</span>';
        } else {
            return '<span style="color:red;">POR SOLICITAR</span>';
        }
        return val;
    }

    var printButton = new Ext.Button({
        text: 'Generar Pdf Anticipo',
        disabled: true,
        ref: '../printButton',
        handler: function() {
            array = sm2.getSelections();
            var len = array.length;
            if (len > 1) {
                Ext.MessageBox.alert('Error', 'Debe seleccionar una sola solicitud para generar el anticipo');
                return false;
            } else if (len = 1) {
                Advance.filterForm.getForm().getEl().dom.action = baseUrl + 'index.php/accounting/accounting_advanceliquidation/reportAdvanceLiquidationById/' + array[0].get('request_id');
                Advance.filterForm.getForm().getEl().dom.method = 'POST';
                Advance.filterForm.getForm().submit();
            }
            sm2.clearSelections();
            dataStoreAdvanceLiquidation.load({
                params: {
                    start: 0,
                    limit: 100
                }
            });
        }

    });

    Advance.dataRecordAdvanceLiquidation = new Ext.data.Record.create([
        {
            name: 'request_id'
        },
        {
            name: 'request_date'
        },
        {
            name: 'diet_entrancedate'
        },
        {
            name: 'diet_exitdate'
        },
        {
            name: 'center_name'
        },
        {
            name: 'person_worker'
        },
        {
            name: 'advance_requested'
        },
        {
            name: 'request_area'
        },
        {
            name: 'request_details'
        },
        {
            name: 'province_name'
        }
    ]);
    /*
     * Creamos el reader para el Grid 
     */
    Advance.dataReaderAdvanceLiquidation = new Ext.data.JsonReader({
        root: 'data',
        totalProperty: 'count',
        id: 'request_id'
    },
    Advance.dataRecordAdvanceLiquidation
            );


    Advance.dataProxyAdvanceLiquidation = new Ext.data.HttpProxy({
        url: baseUrl + 'index.php/accounting/accounting_advanceliquidation/getAdvanceLiquidationRequests',
        method: 'POST'
    });

    dataStoreAdvanceLiquidation = new Ext.data.GroupingStore({
        id: 'editDS',
        proxy: Advance.dataProxyAdvanceLiquidation,
        reader: Advance.dataReaderAdvanceLiquidation,
        sortInfo: {
            field: 'request_details',
            direction: "ASC"
        },
        groupField: 'request_details'
    });

    /*
     * Creamos el columnModel para el grid
     */
    Advance.advanceLiquidationColumnMode = new xg.ColumnModel(
            [new xg.RowNumberer(),
                sm2,
                {
                    id: 'request_id',
                    name: 'request_id',
                    dataIndex: 'request_id',
                    hidden: true
                }, {
                    id: 'request_details',
                    name: 'request_details',
                    header: "Detalle",
                    dataIndex: 'request_details',
                    hidden: true
                }, {
                    id: 'advance_requested',
                    name: 'advance_requested',
                    header: "Estado",
                    renderer: state,
                    width: 90,
                    dataIndex: 'advance_requested',
                    sortable: false
                }, {
                    id: 'diet_entrancedate',
                    name: 'diet_entrancedate',
                    header: 'Desde',
                    width: 80,
                    dataIndex: 'diet_entrancedate',
                    sortable: true
                }, {
                    id: 'diet_exitdate',
                    name: 'diet_exitdate',
                    header: 'Hasta',
                    width: 80,
                    dataIndex: 'diet_exitdate',
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
                }/*, {
                 id: 'center_name',
                 name: 'center_name',
                 header: "Centro de Costo",
                 width: 120,
                 dataIndex: 'center_name',
                 sortable: true
                 }{
                 id: 'request_date',
                 name: 'request_date',
                 header: 'Fecha de Solicitud',
                 format: 'dd-mm-YYYY',
                 width: 95,
                 dataIndex: 'request_date',
                 sortable: true
                 },*/]
            );

    /*
     * Creamos el grid 
     */
    Advance.editGrid = new xg.GridPanel({
        id: 'ctr-edits-grid',
        store: dataStoreAdvanceLiquidation,
        cm: Advance.advanceLiquidationColumnMode,
        view: new Ext.grid.GroupingView({
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Personas" : "Persona"]})'
        }),
        stripeRows: true,
        frame: true,
        collapsible: true,
        width: 750,
        height: 500,
        tbar: [/*{
         text: 'Cancelar',
         tooltip: 'Cancelar la(s) Solicitud(es) de Hospedaje Seleccionada(s)',
         iconCls: 'del',
         ref: '../removeButton',
         disabled: true,
         handler: function() {
         array = sm2.getSelections();
         if (array.length > 0) {
         Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea cancelar este(os) Hospedaje(s)?', delRecords);
         }
         }
         }miboton,
            '-',*/
            printButton
        ],
        bbar: new Ext.PagingToolbar({
            pageSize: 100,
            store: dataStoreAdvanceLiquidation,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel: sm2
    });

    Advance.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit: true,
        monitorValid: true,
        labelWidth: 140,
        height: 140,
        width: 750,
        items: [new Ext.form.ComboBox({
                store: dataStoreProv,
                fieldLabel: 'Provincia del Hospedaje',
                displayField: 'province_name',
                valueField: 'province_id',
                hiddenName: 'province_id',
                allowBlank: true,
                formBind: true,
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText: 'Seleccione una Provincia...',
                selectOnFocus: true,
                width: 200,
                id: 'filter_province_id',
                name: 'filter_province_id',
                listeners: {
                    'blur': function() {
                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('filter_province_id').getValue());
                        if (flag == -1) {
                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                            Ext.getCmp('filter_province_id').reset();
                            return false;
                        }
                    }
                }

            }), {
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
            }, {
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
            }]
    });

    Advance.filterForm.addButton({
        text: 'Borrar filtro',
        disabled: false,
        formBind: true,
        handler: function() {
            Advance.filterForm.getForm().reset();
            dataStoreAdvanceLiquidation.baseParams = {
                dateStart: '1900-01-01',
                dateEnd: '1900-01-01',
                province: 0
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
    Advance.filterForm.addButton({
        text: 'Filtrar',
        disabled: false,
        formBind: true,
        handler: function() {
            var startDate = Ext.getCmp('startdt').getValue();
            var endDate = Ext.getCmp('enddt').getValue();
            var province = Ext.getCmp('filter_province_id').getValue();
            dataStoreAdvanceLiquidation.baseParams = {
                dateStart: startDate.dateFormat('Y-m-d'),
                dateEnd: endDate.dateFormat('Y-m-d'),
                province: province
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
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Advance.filterForm.render(Ext.get('advance_grid'));
    Advance.editGrid.render(Ext.get('advance_grid'));



    /*
     * A?adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Advance.editGrid.on('rowdblclick', function (grid, row, evt) {
        var selectedId = dataStoreAdvanceLiquidation.getAt(row).data.request_id;
        detail_ventana(selectedId);
    });

    function detail_ventana(id) {

        advanceRecordDetail = new Ext.data.Record.create([
            {
                name: 'request_id',
                type: 'int'
            },
            {
                name: 'request_details'
            },
            {
                name: 'diet_entrancedate'
            },
            {
                name: 'diet_exitdate'
            },
            {
                name: 'person_requestedby'
            },
            {
                name: 'center_requestedby'
            },
            {
                name: 'request_consecutive'
            },
            {
                name: 'request_area'
            },
            {
                name: 'person_groupresponsable'
            },
            {
                name: 'request_inversiontask'
            },
            {
                name: 'person_worker'
            },
            {
                name: 'motive_name'
            },
            {
                name: 'center_advance'
            }

        ]);

        /*
         * Creamos el reader para el formulario de alta/modificaci?n
         */
        advanceFormReader = new Ext.data.JsonReader({
                root: 'data',
                successProperty: 'success',
                totalProperty: 'count',
                id: 'request_id'
            }, advanceRecordDetail
        );

        /*
         * Creamos el formulario de alta/modificacion de request
         */
        var detailForm = new Ext.FormPanel({
            id: 'form-requests',
            region: 'west',
            split: false,
            collapsible: true,
            frame: true,
            labelWidth: 150,
            width: 735,
            minWidth: 730,
            height: 390,
            waitMsgTarget: true,
            monitorValid: true,
            reader: advanceFormReader,
            items: [
                {
                    layout: 'column',
                    items: [
                        {
                            columnWidth: .5,
                            layout: 'form',
                            items: [
                                {
                                    fieldLabel: 'Consecutivo',
                                    hiddenName: 'request_consecutive',
                                    name: 'request_consecutive',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'Tarea Inversion',
                                    hiddenName: 'request_inversiontask',
                                    name: 'request_inversiontask',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'Motivo',
                                    hiddenName: 'motive_name',
                                    name: 'motive_name',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'CC solicita',
                                    hiddenName: 'center_requestedby',
                                    name: 'center_requestedby',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'Solicitante',
                                    hiddenName: 'person_requestedby',
                                    name: 'person_requestedby',
                                    width: 180,
                                    xtype: 'textfield'
                                }
                            ]
                        },
                        {
                            columnWidth: .5,
                            layout: 'form',
                            items: [
                                {
                                    fieldLabel: 'Area de trabajo',
                                    hiddenName: 'request_area',
                                    name: 'request_area',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'CC entrega anticipo',
                                    hiddenName: 'center_advance',
                                    name: 'center_advance',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'Dieta desde',
                                    name: 'diet_entrancedate',
                                    hiddenName: 'diet_entrancedate',
                                    width: 180,
                                    xtype: 'textfield'
                                },
                                {
                                    fieldLabel: 'Dieta hasta',
                                    name: 'diet_exitdate', //debe coincidir con los campos del requestsRecordUpdate
                                    hiddenName: 'diet_exitdate',
                                    width: 180,
                                    xtype: 'textfield'
                                }
                            ]
                        }
                    ]
                },
                {
                    fieldLabel: 'Trabajador',
                    name: 'person_worker',
                    hiddenName: 'person_worker',
                    width: 400,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Persona Autorizada del Grupo',
                    name: 'person_groupresponsable',
                    hiddenName: 'person_groupresponsable',
                    width: 400,
                    xtype: 'textfield'
                },
                {
                    fieldLabel: 'Detalle',
                    name: 'request_details',
                    hiddenName: 'request_details',
                    width: 500,
                    heigth: 200,
                    xtype: 'textarea'
                }
            ]
        });

        /*
         * A?adimos el bot?n para borrar el formulario
         */
        detailForm.addButton({
            text: 'CERRAR',
            disabled: false,
            handler: function () {
                detailForm.getForm().reset();
                detailWindow.destroy();
                sm2.clearSelections();
            }
        });

        var detailWindow;
        if (id > 0) {
            detailForm.load({
                url: baseUrl + 'index.php/request/request_requests/getRequestDetailById/' + id
            });
        }

        if (!detailWindow) {

            detailWindow = new Ext.Window({
                title: 'Detalles de la Solicitud',
                layout: 'form',
                top: 200,
                width: 760,
                height: 430,
                resizable: false,
                modal: true,
                bodyStyle: 'padding:5px;',
                items: detailForm

            });
        }
        detailWindow.show(this);

    }
});

///////fin del onReady




