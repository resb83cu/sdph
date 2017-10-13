var etecsaDataStore;
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

var dataRecordPers = new Ext.data.Record.create([
						{name:'person_id'},
						{name:'person_fullname'}
					]);
var dataReaderPers = new Ext.data.JsonReader({root:'data'},dataRecordPers);
var dataProxyPers = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/person/person_persons/setDataGrid',
						method: 'POST'
					});
var dataStorePers = new Ext.data.Store({
						proxy: dataProxyPers,
						reader: dataReaderPers
					});
						
var dataRecordState = new Ext.data.Record.create([
						{name:'state_id'},
						{name:'state_name'}
					]);
var dataReaderState = new Ext.data.JsonReader({root:'data'},dataRecordState);
var dataProxyState = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_ticketrequeststates/setDataGrid',
						method: 'POST'
					});
var dataStoreState = new Ext.data.Store({
						proxy: dataProxyState,
						reader: dataReaderState,
						autoLoad: true
					});	
					
var dataRecordMotive = new Ext.data.Record.create([
				        {name: 'motive_id', type: 'int'},
				        {name: 'motive_name', type: 'string'}
				    ]);
var dataRecordTransport = new Ext.data.Record.create([
				        {name: 'transport_id', type: 'int'},
				        {name: 'transport_name', type: 'string'}
				    ]);

    /*
     * Creamos el reader para el Grid de movimientos
     */
var dataReaderTransport = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'transport_id'},
        dataRecordTransport
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
var dataProxyTransport = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_tickettransports/setDataGrid',
        method: 'POST'
    });

var dataStoreTransport = new Ext.data.Store({
        id: 'transportsDS',
        proxy: dataProxyTransport,
        reader: dataReaderTransport
    });				    

var dataReaderMotive =  new Ext.data.JsonReader({root:'data'},dataRecordMotive);

var dataProxyMotive = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_motives/setDataGrid',
        method: 'POST'
    });

var dataStoreMotive = new Ext.data.Store({
        proxy: dataProxyMotive,
        reader: dataReaderMotive,
        autoLoad: true
    });	

