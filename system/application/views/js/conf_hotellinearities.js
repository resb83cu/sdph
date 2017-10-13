var hotelsDataStore;
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
	autoLoad:true
});
 						
var dataRecordHotel = new Ext.data.Record.create([
	{name:'hotel_id'},
	{name:'hotel_name'}
]);

var dataReaderHotel = new Ext.data.JsonReader({root:'data'},dataRecordHotel);

var dataProxyHotel = new Ext.data.HttpProxy({
	url:baseUrl+'index.php/conf/conf_hotels/setDataGrid',
	method: 'POST'
});

var dataStoreHotel = new Ext.data.Store({
	proxy: dataProxyHotel,
	reader: dataReaderHotel,
	autoLoad:true
});


Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    //Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Linearities');
    
    function state(val){
        if(val == 'No'){
            return '<span style="color:green;"><b>' + 'No' + '</b></span>';
        }else {
            return '<span style="color:red;"><b>' + 'Si' + '</b></span>';
        }
        return val;
    }
    
   	var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Linearities.linearitiesGrid.removeButton.enable();
                } else {
                    Linearities.linearitiesGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Linealidad de Hoteles',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un hotel
     */
     
    Linearities.linearitiesRecord = new Ext.data.Record.create([
        {name: 'hotel_id', type: 'int'},
        {name: 'hotel_name', type: 'string'},
        {name: 'province_id', type: 'int'},
        {name: 'province_name', type: 'string'},
        {name: 'linearity_id', type: 'int'},
        {name: 'linearity_name', type: 'string'},
        {name: 'linearity_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Linearities.linearitiesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'linearity_id'},
        Linearities.linearitiesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Linearities.linearitiesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_hotellinearities/setData',
        method: 'POST'
    });

    linearitiesDataStore = new Ext.data.GroupingStore({
        id: 'linearitiesDS',
        proxy: Linearities.linearitiesDataProxy,
        reader: Linearities.linearitiesGridReader,
        sortInfo:{field: 'hotel_name', direction: "ASC"},
		groupField:'hotel_name'
        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Linearities.linearitiesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       	{
            id: 'province_id',
            name : 'province_id',
            dataIndex: 'province_id',
            hidden: true
        },	{
	   		id: 'province_name',
            name: 'province_name',
			header: "Provincia",
			width: 150,
			dataIndex: 'province_name',
			sortable: true
		},	{
            id: 'hotel_id',
            name : 'hotel_id',
            dataIndex: 'hotel_id',
            hidden: true
        },	{
	   		id: 'hotel_name',
            name: 'hotel_name',
            header: 'Hotel',
            width: 150,
            dataIndex: 'hotel_name',
            sortable: true
        },	{
            id: 'linearity_id',
            name : 'linearity_id',
            dataIndex: 'linearity_id',
            hidden: true
        },	{
	   		id: 'linearity_name',
            name: 'linearity_name',
			header: "Linealidad",
			dataIndex: 'linearity_name',
			width: 120,
			dataIndex: 'linearity_name',
			sortable: true
		},	{
			header: "Eliminado",
			width: 80,
			dataIndex: 'linearity_deleted',
			renderer: state,
			sortable: true
		}]
    );


    /*
     * Creamos el grid
     */
    Linearities.linearitiesGrid = new xg.GridPanel({
        id : 'ctr-linearities-grid',
        store : linearitiesDataStore,
        cm : Linearities.linearitiesColumnMode,
        view: new Ext.grid.GroupingView({
        	forceFit:true,
          	groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Linealidades" : "Linealidad"]})'
        }),
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Hotel',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Hotel seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) hotel(es)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: linearitiesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Linearities.linearitiesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = linearitiesDataStore.getAt(row).data.linearity_id;
        update_ventana(selectedId);
    });
    
    Linearities.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 160,
        height: 100,
        width: 750,
        items: [new Ext.form.ComboBox({
			   			store: hotelsDataStore,
			   			fieldLabel: 'Hoteles existentes',
			   			displayField: 'hotel_name',
			   			valueField: 'hotel_id',
			   			hiddenName: 'hotel_id',
			   			allowBlank: false,
			   			typeAhead: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			emptyText: 'Seleccione un Hotel...',
			   			selectOnFocus: true,
			   			width: '100%',
						id: 'filter_hotel_id',
						name : 'filter_hotel_id',
						listeners: {
							'blur': function(){
								var flag = hotelsDataStore.findExact( 'hotel_id', Ext.getCmp('filter_hotel_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('filter_hotel_id').reset();
					    			return false;
					    		}
							}
				 		}
		        })]
	});

    Linearities.filterForm.addButton({
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
    		Linearities.filterForm.getForm().reset();
    			linearitiesDataStore.baseParams = {
        		linearity: 0
    		};
    		linearitiesDataStore.load({params: {start:0,limit:15}});
       }
    });

    
    function update_ventana(id){
	
		Linearities.linearitiesRecordUpdate = new Ext.data.Record.create([
	        {name: 'hotel_id', type: 'int'},
	        {name: 'province_id', type: 'int'},
	        {name: 'linearity_id', type: 'int'},
	        {name: 'linearity_name', type: 'string'},
	        {name: 'linearity_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Linearities.linearitiesFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'linearity_id'
	        },Linearities.linearitiesRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Linearities.Form = new Ext.FormPanel({
	        id: 'form-linearities',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 360,
	        minWidth: 360,
	        labelWidth: 140,
	        height: 160,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Linearities.linearitiesFormReader,
	        items: [
	        			new Ext.form.ComboBox({
		        			store: dataStoreProv,
		          			fieldLabel: 'Provincia del Hotel',
		          			displayField: 'province_name',  
		          			valueField: 'province_id',
		          			hiddenName: 'province_id',
		          			allowBlank: false,
		          			formBind: true,
		          			typeAhead: true,
		          			mode: 'local',
		          			triggerAction: 'all',
		          			emptyText: 'Seleccione una Provincia...',
		          			selectOnFocus: true,
		          			width: 200,
						    id: 'frm_province_id',
				            name : 'frm_province_id',
				            listeners: {
								'select': function(){
											dataStoreHotel.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
											dataStoreHotel.load();
								},
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
						    			Ext.getCmp('frm_province_id').reset();
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
		           			allowBlank: false,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all', 					
		           			emptyText: 'Seleccione un Hotel...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'frm_hotel_id',
				            name : 'frm_hotel_id',
				            listeners: {
								'blur': function(){
									var flag = dataStoreHotel.findExact( 'hotel_id', Ext.getCmp('frm_hotel_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('frm_hotel_id').reset();
						    			return false;
						    		}
								}
					 		}
		                }),	{
				            fieldLabel : 'Linealidad',
				            id: 'frm_linearity_name',
				            name : 'linearity_name',
				            allowBlank:false,
				            xtype: 'textfield'
	        			}, 	{
				            id: 'frm_linearity_id',
				            name : 'linearity_id',
				            xtype: 'hidden'
				        },	new Ext.form.ComboBox({
				   			store:  ['No','Si'],
				   			fieldLabel: 'Eliminado',
				   			displayField: 'linearity_deleted',
				   			valueField: 'linearity_deleted',
				   			allowBlank: true,
				   			typeAhead: true,
				   			readOnly: true,
				   			mode: 'local',
				   			triggerAction: 'all',
				   			selectOnFocus: true,
				   			width: 50,
							id: 'frm_linearity_deleted',
							hiddenName: 'linearity_deleted',
							name : 'frm_linearity_deleted'
							/*listeners: {
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
						 	}*/
				        })
	        		]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Linearities.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Linearities.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_hotellinearities/insert',
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
	                    linearitiesDataStore.load({params: {start:0,limit:15}});
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
	                    Linearities.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    linearitiesDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Linearities.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Linearities.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Linearities.Form.load({url:baseUrl+'index.php/conf/conf_hotellinearities/getById/'+id});
        	var title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Linealidad',
				layout:'form',
				top: 200,
				width: 380,
				height: 205,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Linearities.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Linearities.filterForm.render(Ext.get('linearities_grid'));
	Linearities.linearitiesGrid.render(Ext.get('linearities_grid'));
    linearitiesDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_hotellinearities/delete/'+array[i].get('linearity_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		linearitiesDataStore.load({params: {start:0,limit:15}});
				   		Ext.MessageBox.show({
	                        title: 'Datos eliminados correctamente',
	                        msg: 'Datos eliminados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
				   		sm2.clearSelections();
				   },
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el hotel.');
				   		sm2.clearSelections();
	                    linearitiesDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
