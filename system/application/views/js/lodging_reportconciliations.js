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
    autoLoad:true
});
 						
var dataRecordHotel = new Ext.data.Record.create([
{
    name:'hotel_id'
},
{
    name:'hotel_name'
}
]);

var dataReaderHotel = new Ext.data.JsonReader({
    root:'data'
},dataRecordHotel);

var dataProxyHotel = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_hotels/setDataGrid',
    method: 'POST'
});

var dataStoreHotel = new Ext.data.Store({
    proxy: dataProxyHotel,
    reader: dataReaderHotel
//autoLoad:true
});						
 							
var dataRecordCenter= new Ext.data.Record.create([
{
    name:'center_id'
},
{
    name:'center_name'
}
]);

var dataReaderCenter = new Ext.data.JsonReader({
    root:'data'
},dataRecordCenter);

var dataProxyCenter = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_costcenters/setDataGrid',
    method: 'POST'
});

var dataStoreCenter= new Ext.data.Store({
    proxy: dataProxyCenter,
    reader: dataReaderCenter,
    autoLoad:true
});

var dataRecordMotive= new Ext.data.Record.create([
{
    name:'motive_id'
},
{
    name:'motive_name'
}
]);

var dataReaderMotive = new Ext.data.JsonReader({
    root:'data'
},dataRecordMotive);

var dataProxyMotive = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_motives/setDataGrid',
    method: 'POST'
});

