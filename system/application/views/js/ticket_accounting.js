////////
var accountingDataStore;
var array;

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

var dataRecordTransport = new Ext.data.Record.create([
				        {name: 'transport_id', type: 'int'},
				        {name: 'transport_name', type: 'string'}
				    ]);

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
var dataProxyTransport = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_tickettransports/setDataGrid',
        method: 'POST'
    });
    
var dataReaderTransport = new Ext.data.JsonReader({root:'data'},dataRecordTransport);

var dataStoreTransport = new Ext.data.Store({
        id: 'transportsDS',
        proxy: dataProxyTransport,
        reader: dataReaderTransport
    });		
    
var dataRecordCenter= new Ext.data.Record.create([
						{name:'center_id'},
						{name:'center_name'}
					]);

var dataReaderCenter = new Ext.data.JsonReader({root:'data'},dataRecordCenter);

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
						{name:'motive_id'},
						{name:'motive_name'}
					]);

var dataReaderMotive = new Ext.data.JsonReader({root:'data'},dataRecordMotive);

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
    Ext.namespace('Accounting');
    

   	var xg = Ext.grid;
   
    /*
     * Definimos el registro
     */
     
    Accounting.accountingRecord = new Ext.data.Record.create([
        {name: 'request_id', type: 'int'},
        {name: 'viazul_voucher', type: 'string'},
        {name: 'person_nameworker', type: 'string'},
        {name: 'viazul_price', type: 'float'},
        {name: 'person_namelicensedby', type: 'string'},
        {name: 'request_details', type: 'string'},
        {name: 'center_name', type: 'string'},
        {name: 'ticket_date'}
    ]);
    
	var p = new Ext.Panel({
        title: 'Contabilidad -> Pasaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });

    /*
     * Creamos el reader para el Grid
     */
    Accounting.accountingGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'request_id'},
        Accounting.accountingRecord
    );
    
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><b>Detalle:</b> {request_details}</p>'
        )
    });

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Accounting.accountingDataProxy = new Ext.data.HttpProxy({
       url: baseUrl+'index.php/ticket/ticket_accounting/setDataGrid/true/no',
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
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },{
	   		id: 'viazul_voucher',
            name: 'viazul_voucher',
            header: 'Voucher',
            width: 80,
            dataIndex: 'viazul_voucher',
            sortable: false
        },{
	   		id: 'person_nameworker',
            name: 'person_nameworker',
            header: 'Nombre y Apellidos',
            width: 180,
            dataIndex: 'person_nameworker',
            sortable: true
        },{
            id: 'viazul_price',
            name : 'viazul_price',
            header: 'Importe',
            width: 80,
            renderer: 'usMoney',
            dataIndex: 'viazul_price',
            sortable: false
        },{
	   		id: 'person_namelicensedby',
            name: 'person_namelicensedby',
            header: 'Autoriza',
            width: 180,
            dataIndex: 'person_namelicensedby',
            sortable: true
        },{
            id: 'center_name',
            name : 'center_name',
            header: 'Presupuesto',
            width: 180,
            dataIndex: 'center_name',
            sortable: true
        },{
            id: 'ticket_date',
            name : 'ticket_date',
            header: 'Salida',
            width: 70,
            dataIndex: 'ticket_date',
            sortable: true
        }]
    );

    /*
     * Creamos el grid
     */
    Accounting.accountingGrid = new xg.GridPanel({
        id : 'ctr-accounting-grid',
        store : accountingDataStore,
        cm : Accounting.accountingColumnMode,
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
          //  ref: '../exportButton', //para hacer referencia a este boton en otro lugar en este caso desde el 
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function(){
				
            	 //   arreglo = sm2.getSelections();
				 //   if (arreglo.length > 0) {
					if (accountingDataStore.getCount() > 0 ){
						
						Accounting.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_accounting/setDataGrid/true/si';
						Accounting.filterForm.getForm().getEl().dom.method = 'POST';
						Accounting.filterForm.getForm().submit();
                         //el codigo anterior es si el standarSubmit del fomulario esta en true, sino seria la linea de abajo, pero en este caso hace falyta que no devuelva nada sino vaya al controlador y ejecute la funcion y ya(ext opor defecto todo es ajax...)                      
					      //Requests.filterForm.getForm().submit({url : baseUrl+'index.php/report/reports/reportInternalTicket/false/si/' });
	            	  //exportarPDF();
					} else {
						Ext.Msg.alert('Mensaje','No hay datos que exportar!');
					}
				   // }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: accountingDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
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

	Accounting.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
		standardSubmit:true,
        frame: true,
        monitorValid: true,
        labelWidth: 140,
        height: 130,
        width: 750,
        items:[{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items: [	new Ext.form.ComboBox({
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
		      			}),	{
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
				            endDateField: 'enddt'
				        },{
				            xtype: 'datefield',
				            width: 200,
				            allowBlank: false,
				            fieldLabel: 'Hasta',
				            format: 'Y-m-d',
				            name: 'enddt',
							hiddenName: 'enddt',
				            id: 'enddt',
				            vtype: 'daterange',
				            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
				            startDateField: 'startdt'
				        }
		              ]
		    },{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items: [		new Ext.form.ComboBox({
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

	Accounting.filterForm.addButton({
       text : 'Limpiar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Accounting.filterForm.getForm().reset();
           accountingDataStore.baseParams = {
               dateStart: '',
               dateEnd: '',
               transport: 0,
               center: 0,
               motive: 0
           };
           accountingDataStore.load({params: {start:0,limit:15}});
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Accounting.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Accounting.filterForm.findById('startdt').getValue();
           	var endDate = Accounting.filterForm.findById('enddt').getValue();
			var transport = Accounting.filterForm.findById('filter_transport_id').getValue();
			var motive = Accounting.filterForm.findById('filter_motive_id').getValue();
			var center = Accounting.filterForm.findById('filter_center_id').getValue();
           	accountingDataStore.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d'),
				transport: transport,
				center: center,
				motive: motive
           	};
           	accountingDataStore.load({params: {start:0,limit:15}});
       	}
   	});    

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Accounting.filterForm.render(Ext.get('accounting_grid'));
	Accounting.accountingGrid.render(Ext.get('accounting_grid'));
	//accountingDataStore.load({params: {start:0,limit:30}});
});
