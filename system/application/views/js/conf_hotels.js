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
						autoLoad: true
						});
						
var dataRecordChain = new Ext.data.Record.create([
						{name:'chain_id'},
						{name:'chain_name'}
					]);
var dataReaderChain = new Ext.data.JsonReader({root:'data'},dataRecordChain);
var dataProxyChain = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_hotelchains/setDataGrid',
						method: 'POST'
					});
var dataStoreChain = new Ext.data.Store({
						proxy: dataProxyChain,
						reader: dataReaderChain,
						autoLoad: true
						});	

Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    //Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Hotels');
    
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
                    Hotels.hotelsGrid.removeButton.enable();
                } else {
                    Hotels.hotelsGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Hoteles',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un hotel
     */
     
    Hotels.hotelsRecord = new Ext.data.Record.create([
        {name: 'hotel_id', type: 'int'},
        {name: 'hotel_name', type: 'string'},
        {name: 'hotel_deleted', type: 'string'},
        {name: 'hotel_price', type: 'float'},
        {name: 'province_id', type: 'int'},
        {name: 'province_name', type: 'string'},
        {name: 'chain_id', type: 'int'},
        {name: 'chain_name', type: 'string'},
        {name: 'linearity_id', type: 'int'},
        {name: 'linearity_name', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Hotels.hotelsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'hotel_id'},
        Hotels.hotelsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Hotels.hotelsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_hotels/setData',
        method: 'POST'
    });

    hotelsDataStore = new Ext.data.GroupingStore({
        id: 'hotelsDS',
        proxy: Hotels.hotelsDataProxy,
        reader: Hotels.hotelsGridReader,
        sortInfo:{field: 'province_name', direction: "ASC"},
		groupField:'province_name'
        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Hotels.hotelsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'hotel_id',
            name : 'hotel_id',
            dataIndex: 'hotel_id',
            hidden: true
        },{
	   		id: 'hotel_name',
            name: 'hotel_name',
            header: 'Hoteles',
            width: 150,
            dataIndex: 'hotel_name',
            sortable: true
        },{
            id: 'chain_id',
            name : 'chain_id',
            dataIndex: 'chain_id',
            hidden: true
        },{
	   		id: 'chain_name',
            name: 'chain_name',
			header: "Cadena Hotelera",
			width: 150,
			dataIndex: 'chain_name',
			sortable: true
		},{
            id: 'province_id',
            name : 'province_id',
            dataIndex: 'province_id',
            hidden: true
        },{
	   		id: 'province_name',
            name: 'province_name',
			header: "Provincia",
			width: 150,
			dataIndex: 'province_name',
			sortable: true
		},{
			header: "Eliminado",
			width: 80,
			dataIndex: 'hotel_deleted',
			renderer: state,
			sortable: true
		},{
			header: "Hospedaje",
			width: 80,
			dataIndex: 'hotel_price',
			renderer: 'usMoney',
			sortable: true
		},{
            id: 'linearity_id',
            name : 'linearity_id',
            dataIndex: 'linearity_id',
            hidden: true
        },{
	   		id: 'linearity_name',
            name: 'linearity_name',
			header: "Linealidad",
			dataIndex: 'linearity_name',
			width: 120,
			dataIndex: 'linearity_name',
			sortable: true
		}]
    );


    /*
     * Creamos el grid
     */
    Hotels.hotelsGrid = new xg.GridPanel({
        id : 'ctr-hotels-grid',
        store : hotelsDataStore,
        cm : Hotels.hotelsColumnMode,
        view: new Ext.grid.GroupingView({
        	forceFit:true,
          	groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Hoteles" : "Hotel"]})'
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
        },'-',{
            text:'Exportar a excel',
            tooltip:'Exportar a excel',
            iconCls:'xls',
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function(){
                //if (dataStorereportInternalLodging.getCount()>0 ){
                Hotels.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/conf/conf_hotels/exportExcel';
                Hotels.filterForm.getForm().getEl().dom.method = 'POST';
                Hotels.filterForm.getForm().submit();
            /*} else{
                        Ext.Msg.alert('Mensaje','No hay datos que exportar!');    
                    }*/
            // }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: hotelsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Hotels.hotelsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = hotelsDataStore.getAt(row).data.hotel_id;
        update_ventana(selectedId);
    });
    
    Hotels.filterForm = new Ext.FormPanel({
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
		 			store: dataStoreProv,
		   			fieldLabel: 'Provincia',
		   			displayField: 'province_name',
		   			valueField: 'province_id',
		   			hiddenName: 'province_id',
		   			typeAhead: true,
		   			mode: 'local',
		   			triggerAction: 'all',
		   			emptyText: 'Seleccione una Provincia...',
		   			selectOnFocus: true,
		   			width: '100%',
				    id: 'filter_province_id',
		            name : 'province_id',
		            listeners: {
		
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
					    			Ext.getCmp('filter_province_id').reset();
					    			return false;
					    		}
							}
				 	}
		        }),	{
		            fieldLabel : 'Nombre del Hotel',
		            id: 'filter_hotel_name',
		            name : 'hotel_name',
		            width: 180,
		            xtype: 'textfield'
		        }
        ]
	});
    
    Hotels.filterForm.addButton({
        text : 'Borrar filtro',
        disabled : false,
        formBind: true,
        handler : function() {
     		Hotels.filterForm.getForm().reset();
     		hotelsDataStore.baseParams = {
 				name: '',
 				province: 0
     		};
     		hotelsDataStore.load({params: {start:0,limit:15}});
        }
     });
     
     Hotels.filterForm.addButton({
        	text : 'Filtrar',
        	disabled : false,
        	formBind: true,
        	handler : function() {;
	            var name = Ext.getCmp('filter_hotel_name').getValue();
	 			var province = Ext.getCmp('filter_province_id').getValue();
	 			hotelsDataStore.baseParams = {
	 				name: name,
	 				province: province
	            };
	 			hotelsDataStore.load({params: {start:0,limit:15}});
        	}
    });    
    
    function update_ventana(id){
	
		Hotels.hotelsRecordUpdate = new Ext.data.Record.create([
	        {name: 'hotel_id', type: 'int'},
	        {name: 'hotel_name', type: 'string'},
	        {name: 'hotel_price', type: 'float'},
	        {name: 'province_id', type: 'int'},
	        {name: 'chain_id', type: 'int'},
	        {name: 'hotel_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Hotels.hotelsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'hotel_id'
	        },Hotels.hotelsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Hotels.Form = new Ext.FormPanel({
	        id: 'form-hotels',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 340,
	        minWidth: 340,
	        height: 200,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Hotels.hotelsFormReader,
	        items: [
	        		new Ext.form.ComboBox({
	                			store: dataStoreProv,
	                			id: 'frm_province_id',
	                			fieldLabel: 'Provincias',
	                			displayField: 'province_name',
	                			valueField: 'province_id',
	                			hiddenName: 'province_id',
	                			allowBlank: false,
	                			typeAhead: true,
	                			mode: 'local',
	                			triggerAction: 'all',
	                			emptyText: 'Seleccione una Provincia...',
	                			selectOnFocus: true,
	                			width: 200,
	                			listeners: {
	    							'blur': function(){
	    								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_id').getValue());
	    					    		if (flag == -1){
	    					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
	    					    			Ext.getCmp('frm_province_id').reset();
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
	                			allowBlank: false,
	                			id: 'frm_chain_id',
	                			typeAhead: true,
	                			mode: 'local',
	                			triggerAction: 'all',
	                			emptyText: 'Seleccione una Cadena...',
	                			selectOnFocus: true,
	                			width: 200,
	                			listeners: {
	    							'blur': function(){
	    								var flag = dataStoreChain.findExact( 'chain_id', Ext.getCmp('frm_chain_id').getValue());
	    					    		if (flag == -1){
	    					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
	    					    			Ext.getCmp('frm_chain_id').reset();
	    					    			return false;
	    					    		}
	    							}
	    				 		}
	                }),	{
			            fieldLabel : 'Hotel',
			            id: 'frm_hotel_name',
			            name : 'hotel_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        }, 	{
			            fieldLabel : 'Precio',
			            id: 'frm_hotel_price',
			            name : 'hotel_price',
			            allowBlank:false,
			            xtype: 'numberfield'
			        }, 	{
			            id: 'frm_hotel_id',
			            name : 'hotel_id',
			            xtype: 'hidden'
			        }, 	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'hotel_deleted',
			   			valueField: 'hotel_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_hotel_deleted',
						hiddenName: 'hotel_deleted',
						name : 'frm_hotel_deleted'
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
			        })]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Hotels.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Hotels.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_hotels/insert',
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
	                    hotelsDataStore.load({params: {start:0,limit:15}});
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
	                    Hotels.Form.getForm().reset();
	                    //updateWindow.destroy();
	                    sm2.clearSelections();
	                    hotelsDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Hotels.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Hotels.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Hotels.Form.load({url:baseUrl+'index.php/conf/conf_hotels/getById/'+id});
        	var title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Hotel',
				layout:'form',
				top: 200,
				width: 360,
				height:240,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Hotels.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Hotels.filterForm.render(Ext.get('hotels_grid'));
	Hotels.hotelsGrid.render(Ext.get('hotels_grid'));
    hotelsDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_hotels/delete/'+array[i].get('hotel_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		hotelsDataStore.load({params: {start:0,limit:15}});
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
	                    hotelsDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
