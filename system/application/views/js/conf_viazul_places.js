var placesDataStore;
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

Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    //Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Places');

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
                    Places.placesGrid.removeButton.enable();
                } else {
                    Places.placesGrid.removeButton.disable();
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

    Places.placesRecord = new Ext.data.Record.create([
        {name: 'viazul_place_id', type: 'int'},
        {name: 'viazul_place_name', type: 'string'},
        {name: 'viazul_place_deleted', type: 'string'},
        {name: 'province_id', type: 'int'},
        {name: 'province_name', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Places.placesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'viazul_place_id'},
        Places.placesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Places.placesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_viazul_places/setData',
        method: 'POST'
    });

    placesDataStore = new Ext.data.GroupingStore({
        id: 'placesDS',
        proxy: Places.placesDataProxy,
        reader: Places.placesGridReader,
        sortInfo:{field: 'province_name', direction: "ASC"},
		groupField:'province_name'

    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Places.placesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'viazul_place_id',
            name : 'viazul_place_id',
            dataIndex: 'viazul_place_id',
            hidden: true
        },{
		   id: 'province_name',
		   name: 'province_name',
		   header: "Provincia",
		   width: 150,
		   dataIndex: 'province_name',
		   sortable: true
	   },{
	   		id: 'viazul_place_name',
            name: 'viazul_place_name',
            header: 'Destinos Viazul',
            width: 150,
            dataIndex: 'viazul_place_name',
            sortable: true
        },{
            id: 'province_id',
            name : 'province_id',
            dataIndex: 'province_id',
            hidden: true
        },{
			header: "Eliminado",
			width: 80,
			dataIndex: 'viazul_place_deleted',
			renderer: state,
			sortable: true
		}]
    );


    /*
     * Creamos el grid
     */
    Places.placesGrid = new xg.GridPanel({
        id : 'ctr-places-grid',
        store : placesDataStore,
        cm : Places.placesColumnMode,
        view: new Ext.grid.GroupingView({
        	forceFit:true,
          	groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Destinos" : "Destino"]})'
        }),
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Destino',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Destino seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) destino(s)?', delRecords);
				    }
            }
        },'-',{
            text:'Exportar a excel',
            tooltip:'Exportar a excel',
            iconCls:'xls',
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function(){
                //if (dataStorereportInternalLodging.getCount()>0 ){
                Places.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/conf/conf_viazul_places/exportExcel';
                Places.filterForm.getForm().getEl().dom.method = 'POST';
                Places.filterForm.getForm().submit();
            /*} else{
                        Ext.Msg.alert('Mensaje','No hay datos que exportar!');    
                    }*/
            // }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 50,
            store: placesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Places.placesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = placesDataStore.getAt(row).data.viazul_place_id;
        update_ventana(selectedId);
    });
    
    Places.filterForm = new Ext.FormPanel({
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
		            fieldLabel : 'Destino Viazul',
		            id: 'filter_viazul_place_name',
		            name : 'viazul_place_name',
		            width: 180,
		            xtype: 'textfield'
		        }
        ]
	});
    
    Places.filterForm.addButton({
        text : 'Borrar filtro',
        disabled : false,
        formBind: true,
        handler : function() {
     		Places.filterForm.getForm().reset();
     		placesDataStore.baseParams = {
 				name: '',
 				province: 0
     		};
     		placesDataStore.load({params: {start:0,limit:15}});
        }
     });
     
     Places.filterForm.addButton({
        	text : 'Filtrar',
        	disabled : false,
        	formBind: true,
        	handler : function() {;
	            var name = Ext.getCmp('filter_viazul_place_name').getValue();
	 			var province = Ext.getCmp('filter_province_id').getValue();
	 			placesDataStore.baseParams = {
	 				name: name,
	 				province: province
	            };
	 			placesDataStore.load({params: {start:0,limit:15}});
        	}
    });    
    
    function update_ventana(id){
	
		Places.placesRecordUpdate = new Ext.data.Record.create([
	        {name: 'viazul_place_id', type: 'int'},
	        {name: 'viazul_place_name', type: 'string'},
	        {name: 'province_id', type: 'int'},
	        {name: 'viazul_place_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Places.placesFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'viazul_place_id'
	        },Places.placesRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Places.Form = new Ext.FormPanel({
	        id: 'form-places',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 340,
	        minWidth: 340,
	        height: 125,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Places.placesFormReader,
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
	                }),	{
			            fieldLabel : 'Destino',
			            id: 'frm_viazul_place_name',
			            name : 'viazul_place_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        }, 	{
			            id: 'frm_viazul_place_id',
			            name : 'viazul_place_id',
			            xtype: 'hidden'
			        }, 	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'viazul_place_deleted',
			   			valueField: 'viazul_place_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_viazul_place_deleted',
						hiddenName: 'viazul_place_deleted',
						name : 'frm_viazul_place_deleted'
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
	    Places.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Places.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_viazul_places/insert',
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
	                    placesDataStore.load({params: {start:0,limit:15}});
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
	                    Places.Form.getForm().reset();
	                    //updateWindow.destroy();
	                    sm2.clearSelections();
	                    placesDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Places.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Places.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Places.Form.load({url:baseUrl+'index.php/conf/conf_viazul_places/getById/'+id});
        	var title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Destino',
				layout:'form',
				top: 200,
				width: 360,
				height:165,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Places.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Places.filterForm.render(Ext.get('places_grid'));
	Places.placesGrid.render(Ext.get('places_grid'));
    placesDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_viazul_places/delete/'+array[i].get('viazul_place_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		placesDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Destino.');
				   		sm2.clearSelections();
	                    placesDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