Date.patterns = {
	    ISO8601Long:"Y-m-d H:i:s",
	    ISO8601Short:"Y-m-d",
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

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Etecsa');
    

   	var xg = Ext.grid;
   	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
	        selectionchange: function(sm) {
	            if (sm.getCount()) {
	            	Etecsa.etecsaGrid.pdfButton.enable();
	            } else {
	            	Etecsa.etecsaGrid.pdfButton.disable();
	            }
	        }
    	}
    });
    
	var p = new Ext.Panel({
        title: 'Pasaje -> Gestionar pasaje ETECSA',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
        
	function state(val){
        if((val != 'Cancelada') && (val != 'Denegada')){
            return '<span style="color:green;">' + val + '</span>';
        }else {
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }  
    
    /*
     * Definimos el registro
     */
     
    Etecsa.etecsaRecord = new Ext.data.Record.create([
        {name: 'request_id', type: 'int'},
        {name: 'request_date'},
        {name: 'ticket_date'},
        {name: 'person_worker', type: 'string'},
        {name: 'province_namefrom', type: 'string'},
        {name: 'province_nameto', type: 'string'},
        {name: 'state', type: 'string'}
    ]);


    /*
     * Creamos el reader para el Grid de cadenas serviceeras
     */
    Etecsa.etecsaGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count'},
        Etecsa.etecsaRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Etecsa.etecsaDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/ticket/ticket_editetecsa/setDataGrid',
        method: 'POST'
    });

    etecsaDataStore = new Ext.data.GroupingStore({
        id: 'etecsaDS',
        proxy: Etecsa.etecsaDataProxy,
        reader: Etecsa.etecsaGridReader,
        sortInfo:{field: 'ticket_date', direction: "ASC"},
		groupField:'state'
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Etecsa.etecsaColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },{
	   		id: 'state',
            name: 'state',
            renderer: state,
            header: 'Estado',
            width: 80,
            dataIndex: 'state',
            sortable: true
        },{
	   		id: 'ticket_date',
            name: 'ticket_date',
            header: 'Salida',
            width: 80,
            format: 'dd-mm-YYYY',
            dataIndex: 'ticket_date',
            sortable: true
        },{
	   		id: 'request_date',
            name: 'request_date',
            header: 'Solicitado',
            width: 120,
            format: 'dd-mm-YYYY',
            dataIndex: 'request_date',
            sortable: true
        },{
	   		id: 'person_worker',
            name: 'person_worker',
            header: 'Nombre(s) y Apellidos',
            width: 180,
            dataIndex: 'person_worker',
            sortable: true
        },{
            id: 'province_namefrom',
            name : 'province_namefrom',
            header: 'Origen',
            width: 140,
            dataIndex: 'province_namefrom',
            sortable: false
        },{
            id: 'province_nameto',
            name : 'province_nameto',
            header: 'Destino',
            width: 140,
            dataIndex: 'province_nameto',
            sortable: false
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Etecsa.etecsaGrid = new xg.GridPanel({
        id : 'ctr-etecsa-grid',
        store : etecsaDataStore,
        cm : Etecsa.etecsaColumnMode,
		view: new Ext.grid.GroupingView({
	        forceFit:true,
	        groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Pasajeros" : "Pasajero"]})'
        }),
        stripeRows: true,
        frame: true,
        collapsible: true,
        width : 750,
        height : 380,
        tbar:[	{
		            text:'Solicitud Servicio',
		            tooltip:'Hacer solicitud de servicio',
		            iconCls:'add',
		            handler: function(){
        						eventual_ventana();
		            }
		        },'-',{text:'Editar',
		            tooltip:'Editar pasajes(s) seleccionado(s)',
		            iconCls:'add',
		            ref: '../editButton',
		            //disabled: true,
		            handler: function(){
			            	    array = sm2.getSelections();
			            	    if (array.length > 0 && Ext.getCmp('filter_edit_state_id').getValue() != ''){
									for (var i = 0, len = array.length; i < len; i++) {
										var ticketdate = array[i].get('ticket_date');
								   		if (today > ticketdate && session_rollId < 5){
											Ext.MessageBox.alert('Error', 'No se puede editar este pasaje porque la fecha de viaje ya expir&oacute;.');
											return false;
							       		}
								        Ext.Ajax.request({
										   url: baseUrl+'index.php/ticket/ticket_editetecsa/insertMulti/'+array[i].get('request_id')+'/'+array[i].get('ticket_date')+'/'+Ext.getCmp('filter_edit_state_id').getValue(),
										   method: 'GET',
										   disableCaching: false,
										   success: function(){
										   },
										   failure: function(){
										   		Ext.MessageBox.alert('Error', 'No se pudo editar el pasaje.');
										   }
										});
								    }
									Ext.getCmp('filter_edit_state_id').setValue('');
								    sm2.clearSelections();
			            	    	var transportItinerary = Ext.getCmp('frm_transport_itinerary').getValue();
						           	var ticketDate = Ext.getCmp('frm_ticket_date').getValue();
									var motive = Ext.getCmp('filter_motive_id').getValue();
									var state = Ext.getCmp('filter_edit_state_id').getValue();
						           	etecsaDataStore.baseParams = {
						           		transport_itinerary: transportItinerary,
										ticket_date: ticketDate.dateFormat('Y-m-d'),
										motive: motive,
										state: state
						           	};
						           	Ext.getCmp('filter_edit_state_id').setValue('');
						           	etecsaDataStore.load({params: {start:0,limit:50}});
						           	Etecsa.etecsaGrid.getStore().reload();
			            	    
			            	    } else if (Ext.getCmp('filter_edit_state_id').getValue() == ''){
			            	    	Ext.MessageBox.alert('Mensaje', 'Debe seleccionar un estado de pasaje.');
			            	    } else if (array.length == 0 && Ext.getCmp('filter_edit_state_id').getValue() != ''){
			            	    	Ext.MessageBox.confirm('Mensaje', 'Usted no ha seleccionado ning&uacute;n pasaje. Desea filtrar los pasajes por este estado?', filter);
			            	    }
				            }
					}, new Ext.form.ComboBox({
		           			store: dataStoreState,
		           			fieldLabel: 'Estado',
		           			displayField: 'state_name',
		           			valueField: 'state_id',
		           			hiddenName: 'edit_state_id',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all', 					
		           			emptyText: 'Estado...',
		           			selectOnFocus: true,
							listeners: {
								'select': function(){
									Ext.getCmp('filter_edit_state_idHidden').setValue(Ext.getCmp('filter_edit_state_id').getValue());
								},
								'blur': function(){
									var flag = dataStoreState.findExact( 'state_id', Ext.getCmp('filter_edit_state_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_edit_state_id').reset();
						    			return false;
						    		}
								}
					 		},
		           			width: 100,
						    id: 'filter_edit_state_id',
				            name : 'edit_state_id'
	                }),'-',{
			            text:'Exportar a pdf',
			            tooltip:'Exportar a pdf',
			            iconCls:'pdf',
			            ref: '../pdfButton',
			            disabled: true,
			            handler: function(){
			        				array = sm2.getSelections();
									var len = array.length;
									id = array[0].get('request_id');
							        date = array[0].get('ticket_date');
									if (len > 1){
										Ext.MessageBox.alert('Error', 'Debe seleccionar un solo pasaje para mostrar su informaci&oacute;n.');
										sm2.clearSelections();
										return false;
									} else {
										Etecsa.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_editetecsa/etecsaPdf/'+id+'/'+date ;
										Etecsa.filterForm.getForm().getEl().dom.method = 'POST';
										Etecsa.filterForm.getForm().submit();							
									}
			            }
			        }
				],
        bbar: new Ext.PagingToolbar({
            pageSize: 50,
            store: etecsaDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

	Etecsa.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        monitorValid: true,
		standardSubmit:true,
        labelWidth: 160,
        height: 130,
        width: 750,
        items: [	new Ext.form.ComboBox({
		   			store:  ['Santiago-Habana','Habana-Santiago'],
		   			fieldLabel: 'Itinerario',
		   			displayField: 'transport_itinerary',
		   			valueField: 'transport_itinerary',
		   			allowBlank: false,
		   			typeAhead: true,
		   			readOnly: true,
		   			mode: 'local',
		   			triggerAction: 'all',
		   			emptyText: 'Seleccione un itinerario...',
		   			selectOnFocus: true,
		   			width: 200,
					id: 'frm_transport_itinerary',
					hiddenName: 'transport_itinerary',
					name : 'transport_itinerary',
					listeners: {
						'select' : function() {
							var id = Ext.getCmp('frm_transport_itinerary').getValue();
					       	if (id == 'Santiago-Habana'){
						      	Ext.getCmp('frm_ticket_date').enable();
							 	Ext.getCmp('frm_ticket_date').setDisabledDays(['1', '2', '3', '4', '5', '6']);
							}else if (id == 'Habana-Santiago'){
						      	Ext.getCmp('frm_ticket_date').enable();
							 	Ext.getCmp('frm_ticket_date').setDisabledDays(['0', '1', '2', '3', '4', '5']);
							}
					  	}
				 	}
				}),	{
		            xtype: 'datefield',
		            width: 200,
		            allowBlank: false,
		            disabled: true,
					disabledDaysText: 'No sale el Omnibus',
		            fieldLabel: 'D&iacute;a de salida',
		            name: 'ticket_date',
		            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
		            format: 'Y-m-d',
		            id: 'frm_ticket_date'
		        },	new Ext.form.ComboBox({
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
		        }), new Ext.form.ComboBox({
		  			store: dataStoreState,
		  			displayField: 'state_name',
		  			valueField: 'state_id',
		  			hiddenName: 'edit_state_idHidden',
		  			allowBlank: true,
		  			typeAhead: true,
		  			mode: 'local',
		  			triggerAction: 'all', 					
		  			emptyText: 'Seleccione un Estado...',
		  			selectOnFocus: true,
		          	width: 200,
				    id: 'filter_edit_state_idHidden',
		            name : 'edit_state_idHidden',
					hidden:true
					})
        	]
    });

	Etecsa.filterForm.addButton({
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Etecsa.filterForm.getForm().reset();
           Ext.getCmp('filter_edit_state_id').clearValue();
           etecsaDataStore.baseParams = {
               transport_itinerary: '',
               ticket_date: '1900-01-01',
               motive: 0,
               state: 0
           };
           etecsaDataStore.load({params: {start:0,limit:50}});
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Etecsa.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	    	var transportItinerary = Ext.getCmp('frm_transport_itinerary').getValue();
		           	var ticketDate = Ext.getCmp('frm_ticket_date').getValue();
					var motive = Ext.getCmp('filter_motive_id').getValue();
					var state = Ext.getCmp('filter_edit_state_id').getValue();
		           	etecsaDataStore.baseParams = {
		           		transport_itinerary: transportItinerary,
						ticket_date: ticketDate.dateFormat('Y-m-d'),
						motive: motive,
						state: state
		           	};
		           	etecsaDataStore.load({params: {start:0,limit:50}});
       	}
   	});  
   	
   	Etecsa.filterForm.addButton({
		id:'exportPDF',					  
       text : 'Exportar pdf',
       disabled : false,
       formBind: true,
       handler : function() {
		 	if (etecsaDataStore.getCount()>0 ){
		 		var motive = Ext.getCmp('filter_motive_id').getValue();
				Etecsa.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_editetecsa/setDataGrid/si/'+motive ;
				Etecsa.filterForm.getForm().getEl().dom.method = 'POST';
				Etecsa.filterForm.getForm().submit();
			} else {
				Ext.Msg.alert('Mensaje','No hay datos que exportar!');
			}
		   
       }
   });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Etecsa.etecsaGrid.on('rowdblclick',function( grid, row, evt) {
        selectedId = etecsaDataStore.getAt(row).data.request_id;
        selectedDate = etecsaDataStore.getAt(row).data.ticket_date;
        update_ventana(selectedId, selectedDate);
    });
    
    function update_ventana(id, date){
	
		recordUpdate = new Ext.data.Record.create([
	        {name: 'request_id', type: 'int'},
	        {name: 'request_date'},
	        {name: 'ticket_date'},
	        {name: 'person_namerequestedby', type: 'string'},
	        {name: 'center_name', type: 'string'},
	        {name: 'transport_name', type: 'string'},
	        {name: 'transport_itinerary', type: 'string'},
	        {name: 'person_nameworker', type: 'string'},
	        {name: 'province_idfrom', type: 'string'},
	        {name: 'province_idto', type: 'string'},
	        {name: 'motive_name', type: 'string'},
	        {name: 'state_id', type: 'int'}
	    ]);
	    
		formReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'request_id'
	        },recordUpdate
	    );

    	var updateWindow;

	    var updateForm = new Ext.FormPanel({
	        id: 'upd-etecsa',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        labelWidth: 160,
	        width: 400,
	        minWidth: 400,
	        height: 360,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: formReader,
	        items: [
	        		{
			            fieldLabel : 'Fecha Solicitud',
			            id: 'upd_request_date',
			            name : 'request_date',
			            readOnly: true,
			            disabled: true,
			            width: 140,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Fecha de Salida',
			            id: 'upd_ticket_date',
			            name : 'ticket_date',
			            readOnly: true,
			            //disabled: true,
			            width: 140,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Solicitado por',
			            id: 'upd_person_namerequestedby',
			            name : 'person_namerequestedby',
			            readOnly: true,
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Centro de Costo',
			            id: 'upd_center_name',
			            name : 'center_name',
			            readOnly: true,
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Transporte que usar&aacute',
			            id: 'upd_transport_name',
			            name : 'transport_name',
			            readOnly: true,
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Itinerario',
			            id: 'upd_transport_itinerary',
			            name : 'transport_itinerary',
			            readOnly: true,
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Nombre y Apellidos',
			            id: 'upd_person_nameworker',
			            name : 'person_nameworker',
			            readOnly: true,
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        }, new Ext.form.ComboBox({
	           			store: dataStoreProv,
	           			fieldLabel: 'Origen',
	           			displayField: 'province_name',
	           			valueField: 'province_id',
	           			hiddenName: 'province_idfrom',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione una Provincia...',
	           			selectOnFocus: true,
	           			width: 200,
					    id: 'upd_province_idfrom',
			            name : 'province_idfrom',
			            listeners: {
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idfrom').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('upd_province_idfrom').reset();
					    			return false;
					    		}
							}
				 		}
	                }),new Ext.form.ComboBox({
	          			store: dataStoreProv,
	          			fieldLabel: 'Destino',
	          			displayField: 'province_name',
	          			valueField: 'province_id',
	          			hiddenName: 'province_idto',
	          			allowBlank: false,
	          			typeAhead: true,
	          			mode: 'local',
	          			triggerAction: 'all',
	          			emptyText: 'Seleccione una Provincia...',
	          			selectOnFocus: true,
	          			width: 200,
					    id: 'upd_province_idto',
			            name : 'province_idto',
			            listeners: {
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idto').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('upd_province_idto').reset();
					    			return false;
					    		}
							}
				 		}
	                }),{
			            fieldLabel : 'Motivo del Viaje',
			            id: 'upd_motive_name',
			            name : 'motive_name',
			            readOnly: true,
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        }, new Ext.form.ComboBox({
	           			store: dataStoreState,
	           			fieldLabel: 'Omnibus que viajar&aacute;',
	           			displayField: 'state_name',
	           			valueField: 'state_id',
	           			hiddenName: 'state_id',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			id: 'upd_state_id',
	           			emptyText: 'Seleccione un Omnibus...',
	           			selectOnFocus: true,
	           			width: 200,
	           			listeners: {
							'blur': function(){
								var flag = dataStoreState.findExact( 'state_id', Ext.getCmp('upd_state_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('upd_state_id').reset();
					    			return false;
					    		}
							}
				 		}
	                }), {
			            id: 'upd_request_id',
			            name : 'request_id',
			            xtype: 'hidden'
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
				var ticketdate = Ext.getCmp('upd_ticket_date').getValue();
		   		if (today > ticketdate){
					Ext.MessageBox.alert('Error', 'No se puede editar este pasaje porque la fecha de viaje ya expir&oacute;.');
					return false;
	       		}
	            updateForm.getForm().submit({
	                url : baseUrl+'index.php/ticket/ticket_editetecsa/insert',
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
	                    etecsaDataStore.load({params: {start:0,limit:50}});
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
	                    etecsaDataStore.load({params: {start:0,limit:50}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    updateForm.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            updateForm.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	    
	    updateForm.load({url:baseUrl+'index.php/ticket/ticket_editetecsa/getById/'+id+'/'+date});
		
		if(!updateWindow){

				updateWindow = new Ext.Window({
				title: 'Editar Pasaje',
				layout:'form',
				top: 200,
				width: 425,
				height:400,
				resizable : false,
				modal: true,
				bodyStyle:'padding:5px;',
				items: updateForm
				
				});
			}
		updateWindow.show(this);

	}
    
	function eventual_ventana(){

	    var eventualWindow;
	
		var eventualForm = new Ext.FormPanel({
	        id: 'form-services',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        labelWidth: 120,
	        standardSubmit:true,
	        width: 660,
	        minWidth: 660,
	        height: 300,
	        waitMsgTarget: true,
	        monitorValid: true,
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
	     * A�adimos el boton para guardar los datos del formulario
	     */
	    eventualForm.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
			   	eventualForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_requestservices/insert';
	           	eventualForm.getForm().getEl().dom.method = 'POST';
               	eventualForm.getForm().submit();
               	eventualWindow.destroy();
	        }
	    });
	    
	    /*
	     * Anadimos el boton para borrar el formulario
	     */
	    eventualForm.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	            eventualForm.getForm().reset();
	            eventualWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	    var title = 'Agregar ';
		
		if(!eventualWindow){
	
				eventualWindow = new Ext.Window({
				title: title+'Solicitud de Servicio',
				layout:'form',
				top: 200,
				width: 685,
				height: 340,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: eventualForm
				
				});
			}
		eventualWindow.show(this);
	
	}
    
    
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Etecsa.filterForm.render(Ext.get('etecsa_grid'));
	Etecsa.etecsaGrid.render(Ext.get('etecsa_grid'));
    //etecsaDataStore.load({params: {start:0,limit:30}});
});

    
    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
   				   url: baseUrl+'index.php/request/request_requests/deleteTicket/'+array[i].get('request_id')+'/'+array[i].get('ticket_date'),
				   method: 'GET',
				   disableCaching: false,
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la Solicitud.');
				   }
				});
		    }
			sm2.clearSelections();
		    etecsaDataStore.load({params: {start:0,limit:50}});
    	}
    }
    
    function filter(btn) {
	    if (btn == 'yes') {
			var transportItinerary = Ext.getCmp('frm_transport_itinerary').getValue();
           	var ticketDate = Ext.getCmp('frm_ticket_date').getValue();
			var motive = Ext.getCmp('filter_motive_id').getValue();
			var state = Ext.getCmp('filter_edit_state_idHidden').getValue();//Ext.getCmp('filter_edit_state_id').getValue();
           	etecsaDataStore.baseParams = {
           		transport_itinerary: transportItinerary,
				ticket_date: ticketDate.dateFormat('Y-m-d'),
				motive: motive,
				state: state
           	};
           	etecsaDataStore.load({params: {start:0,limit:50}});
    	}
    }        
	
