var dataRecordProv = new Ext.data.Record.create([
    {name: 'province_id'},
    {name: 'province_name'}
]);

var dataReaderProv = new Ext.data.JsonReader({root: 'data'}, dataRecordProv);

var dataProxyProv = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_provinces/setDataGrid',
    method: 'POST'
});

var dataStoreProv = new Ext.data.Store({
    proxy: dataProxyProv,
    reader: dataReaderProv,
    autoLoad: true
});

var dataRecordHotel = new Ext.data.Record.create([
    {name: 'hotel_id'},
    {name: 'hotel_name'}
]);

var dataReaderHotel = new Ext.data.JsonReader({root: 'data'}, dataRecordHotel);

var dataProxyHotel = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_hotels/setDataGrid',
    method: 'POST'
});

var dataStoreHotel = new Ext.data.Store({
    proxy: dataProxyHotel,
    reader: dataReaderHotel
    //autoLoad:true
});

var dataRecordChain = new Ext.data.Record.create([
    {name: 'chain_id'},
    {name: 'chain_name'}
]);

var dataReaderChain = new Ext.data.JsonReader({root: 'data'}, dataRecordChain);

var dataProxyChain = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_hotelchains/setDataGrid',
    method: 'POST'
});

var dataStoreChain = new Ext.data.Store({
    proxy: dataProxyChain,
    reader: dataReaderChain,
    autoLoad: true
});

var dataRecordCenter = new Ext.data.Record.create([
    {name: 'center_id'},
    {name: 'center_name'}
]);

var dataReaderCenter = new Ext.data.JsonReader({root: 'data'}, dataRecordCenter);

var dataProxyCenter = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_costcenters/setDataGrid',
    method: 'POST'
});

var dataStoreCenter = new Ext.data.Store({
    proxy: dataProxyCenter,
    reader: dataReaderCenter,
    autoLoad: true
});

var dataRecordMotive = new Ext.data.Record.create([
    {name: 'motive_id'},
    {name: 'motive_name'}
]);

var dataReaderMotive = new Ext.data.JsonReader({root: 'data'}, dataRecordMotive);

var dataProxyMotive = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_motives/setDataGrid',
    method: 'POST'
});

var dataStoreMotive = new Ext.data.Store({
    proxy: dataProxyMotive,
    reader: dataReaderMotive,
    autoLoad: true
});

var dataRecordTransport = new Ext.data.Record.create([
    {name: 'transport_id'},
    {name: 'transport_name'}
]);

var dataReaderTransport = new Ext.data.JsonReader({root: 'data'}, dataRecordTransport);

var dataProxyTransport = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_lodgingtransports/setDataGrid',
    method: 'POST'
});

var dataStoreTransport = new Ext.data.Store({
    proxy: dataProxyTransport,
    reader: dataReaderTransport,
    autoLoad: true
});

var dataStoreLodgingEdit;


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

