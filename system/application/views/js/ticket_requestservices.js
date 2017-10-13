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

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Services');
    

   	var xg = Ext.grid;
   	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Services.servicesGrid.removeButton.enable();
                } else {
                    Services.servicesGrid.removeButton.disable();
                }
            }
        }
    });
    
    /*
     * Definimos el registro
     */
     
    Services.servicesRecord = new Ext.data.Record.create([
        {name: 'service_id', type: 'int'},
        {name: 'service_capacity', type: 'int'},
        {name: 'service_date'},
        {name: 'service_hour'},
        {name: 'service_itinerary', type: 'string'},
        {name: 'supplier_name', type: 'string'},
        {name: 'province_nameexit', type: 'string'},
        {name: 'province_namelunch', type: 'string'},
        {name: 'province_namearrival', type: 'string'}
    ]);
    
	var p = new Ext.Panel({
        title: 'Pasaje -> Solicitud de Servicios',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    

   
    /*
     * Creamos el reader para el Grid de cadenas serviceeras
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
        url: baseUrl+'index.php/ticket/ticket_requestservices/setDataGrid',
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
       	sm2,
       {
            id: 'service_id',
            name : 'service_id',
            dataIndex: 'service_id',
            header: 'No. Servicio',
            width: 80
        },	{
	   		id: 'service_date',
            name: 'service_date',
            header: 'Fecha',
            format: 'dd-mm-YYYY',
            width: 80,
            dataIndex: 'service_date',
            sortable: true
        },	{
	   		id: 'service_capacity',
            name: 'service_capacity',
            header: 'Capacidad',
            width: 80,
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
            id: 'service_hour',
            name : 'service_hour',
            header: 'Hora',
            width: 50,
            dataIndex: 'service_hour',
            sortable: false
        },	{
            id: 'province_nameexit',
            name : 'province_nameexit',
            header: "Salida",
            width: 130,
            dataIndex: 'province_nameexit',
            sortable: true
        },	/*{
	   		id: 'province_namelunch',
            name: 'province_namelunch',
			header: "Almuerzo",
			width: 130,
			dataIndex: 'province_namelunch',
			sortable: true
		},*/{
	   		id: 'province_namearrival',
            name: 'province_namearrival',
			header: "Llegada",
			width: 130,
			dataIndex: 'province_namearrival',
			sortable: true
		},	{
            id: 'service_itinerary',
            name : 'service_itinerary',
            header: 'Itinerario',
            width: 250,
            dataIndex: 'service_itinerary',
            sortable: false
        },	{
	   		id: 'supplier_name',
            name: 'supplier_name',
			header: "Proveedor",
			width: 90,
			dataIndex: 'supplier_name',
			sortable: true
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
            text:'Agregar',
            tooltip:'Agregar servicio',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Servicio seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Servicio(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: servicesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
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
           servicesDataStore.load({params: {start:0,limit:15}});
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
           	servicesDataStore.load({params: {start:0,limit:15}});
       	}
   	});    
    

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Services.servicesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = servicesDataStore.getAt(row).data.service_id;
        update_ventana(selectedId);
    });
    
    function concat (text){
		var init = Services.Form.findById('frm_service_itinerary').getValue();
		return init + ' ' + text;
	}
    
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Services.filterForm.render(Ext.get('services_grid'));
	Services.servicesGrid.render(Ext.get('services_grid'));
    servicesDataStore.load({params: {start:0,limit:15}});

	function update_ventana(id){
	
		recordUpdate = new Ext.data.Record.create([
	        {name: 'service_id', type: 'int'},
	        {name: 'service_capacity', type: 'int'},
	        {name: 'service_date'},
	        {name: 'service_hour'},
	        {name: 'service_itinerary', type: 'string'},
	        {name: 'supplier_id', type: 'int'},
	        {name: 'province_idexit', type: 'int'},
	        {name: 'province_idlunch', type: 'int'},
	        {name: 'province_idarrival', type: 'int'},
	        {name: 'service_details', type: 'string'},
	        {name: 'place_exit', type: 'string'},
	        {name: 'place_lunch', type: 'string'},
	        {name: 'place_arrival', type: 'string'},
	        {name: 'service_amount', type: 'float'},
	        {name: 'service_costcenter', type: 'string'}
	    ]);
	    
		formReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'service_id'
	        },recordUpdate
	    );
	    
	    var updateWindow;
	
		var updateForm = new Ext.FormPanel({
	        id: 'form-services',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        labelWidth: 115,
	        standardSubmit:true,
	        width: 660,
	        minWidth: 660,
	        height: 330,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: formReader,
	        items:[{
	            layout:'column',
	            border: true,
	            items:[{
			      columnWidth:.5,
			      layout: 'form',
			      border:false,
			      items:[	new Ext.form.ComboBox({
			           			store: dataStoreSupplier,
			           			fieldLabel: 'Proveedores',
			           			displayField: 'supplier_name',
			           			valueField: 'supplier_id',
			           			hiddenName: 'supplier_id',
			           			id: 'upd_supplier_id',
			           			allowBlank: false,
							    autoload: true,
			           			typeAhead: true,
			           			mode: 'local',
			           			triggerAction: 'all',
			           			emptyText: 'Seleccione un Proveedor...',
			           			selectOnFocus: true,
			           			width: 170,
			           			listeners: {
									'blur': function(){
										var flag = dataStoreSupplier.findExact( 'supplier_id', Ext.getCmp('upd_supplier_id').getValue());
							    		if (flag == -1){
							    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
							    			Ext.getCmp('upd_supplier_id').reset();
							    			return false;
							    		}
									}
						 		}
			                }),	{
					            fieldLabel : 'Capacidad',
					            id: 'upd_service_capacity',
					            name : 'service_capacity',
					            allowBlank:false,
					            width: 100,
					            xtype: 'numberfield'
					        },	{
					            fieldLabel : 'Fecha',
					            id: 'upd_service_date',
					            name : 'service_date',
					            allowBlank:false,
					            width: 180,
					            format: 'Y-m-d',
					            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
					            xtype: 'datefield'
					        }, 	new Ext.form.TimeField({
							    minValue: '04:00',
							    maxValue: '23:00',
							    allowBlank:false,
							    increment: 30,
							    format: 'H:i',
							    width: 75,
			           			displayField: 'service_hour',
			           			valueField: 'service_hour',
			           			hiddenName: 'service_hour',
			           			fieldLabel: 'Hora salida',
							    id: 'upd_service_hour',
					            name : 'service_hour'
							}), {
					            fieldLabel : 'Importe Estimado',
					            id: 'upd_service_amount',
					            name : 'service_amount',
					            allowBlank:false,
					            width: 100,
					            xtype: 'numberfield'
					        },	{
					            fieldLabel : 'Detalle',
					            id: 'upd_service_details',
					            name : 'service_details',
					            allowBlank: true,
					            height: 35,
					            width: 170,
					            xtype: 'textarea'
					        }, 	{
					            id: 'upd_service_id',
					            name : 'service_id',
					            xtype: 'hidden'
					        }
			             ]
			    },{
			      columnWidth:.5,
			      layout: 'form',
			      border:false,
			      items: [	new Ext.form.ComboBox({
			           			store: dataStoreProv,
			           			fieldLabel: 'Provincia Salida',
			           			displayField: 'province_name',
			           			valueField: 'province_id',
			           			hiddenName: 'province_idexit',
			           			allowBlank: false,
							    autoload: true,
			           			typeAhead: true,
			           			mode: 'local',
			           			triggerAction: 'all',
			           			emptyText: 'Seleccione una Provincia...',
			           			selectOnFocus: true,
			           			width: 170,
							    id: 'upd_province_idexit',
					            name : 'province_idexit',
					            listeners: {
									'blur': function(){
										var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idexit').getValue());
							    		if (flag == -1){
							    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
							    			Ext.getCmp('upd_province_idexit').reset();
							    			return false;
							    		}
									}
						 		}
			                }),	{
					            fieldLabel : 'Lugar',
					            id: 'frm_place_exit',
					            name : 'place_exit',
					            width: 170,
					            allowBlank: true,
					            xtype: 'textfield'
					        }, 	new Ext.form.ComboBox({
			          			store: dataStoreProv,
			          			fieldLabel: 'Provincia Almuerzo',
			          			displayField: 'province_name',
			          			valueField: 'province_id',
			          			hiddenName: 'province_idlunch',
			          			allowBlank: true,
			          			autoload: true,
			          			typeAhead: true,
			          			mode: 'local',
			          			triggerAction: 'all',
			          			emptyText: 'Seleccione una Provincia...',
			          			selectOnFocus: true,
			          			width: 170,
							    id: 'upd_province_idlunch',
					            name : 'province_idlunch',
					            listeners: {
									'blur': function(){
										var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idlunch').getValue());
							    		if (flag == -1){
							    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
							    			Ext.getCmp('upd_province_idlunch').reset();
							    			return false;
							    		}
									}
						 		}
			                }),	{
					            fieldLabel : 'Lugar',
					            id: 'frm_place_lunch',
					            name : 'place_lunch',
					            width: 170,
					            allowBlank: true,
					            xtype: 'textfield'
					        }, 	new Ext.form.ComboBox({
			           			store: dataStoreProv,
			           			fieldLabel: 'Provincia Destino',
			           			displayField: 'province_name',
			           			valueField: 'province_id',
			           			hiddenName: 'province_idarrival',
			           			allowBlank: false,
							    autoload: true,
			           			typeAhead: true,
			           			mode: 'local',
			           			triggerAction: 'all',
			           			emptyText: 'Seleccione una Provincia...',
			           			selectOnFocus: true,
			           			width: 170,
							    id: 'upd_province_idarrival',
					            name : 'province_idarrival',
					            listeners: {
									'blur': function(){
										var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idarrival').getValue());
							    		if (flag == -1){
							    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
							    			Ext.getCmp('upd_province_idarrival').reset();
							    			return false;
							    		}
									}
						 		}
			                }),	{
					            fieldLabel : 'Lugar',
					            id: 'frm_place_arrival',
					            name : 'place_arrival',
					            width: 170,
					            allowBlank: true,
					            xtype: 'textfield'
					        } 	
			             ]
	            }]
	            
	        },{
	            fieldLabel : 'Presupuesto',
	            id: 'frm_service_costcenter',
	            name : 'service_costcenter',
	            width: 400,
	            allowBlank: false,
	            xtype: 'textfield'
	        },	{
	            fieldLabel : 'Itinerario',
	            id: 'upd_service_itinerary',
	            name : 'service_itinerary',
	            allowBlank: true,
	            height: 35,
	            width: 400,
	            xtype: 'textarea'
	        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    updateForm.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	           /* updateForm.getForm().submit({
	                url : baseUrl+'index.php/ticket/ticket_requestservices/insert',
	                waitMsg : 'Salvando datos...',
	                failure: function (form, action) {
	                    Ext.MessageBox.show({
	                        title: 'Error al salvar los datos',
	                        msg: 'Error al salvar los datos.',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.ERROR
	                    });
	                    sm2.clearSelections();
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
	                    sm2.clearSelections();
	                    servicesDataStore.load({params: {start:0,limit:30}});
	                }
	            });*/
			   	updateForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_requestservices/insert';
	           	updateForm.getForm().getEl().dom.method = 'POST';
               	updateForm.getForm().submit();
               	//servicesDataStore.load({params: {start:0,limit:15}});
				updateWindow.destroy();
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    updateForm.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	            updateForm.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	    var title = 'Agregar ';
		if (id > 0){
			title = 'Editar ';
			updateForm.load({url:baseUrl+'index.php/ticket/ticket_requestservices/getById/'+id});
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title+'Solicitud de Servicio',
				layout:'form',
				top: 200,
				width: 685,
				height: 370,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: updateForm
				
				});
			}
		updateWindow.show(this);
	
	}
    
});


function delRecords(btn) {
	if (btn == 'yes') {
		for (var i = 0, len = array.length; i < len; i++) {
	        Ext.Ajax.request({
			   url: baseUrl+'index.php/ticket/ticket_requestservices/delete/'+array[i].get('service_id'),
			   method: 'GET',
			   disableCaching: false,
			   success: function(){
			   		Ext.MessageBox.show({
	                       title: 'Datos eliminados correctamente',
	                       msg: 'Datos eliminados correctamente',
	                       width: 300,
	                       buttons: Ext.MessageBox.OK,
	                       icon: Ext.MessageBox.INFO
	                   });
			   },
			   failure: function(){
			   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Servicio.');
			   }
			});
		}
		sm2.clearSelections();
		servicesDataStore.load({params: {start:0,limit:15}});
  	}
}