var dataStoreMotive= new Ext.data.Store({
    proxy: dataProxyMotive,
    reader: dataReaderMotive,
    autoLoad:true
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
   	
    sm2 = new xg.CheckboxSelectionModel({});
    
    var p = new Ext.Panel({
        title: 'Hospedaje -> Reporte de Conciliaci&oacute;n Hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
    }
    });
	
    function conciliation(val){
        if(val == 'NO'){
            return '<span style="color:red;">' + val + '</span>';
        }else {
            return '<span style="color:green;">' + val + '</span>';
        }
        return val;
    }
    
    
    /*
     * Definimos el registro
     */
     
    Conciliations.conciliationsRecord = new Ext.data.Record.create([
    {
        name: 'request_id'
    },

    {
        name: 'bill_number'
    },

    {
        name: 'person_licensedby'
    },

    {
        name: 'lodging_entrancedate'
    },

    {
        name: 'lodging_exitdate'
    },

    {
        name: 'conciliation_pay'
    },

    {
        name: 'center_name'
    },

    {
        name: 'person_worker'
    },

    {
        name: 'person_identity'
    },

    {
        name: 'request_details', 
        type: 'string'
    },	

    {
        name: 'province_lodging'
    },

    {
        name: 'hotel_name'
    },

    {
        name: 'diet'
    },

    {
        name: 'lodging'
    },

    {
        name: 'total'
    } 
		
    ]);


    /*
     * Creamos el reader para el Grid
     */
    Conciliations.conciliationsGridReader = new Ext.data.JsonReader({
        root: 'data',
        totalProperty: 'count',
        id: 'request_id'
    },
    Conciliations.conciliationsRecord
    );
    
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><b>Detalle:</b> {request_details}</p>'
            )
    });

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Conciliations.conciliationsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/lodging/lodging_conciliations/setDataCociliation/true/no',
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
        expander,
        {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },	{
            id: 'bill_number',
            name: 'bill_number',
            header: "Factura",
            width: 90,
            dataIndex: 'bill_number',
            sortable: true
        },	{
            id: 'conciliation_pay',
            name: 'conciliation_pay',
            header: "Facturado",
            width: 80,
            renderer: conciliation,
            dataIndex: 'conciliation_pay',
            sortable: true
        },	{
            id: 'person_identity',
            name: 'person_identity',
            header: "CI",
            width: 90,
            dataIndex: 'person_identity',
            sortable: true
        },	{
            id: 'person_worker',
            name: 'person_worker',
            header: "Trabajador",
            width: 180,
            dataIndex: 'person_worker',
            sortable: true
        },	{
            id: 'center_name',
            name : 'center_name',
            header: "Centro de Costo",
            width: 130,
            dataIndex: 'center_name',
            sortable: true
        },	{
            id: 'person_licensedby',
            name : 'person_licensedby',
            header: "Autoriza",
            width: 130,
            dataIndex: 'person_licensedby',
            sortable: true
        },	{
            id: 'province_lodging',
            name: 'province_lodging',
            header: "Prov. Hosp.",
            width: 130,
            dataIndex: 'province_lodging',
            sortable: true
        },	{
            id: 'hotel_name',
            name: 'hotel_name',
            header: "Hotel",
            width: 130,
            dataIndex: 'hotel_name',
            sortable: true
        },	{
            id: 'lodging_entrancedate',
            name: 'lodging_entrancedate',
            header: 'Entrada',
            width: 80,
            dataIndex: 'lodging_entrancedate',
            sortable: true
        },	{
            id: 'lodging_exitdate',
            name : 'lodging_exitdate',
            header: 'Salida',
            width: 80,
            dataIndex: 'lodging_exitdate',
            sortable: false
        },	{
            id: 'diet',
            name : 'diet',
            header: 'Dieta',
            width: 50,
            renderer: 'usMoney',
            dataIndex: 'diet',
            sortable: false
        },	{
            id: 'lodging',
            name : 'lodging',
            header: 'Hospedaje',
            width: 60,
            renderer: 'usMoney',
            dataIndex: 'lodging',
            sortable: false
        },	{
            id: 'total',
            name : 'total',
            header: 'Total',
            width: 60,
            renderer: 'usMoney',
            dataIndex: 'total',
            sortable: false
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
        plugins: expander,
        tbar:[{
            text:'Exportar a pdf',
            tooltip:'Exportar a pdf',
            iconCls:'pdf',
            disabled: false,
            handler: function(){
                if (conciliationsDataStore.getCount()>0 ){
                    array = sm2.getSelections();
                    var count = array.length; 
                    if (count > 0) {
                        var conciliados = array[0].get('request_id');
                        for (var i = 1, len = count; i < len; i++) {
                            conciliados = conciliados + "-" + array[i].get('request_id');
                        }
                        Conciliations.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_conciliations/setPdfCociliation/'+conciliados;
                        Conciliations.filterForm.getForm().getEl().dom.method = 'POST';
                        Conciliations.filterForm.getForm().submit();
                    } else {
                        Conciliations.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_conciliations/setDataCociliation/true/si';
                        Conciliations.filterForm.getForm().getEl().dom.method = 'POST';
                        Conciliations.filterForm.getForm().submit();
                    }
        	 				

                }
                else{
                    Ext.Msg.alert('Mensaje','No hay datos que exportar!');
                }
                sm2.clearSelections();
                conciliationsDataStore.load();	
            }
        },'-',{
            text:'Exportar a excel',
            tooltip:'Exportar a excel',
            iconCls:'xls',
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function(){
                if (conciliationsDataStore.getCount()>0 ){
                    array = sm2.getSelections();
                    var count = array.length; 
                    if (count > 0) {
                        var conciliados = array[0].get('request_id');
                        for (var i = 1, len = count; i < len; i++) {
                            conciliados = conciliados + "-" + array[i].get('request_id');
                        }
                        Conciliations.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_conciliations/to_excel/'+conciliados;
                        Conciliations.filterForm.getForm().getEl().dom.method = 'POST';
                        Conciliations.filterForm.getForm().submit();
                    } else {
                        Conciliations.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_conciliations/to_excelAll';
                        Conciliations.filterForm.getForm().getEl().dom.method = 'POST';
                        Conciliations.filterForm.getForm().submit();
                    }
			 				
			
                }
                sm2.clearSelections();
                conciliationsDataStore.load();	
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 100,
            store: conciliationsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    Conciliations.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 140,
        height: 160,
        width: 750,
        items: [{
            layout:'column',
            border:false,
            items:[{
                columnWidth:.5,
                layout: 'form',
                border:false,
                items:[	new Ext.form.ComboBox({
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
                    name : 'filter_province_id',
                    listeners: {
                        'select': function(){
                            dataStoreHotel.baseParams = {
                                province_id: Ext.getCmp('filter_province_id').getValue()
                            };
                            dataStoreHotel.load();
                        },
                        'blur': function(){
                            var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_id').getValue());
                            if (flag == -1){
                                Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
                                Ext.getCmp('filter_province_id').reset();
                                return false;
                            }
                        }
                    }
                }),	new Ext.form.ComboBox({
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
                    name : 'filter_hotel_id',
                    listeners: {
                        'blur': function(){
                            var flag = dataStoreHotel.findExact( 'hotel_id', Ext.getCmp('filter_hotel_id').getValue());
                            if (flag == -1){
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
                },{
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
            },{
                columnWidth:.5,
                layout: 'form',
                border:false,
                items:[	new Ext.form.ComboBox({
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
                    name : 'filter_motive_id',
                    listeners: {
                        'blur': function(){
                            var flag = dataStoreMotive.findExact( 'motive_id', Ext.getCmp('filter_motive_id').getValue());
                            if (flag == -1){
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('filter_motive_id').reset();
                                return false;
                            }
                        }
                    }
                }),	new Ext.form.ComboBox({
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
                    name : 'filter_center_id',
                    listeners: {
                        'blur': function(){
                            var flag = dataStoreCenter.findExact( 'center_id', Ext.getCmp('filter_center_id').getValue());
                            if (flag == -1){
                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                Ext.getCmp('filter_center_id').reset();
                                return false;
                            }
                        }
                    }
                })
                ]
            }]
        }]
    });

    Conciliations.filterForm.addButton({
        text : 'Limpiar filtro',
        formBind: true,
        handler : function() {
            Conciliations.filterForm.getForm().reset();
            conciliationsDataStore.baseParams = {
                dateStart: '1900-01-01',
                dateEnd: '1900-01-01',
                hotel: 0,
                province: 0,
                motive: 0,
                center: 0
            };
            sm2.clearSelections();
            conciliationsDataStore.load();
        }
    });

    /*
    * Anadimos el boton para filtrar
    */
    Conciliations.filterForm.addButton({
        text : 'Filtrar',
        disabled : false,
        formBind: true,
        handler : function() {
            var startDate = Conciliations.filterForm.findById('startdt').getValue();
            var endDate = Conciliations.filterForm.findById('enddt').getValue();
            var province = Conciliations.filterForm.findById('filter_province_id').getValue();
            var hotel = Conciliations.filterForm.findById('filter_hotel_id').getValue();
            var motive = Conciliations.filterForm.findById('filter_motive_id').getValue();
            var center = Conciliations.filterForm.findById('filter_center_id').getValue();
            conciliationsDataStore.baseParams = {
                dateStart: startDate.dateFormat('Y-m-d'),
                dateEnd: endDate.dateFormat('Y-m-d'),
                hotel: hotel,
                province: province,
                motive: motive,
                center: center
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