Ext.onReady(function () {

    Ext.BLANK_IMAGE_URL = baseAppUrl + 'views/images/s.gif';
    Ext.QuickTips.init();

    Ext.form.Field.prototype.msgTarget = 'side';

    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Accounting');


    var xg = Ext.grid;

    /*
     * Definimos el registro
     */

    Accounting.accountingRecord = new Ext.data.Record.create([
        {name: 'request_id'},
        {name: 'request_date'},
        {name: 'letter_id'},
        {name: 'lodging_entrancedate'},
        {name: 'lodging_exitdate'},
        {name: 'center_name'},
        {name: 'person_worker'},
        {name: 'person_identity'},
        {name: 'request_details', type: 'string'},
        {name: 'province_lodging'},
        {name: 'hotel_name'},
        {name: 'diet'},
        {name: 'lodging'}
    ]);


    /*
     * Creamos el reader para el Grid
     */
    Accounting.accountingGridReader = new Ext.data.JsonReader({
            root: 'data',
            totalProperty: 'count',
            id: 'request_id'
        },
        Accounting.accountingRecord
    );

    var p = new Ext.Panel({
        title: 'Contabilidad -> Hospedaje',
        collapsible: false,
        renderTo: 'panel-basic',
        width: 750,
        bodyCfg: {}
    });

    var expander = new Ext.ux.grid.RowExpander({
        tpl: new Ext.Template(
            '<p><b>Detalle:</b> {request_details}</p>'
        )
    });

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Accounting.accountingDataProxy = new Ext.data.HttpProxy({
        url: baseUrl + 'index.php/lodging/lodging_conciliations/accounting/true/no',
        method: 'POST'
    });

    accountingDataStore = new Ext.data.Store({
        id: 'accountingDS',
        proxy: Accounting.accountingDataProxy,
        reader: Accounting.accountingGridReader
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Accounting.accountingColumnMode = new xg.ColumnModel(
        [new xg.RowNumberer(),
            expander,
            {
                id: 'request_id',
                name: 'request_id',
                dataIndex: 'request_id',
                hidden: true
            }, {
            id: 'letter_id',
            name: 'letter_id',
            header: "No. Carta",
            width: 80,
            dataIndex: 'letter_id',
            sortable: true
        }, {
            id: 'person_identity',
            name: 'person_identity',
            header: "CI",
            width: 90,
            dataIndex: 'person_identity',
            sortable: true
        }, {
            id: 'person_worker',
            name: 'person_worker',
            header: "Trabajador",
            width: 180,
            dataIndex: 'person_worker',
            sortable: true
        }, {
            id: 'center_name',
            name: 'center_name',
            header: "Centro de Costo",
            width: 130,
            dataIndex: 'center_name',
            sortable: true
        }, {
            id: 'province_lodging',
            name: 'province_lodging',
            header: "Prov. Hosp.",
            width: 130,
            dataIndex: 'province_lodging',
            sortable: true
        }, {
            id: 'hotel_name',
            name: 'hotel_name',
            header: "Hotel",
            width: 130,
            dataIndex: 'hotel_name',
            sortable: true
        }, {
            id: 'lodging_entrancedate',
            name: 'lodging_entrancedate',
            header: 'Entrada',
            width: 80,
            dataIndex: 'lodging_entrancedate',
            sortable: true
        }, {
            id: 'lodging_exitdate',
            name: 'lodging_exitdate',
            header: 'Salida',
            width: 80,
            dataIndex: 'lodging_exitdate',
            sortable: false
        }, {
            id: 'diet',
            name: 'diet',
            header: 'Dieta',
            width: 50,
            //renderer: 'usMoney',
            dataIndex: 'diet',
            sortable: false
        }, {
            id: 'lodging',
            name: 'lodging',
            header: 'Hospedaje',
            width: 60,
            renderer: 'usMoney',
            dataIndex: 'lodging',
            sortable: false
        }]
    );

    /*
     * Creamos el grid
     */
    Accounting.accountingGrid = new xg.GridPanel({
        id: 'ctr-accounting-grid',
        store: accountingDataStore,
        cm: Accounting.accountingColumnMode,
        viewConfig: {
            forceFit: false
        },
        frame: true,
        stripeRows: true,
        collapsible: true,
        width: 750,
        height: 380,
        plugins: expander,
        tbar: [{
            text: 'Exportar a pdf',
            tooltip: 'Exportar a pdf',
            iconCls: 'pdf',
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function () {
                if (accountingDataStore.getCount() > 0) {
                    Accounting.filterForm.getForm().getEl().dom.action = baseUrl + 'index.php/lodging/lodging_conciliations/accounting/true/si';
                    Accounting.filterForm.getForm().getEl().dom.method = 'POST';
                    Accounting.filterForm.getForm().submit();
                }
                else {
                    Ext.Msg.alert('Mensaje', 'No hay datos que exportar!');
                }
            }
        }],
        bbar: new Ext.PagingToolbar({
            store: accountingDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    });

    Accounting.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        standardSubmit: true,
        frame: true,
        monitorValid: true,
        labelWidth: 142,
        height: 180,
        width: 750,
        items: [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                border: false,
                items: [new Ext.form.ComboBox({
                    store: dataStoreProv,
                    fieldLabel: 'Provincia del Hospedaje',
                    displayField: 'province_name',
                    valueField: 'province_id',
                    hiddenName: 'province_id',
                    allowBlank: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione una Provincia...',
                    selectOnFocus: true,
                    width: 200,
                    id: 'filter_province_id',
                    name: 'filter_province_id',
                    listeners: {
                        'select': function () {
                            dataStoreHotel.baseParams = {province_id: Ext.getCmp('filter_province_id').getValue()};
                            dataStoreHotel.load();
                        },
                        'blur': function () {
                            var flag = dataStoreProv.findExact('province_id', Ext.getCmp('filter_province_id').getValue());
                            if (flag == -1 && Ext.getCmp('filter_province_id').getValue() != "") {
                                Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
                                Ext.getCmp('filter_province_id').reset();
                                return false;
                            }
                        }
                    }
                }), new Ext.form.ComboBox({
                    store: dataStoreChain,
                    fieldLabel: 'Cadena hotelera',
                    displayField: 'chain_name',
                    valueField: 'chain_id',
                    hiddenName: 'chain_id',
                    allowBlank: true,
                    id: 'frm_chain_id',
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione una Cadena...',
                    selectOnFocus: true,
                    width: 200,
                    listeners: {
                        'blur': function () {
                            var flag = dataStoreChain.findExact('chain_id', Ext.getCmp('frm_chain_id').getValue());
                            if (flag == -1 && Ext.getCmp('frm_chain_id').getValue() != "") {
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('frm_chain_id').reset();
                                return false;
                            }
                        }
                    }
                }), new Ext.form.ComboBox({
                    store: dataStoreHotel,
                    fieldLabel: 'Hotel',
                    displayField: 'hotel_name',
                    valueField: 'hotel_id',
                    hiddenName: 'hotel_id',
                    allowBlank: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Hotel...',
                    selectOnFocus: true,
                    width: 200,
                    id: 'filter_hotel_id',
                    name: 'filter_hotel_id',
                    listeners: {
                        'blur': function () {
                            var flag = dataStoreHotel.findExact('hotel_id', Ext.getCmp('filter_hotel_id').getValue());
                            if (flag == -1 && Ext.getCmp('filter_hotel_id').getValue() != "") {
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('filter_hotel_id').reset();
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
                    hiddenName: 'startdt',
                    id: 'startdt',
                    vtype: 'daterange',
                    format: 'Y-m-d',
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    endDateField: 'enddt' // id of the end date field
                }, {
                    xtype: 'datefield',
                    width: 200,
                    allowBlank: false,
                    fieldLabel: 'Hasta',
                    name: 'enddt',
                    hiddenName: 'enddt',
                    id: 'enddt',
                    vtype: 'daterange',
                    format: 'Y-m-d',
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    startDateField: 'startdt' // id of the start date field
                }
                ]
            }, {
                columnWidth: .5,
                layout: 'form',
                border: false,
                items: [new Ext.form.ComboBox({
                    store: dataStoreMotive,
                    fieldLabel: 'Motivo del viaje',
                    displayField: 'motive_name',
                    valueField: 'motive_id',
                    hiddenName: 'motive_id',
                    allowBlank: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Motivo...',
                    selectOnFocus: true,
                    width: 200,
                    id: 'filter_motive_id',
                    name: 'filter_motive_id',
                    listeners: {
                        'blur': function () {
                            var flag = dataStoreMotive.findExact('motive_id', Ext.getCmp('filter_motive_id').getValue());
                            if (flag == -1 && Ext.getCmp('filter_motive_id').getValue() != "") {
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('filter_motive_id').reset();
                                return false;
                            }
                        }
                    }
                }), new Ext.form.ComboBox({
                    store: dataStoreCenter,
                    fieldLabel: 'Centro de Costo',
                    displayField: 'center_name',
                    valueField: 'center_id',
                    hiddenName: 'center_id',
                    allowBlank: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Centro de Costo...',
                    selectOnFocus: true,
                    width: 200,
                    id: 'filter_center_id',
                    name: 'filter_center_id',
                    listeners: {
                        'blur': function () {
                            var flag = dataStoreCenter.findExact('center_id', Ext.getCmp('filter_center_id').getValue());
                            if (flag == -1 && Ext.getCmp('filter_center_id').getValue() != "") {
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('filter_center_id').reset();
                                return false;
                            }
                        }
                    }
                }), new Ext.form.ComboBox({
                    store: ['Si', 'No'],
                    fieldLabel: 'Conciliado',
                    displayField: 'conciliation',
                    valueField: 'conciliation',
                    hiddenName: 'conciliation',
                    allowBlank: true,
                    readOnly: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    width: 50,
                    id: 'filter_conciliation',
                    name: 'filter_conciliation'
                }), new Ext.form.ComboBox({
                    store: ['Si', 'No'],
                    fieldLabel: 'Tarea Inversion',
                    displayField: 'inversion',
                    valueField: 'inversion',
                    hiddenName: 'inversion',
                    allowBlank: true,
                    readOnly: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    width: 50,
                    id: 'filter_inversion',
                    name: 'filter_inversion'
                })
                ]
            }]
        }]
    });

    Accounting.filterForm.addButton({
        text: 'Limpiar filtro',
        disabled: false,
        formBind: true,
        handler: function () {
            Accounting.filterForm.getForm().reset();
            accountingDataStore.baseParams = {
                dateStart: '1900-01-01',
                dateEnd: '1900-01-01',
                hotel: 0,
                province: 0,
                motive: 0,
                center: 0,
                conciliation: '',
                inversion: '',
                chain_id: 0
            };
            accountingDataStore.load();
        }
    });

    /*
     * A�adimos el bot�n para filtrar
     */
    Accounting.filterForm.addButton({
        text: 'Filtrar',
        disabled: false,
        formBind: true,
        handler: function () {
            var startDate = Accounting.filterForm.findById('startdt').getValue();
            var endDate = Accounting.filterForm.findById('enddt').getValue();
            var province = Accounting.filterForm.findById('filter_province_id').getValue();
            var hotel = Accounting.filterForm.findById('filter_hotel_id').getValue();
            var motive = Accounting.filterForm.findById('filter_motive_id').getValue();
            var center = Accounting.filterForm.findById('filter_center_id').getValue();
            var conciliation = Accounting.filterForm.findById('filter_conciliation').getValue();
            var inversion = Accounting.filterForm.findById('filter_inversion').getValue();
            chain_id = Accounting.filterForm.findById('frm_chain_id').getValue();

            accountingDataStore.baseParams = {
                dateStart: startDate.dateFormat('Y-m-d'),
                dateEnd: endDate.dateFormat('Y-m-d'),
                hotel: hotel,
                province: province,
                motive: motive,
                center: center,
                conciliation: conciliation,
                inversion: inversion,
                chain_id: chain_id
            };
            accountingDataStore.load();
        }
    });

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Accounting.filterForm.render(Ext.get('accounting_grid'));
    Accounting.accountingGrid.render(Ext.get('accounting_grid'));
});   
