var servicesDataStore;
var array, sm2;

var dataRecordProv = new Ext.data.Record.create([
						{name:'province_id'},
						{name:'province_name'}
					]);
var dataReaderProv = new Ext.data.JsonReader({root:'data'},dataRecordProv);
var dataProxyProv = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_provinces/setDataGrid',
						method: 'POST'
					});
var dataStoreProv = new Ext.data.Store({
						proxy: dataProxyProv,
						reader: dataReaderProv,
						autoLoad: true
						});
						
var dataRecordSupplier = new Ext.data.Record.create([
						{name:'supplier_id'},
						{name:'supplier_name'}
					]);
var dataReaderSupplier = new Ext.data.JsonReader({root:'data'},dataRecordSupplier);
var dataProxySupplier = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_transportsuppliers/setDataGrid',
						method: 'POST'
					});
var dataStoreSupplier = new Ext.data.Store({
						proxy: dataProxySupplier,
						reader: dataReaderSupplier,
						autoLoad: true
						});	

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

Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    Ext.form.Field.prototype.msgTarget = 'side';
	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Services');
    

   	var xg = Ext.grid;
    
    /*
     * Definimos el registro
     */
     
    Services.servicesRecord = new Ext.data.Record.create([
        {name: 'service_id', type: 'int'},
        {name: 'service_capacity', type: 'int'},
        {name: 'service_date'},
        {name: 'service_details', type: 'string'},
        {name: 'service_amount', type: 'float'},
        {name: 'supplier_name', type: 'string'},
        {name: 'province_nameexit', type: 'string'},
        {name: 'province_namelunch', type: 'string'},
        {name: 'province_namearrival', type: 'string'}

    ]);
    
	var p = new Ext.Panel({
        title: 'Contabilidad -> Contabilidad de Servicios Solicitados',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><b>Detalle:</b> {service_details}</p>'
        )
    });
   
    /*
     * Creamos el reader para el Grid
     */
    Services.servicesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'service_id'},
        Services.servicesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Services.servicesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/ticket/ticket_requestservices/getDataAccounting',
        method: 'POST'
    });

    servicesDataStore = new Ext.data.Store({
        id: 'servicesDS',
        proxy: Services.servicesDataProxy,
        reader: Services.servicesGridReader
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Services.servicesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
        expander,
       {
            id: 'service_id',
            name : 'service_id',
            dataIndex: 'service_id',
            hidden: true
        },{
	   		id: 'service_capacity',
            name: 'service_capacity',
            header: 'Capacidad',
            width: 70,
            dataIndex: 'service_capacity',
            sortable: true
        },	{
	   		id: 'service_date',
            name: 'service_date',
            header: 'Fecha',
            format: 'dd-mm-YYYY',
            width: 80,
            dataIndex: 'service_date',
            sortable: true
        },	{
	   		id: 'supplier_name',
            name: 'supplier_name',
			header: "Proveedor",
			width: 90,
			dataIndex: 'supplier_name',
			sortable: true
		},	{
            id: 'province_nameexit',
            name : 'province_nameexit',
            header: "Salida",
            width: 130,
            dataIndex: 'province_nameexit',
            sortable: true
        },	{
	   		id: 'province_namelunch',
            name: 'province_namelunch',
			header: "Almuerzo",
			width: 130,
			dataIndex: 'province_namelunch',
			sortable: true
		},	{
	   		id: 'province_namearrival',
            name: 'province_namearrival',
			header: "Llegada",
			width: 130,
			dataIndex: 'province_namearrival',
			sortable: true
		},	{
            id: 'service_amount',
            name : 'service_amount',
            header: 'Importe',
            renderer: 'usMoney',
            width: 80,
            dataIndex: 'service_amount',
            sortable: false
        }]
    );

    /*
     * Creamos el grid de movimientos
     */
    Services.servicesGrid = new xg.GridPanel({
        id : 'ctr-services-grid',
        store : servicesDataStore,
        cm : Services.servicesColumnMode,
        viewConfig: {
            forceFit:false
        },
        columnLines: true,
        stripeRows: true,
        frame:true,
        collapsible: true,
        width : 750,
        height : 380,
		tbar:[{
            text:'Exportar a pdf',
            tooltip:'Exportar a pdf',
            iconCls:'pdf',
            disabled: false,
            handler: function(){
					if (servicesDataStore.getCount() > 0 ){
						Services.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_requestservices/accountingPdf';
						Services.filterForm.getForm().getEl().dom.method = 'POST';
						Services.filterForm.getForm().submit();

					} else {
						Ext.Msg.alert('Mensaje','No hay datos que exportar!');
					}
            }
        }],
        bbar: new Ext.PagingToolbar({
            //pageSize: 15,
            store: servicesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    });
    
	Services.filterForm = new Ext.FormPanel({
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
                	new Ext.form.ComboBox({
		 			store: dataStoreSupplier,
		   			fieldLabel: 'Proveedores',
		   			displayField: 'supplier_name',
		   			valueField: 'supplier_id',
		   			hiddenName: 'supplier_id',
		   			id: 'filter_supplier_id',
		   			allowBlank: true,
				    autoload: true,
		   			typeAhead: true,
		   			mode: 'local',
		   			triggerAction: 'all',
		   			emptyText: 'Seleccione un Proveedor...',
		   			selectOnFocus: true,
		   			width: 170,
		   			listeners: {
						'blur': function(){
							var flag = dataStoreSupplier.findExact( 'supplier_id', Ext.getCmp('filter_supplier_id').getValue());
				    		if (flag == -1){
				    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
				    			Ext.getCmp('filter_supplier_id').reset();
				    			return false;
				    		}
						}
			 		}
		        }),	{
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
		        },	{
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

	Services.filterForm.addButton({
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Services.filterForm.getForm().reset();
           servicesDataStore.baseParams = {
				dateStart: '',
				dateEnd: '',
				supplier: 0
           };
           servicesDataStore.load();
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Services.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Ext.getCmp('startdt').getValue() != '' ? Ext.getCmp('startdt').getValue().dateFormat('Y-m-d') : '';
           	var endDate = Ext.getCmp('enddt').getValue() != '' ? Ext.getCmp('enddt').getValue().dateFormat('Y-m-d') : '';
			var supplier = Ext.getCmp('filter_supplier_id').getValue();
			servicesDataStore.baseParams = {
				dateStart: startDate,
				dateEnd: endDate,
				supplier: supplier
           	};
           	servicesDataStore.load();
       	}
   	});
    
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Services.filterForm.render(Ext.get('services_grid'));
	Services.servicesGrid.render(Ext.get('services_grid'));

    
});
