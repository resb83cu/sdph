var conciliationsDataStore;
var array, sm2;

Ext.apply(Ext.form.VTypes, {
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
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

var dataRecordProv = new Ext.data.Record.create([
{
    name:'province_id'
},
{
    name:'province_name'
}
]);
var dataReaderProv = new Ext.data.JsonReader({
    root:'data'
},dataRecordProv);
var dataProxyProv = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_provinces/setDataGrid',
    method: 'POST'
});
var dataStoreProv = new Ext.data.Store({
    proxy: dataProxyProv,
    reader: dataReaderProv,
    autoLoad: true
});

var dataRecordTransport = new Ext.data.Record.create([
{
    name: 'transport_id', 
    type: 'int'
},
{
    name: 'transport_name', 
    type: 'string'
}
]);

/*
     * Creamos el DataProxy para carga remota de los datos
     */
var dataProxyTransport = new Ext.data.HttpProxy({
    url: baseUrl+'index.php/conf/conf_tickettransports/setDataGrid',
    method: 'POST'
});
    
var dataReaderTransport = new Ext.data.JsonReader({
    root:'data'
},dataRecordTransport);

var dataStoreTransport = new Ext.data.Store({
    id: 'transportsDS',
    proxy: dataProxyTransport,
    reader: dataReaderTransport
});				    


Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Conciliations');
    

    var xg = Ext.grid;
   	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Conciliations.conciliationsGrid.conciliationButton.enable();
                } else {
                    Conciliations.conciliationsGrid.conciliationButton.disable();
                }
            }
        }
    });
    
    /*
     * Definimos el registro
     */
     
    Conciliations.conciliationsRecord = new Ext.data.Record.create([
    {
        name: 'request_id', 
        type: 'int'
    },

    {
        name: 'bill_number', 
        type: 'string'
    },

    {
        name: 'cheque', 
        type: 'string'
    },

    {
        name: 'person_nameworker', 
        type: 'string'
    },

    {
        name: 'person_identity', 
        type: 'string'
    },

    {
        name: 'person_namelicensedby', 
        type: 'string'
    },

    {
        name: 'person_nameeditedby', 
        type: 'string'
    },

    {
        name: 'center_name', 
        type: 'string'
    },

    {
        name: 'ticket_date'
    }
    ]);
    
    var p = new Ext.Panel({
        title: 'Pasaje -> Conciliaci&oacute;n de pasajes',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
    }
    });

    /*
     * Creamos el reader para el Grid
     */
    Conciliations.conciliationsGridReader = new Ext.data.JsonReader({
        root: 'data',
        totalProperty: 'count'
    },
    Conciliations.conciliationsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Conciliations.conciliationsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/ticket/ticket_conciliations/setDataGrid',
        method: 'POST'
    });

    conciliationsDataStore = new Ext.data.Store({
        id: 'conciliationsDS',
        proxy: Conciliations.conciliationsDataProxy,
        reader: Conciliations.conciliationsGridReader
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Conciliations.conciliationsColumnMode = new xg.ColumnModel(
        [new xg.RowNumberer(),
        sm2,
        {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },{
            id: 'bill_number',
            name: 'bill_number',
            header: 'No. Factura',
            width: 80,
            dataIndex: 'bill_number',
            sortable: true
        },{
            id: 'cheque',
            name: 'cheque',
            header: 'Voucher',
            width: 80,
            dataIndex: 'cheque',
            sortable: false
        },{
            id: 'ticket_date',
            name : 'ticket_date',
            header: 'Fecha',
            width: 70,
            dataIndex: 'ticket_date',
            sortable: true
        },{
            id: 'person_nameworker',
            name: 'person_nameworker',
            header: 'Trabajador',
            width: 180,
            dataIndex: 'person_nameworker',
            sortable: true
        },{
            id: 'person_identity',
            name : 'person_identity',
            header: 'CI',
            width: 80,
            dataIndex: 'person_identity',
            sortable: false
        },{
            id: 'center_name',
            name : 'center_name',
            header: 'Centro Costo',
            width: 150,
            dataIndex: 'center_name',
            sortable: true
        },{
            id: 'person_namelicensedby',
            name: 'person_namelicensedby',
            header: 'Autorizado por',
            width: 180,
            dataIndex: 'person_namelicensedby',
            sortable: true
        },{
            id: 'person_nameeditedby',
            name: 'person_nameeditedby',
            header: 'Editado por',
            width: 180,
            dataIndex: 'person_nameeditedby',
            sortable: true
        }]
        );

    /*
     * Creamos el grid
     */
    Conciliations.conciliationsGrid = new xg.GridPanel({
        id : 'ctr-conciliations-grid',
        store : conciliationsDataStore,
        cm : Conciliations.conciliationsColumnMode,
        viewConfig: {
            forceFit:false
        },
        frame:true,
        stripeRows: true,
        collapsible: true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Conciliar',
            tooltip:'Conciliar pasaje(s) seleccionado(s)',
            iconCls:'add',
            ref: '../conciliationButton',
            disabled: true,
            handler: function(){
                array = sm2.getSelections();
                for (var i = 0, len = array.length; i < len; i++) {
                    Ext.Ajax.request({
                        url: baseUrl+'index.php/ticket/ticket_conciliations/insert/'+array[i].get('request_id')+'/'+Ext.getCmp('bill_number').getValue()+'/'+array[i].get('ticket_date'),
                        method: 'GET',
                        disableCaching: false,
                        success: function(){
                        },
                        failure: function(){
                            Ext.MessageBox.alert('Error', 'No se pudo conciliar el pasaje.');
                        }
                    });
                }
                Ext.getCmp('bill_number').setValue('');
                sm2.clearSelections();
                conciliationsDataStore.load();
            }
        }, {
            xtype: 'textfield',
            name: 'bill_number',
            id: 'bill_number',
            emptyText: 'No. de factura'
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: conciliationsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });
    
    var transportArray = [
    ['2','Viazul'],
    ['3','Avion'],
    ['5','Barco']
    ];
	
    var transportStore=new Ext.data.SimpleStore({
        fields: ['transport_id', 'transport_name'],
        data: transportArray
    });
	
    

    Conciliations.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        monitorValid: true,
        labelWidth: 120,
        height: 110,
        width: 750,
        items: [{
            layout:'column',
            border:false,
            items:[{
                columnWidth:.5,
                layout: 'form',
                border:false,
                items:[	new Ext.form.ComboBox({
                    store: transportStore,
                    fieldLabel: 'Tipo de Transporte',
                    displayField: 'transport_name',
                    valueField: 'transport_id',
                    hiddenName: 'transport_id',
                    allowBlank: false,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Tipo de Transporte...',
                    selectOnFocus: true,
                    width: 200,
                    id: 'filter_transport_id',
                    name : 'filter_transport_id',
                    listeners: {
                        'blur': function(){
                            var flag = transportStore.findExact( 'transport_id', Ext.getCmp('filter_transport_id').getValue());
                            if (flag == -1){
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('filter_transport_id').reset();
                                return false;
                            }
                        }
                    }
                }),{
                    fieldLabel : 'Número voucher',
                    id: 'filter_voucher',
                    name : 'filter_voucher',
                    allowBlank: true,
                    width: 150,
                    xtype: 'textfield'
                }
                ]
            },	{
                columnWidth:.5,
                layout: 'form',
                border:false,
                items:[	{
                    xtype: 'datefield',
                    width: 200,
                    allowBlank: false,
                    fieldLabel: 'Desde',
                    name: 'startdt',
                    id: 'startdt',
                    vtype: 'daterange',
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    endDateField: 'enddt' // id of the end date field
                },{
                    xtype: 'datefield',
                    width: 200,
                    allowBlank: false,
                    fieldLabel: 'Hasta',
                    name: 'enddt',
                    id: 'enddt',
                    vtype: 'daterange',
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    startDateField: 'startdt' // id of the start date field
                }
                ]
            }]
        }]
    });

    Conciliations.filterForm.addButton({
        text : 'Limpiar filtro',
        disabled : false,
        formBind: true,
        handler : function() {
            Conciliations.filterForm.getForm().reset();
            conciliationsDataStore.baseParams = {
                dateStart: '1900-01-01',
                dateEnd: '1900-01-01',
                transport: 0,
                voucher: ''
            //province: 0,
            //motive: 0
            };
            sm2.clearSelections();
            conciliationsDataStore.load();
        }
    });

    /*
    * A�adimos el bot�n para filtrar
    */
    Conciliations.filterForm.addButton({
        text : 'Filtrar',
        disabled : false,
        formBind: true,
        handler : function() {
            var startDate = Conciliations.filterForm.findById('startdt').getValue();
            var endDate = Conciliations.filterForm.findById('enddt').getValue();
            conciliationsDataStore.baseParams = {
                dateStart: startDate.dateFormat('Y-m-d'),
                dateEnd: endDate.dateFormat('Y-m-d'),
                transport: Conciliations.filterForm.findById('filter_transport_id').getValue(),
                voucher: Conciliations.filterForm.findById('filter_voucher').getValue()
            };
            conciliationsDataStore.load();
        }
    });    

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Conciliations.filterForm.render(Ext.get('conciliation_grid'));
    Conciliations.conciliationsGrid.render(Ext.get('conciliation_grid'));
});