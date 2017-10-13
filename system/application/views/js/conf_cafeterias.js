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
    Ext.namespace('Cafeterias');
    
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
                    Cafeterias.CafeteriasGrid.removeButton.enable();
                } else {
                    Cafeterias.CafeteriasGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Cafeterias',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un cafeteria
     */
     
    Cafeterias.CafeteriasRecord = new Ext.data.Record.create([
        {name: 'cafeteria_id', type: 'int'},
        {name: 'cafeteria_name', type: 'string'},
        {name: 'cafeteria_deleted', type: 'string'},
        {name: 'province_id', type: 'int'},
        {name: 'province_name', type: 'string'},
        {name: 'chain_id', type: 'int'},
        {name: 'chain_name', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid
     */
    Cafeterias.CafeteriasGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'cafeteria_id'},
        Cafeterias.CafeteriasRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Cafeterias.CafeteriasDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_cafeterias/setData',
        method: 'POST'
    });

    CafeteriasDataStore = new Ext.data.GroupingStore({
        id: 'CafeteriasDS',
        proxy: Cafeterias.CafeteriasDataProxy,
        reader: Cafeterias.CafeteriasGridReader,
        sortInfo:{field: 'province_name', direction: "ASC"},
		groupField:'province_name'
        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Cafeterias.CafeteriasColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'cafeteria_id',
            name : 'cafeteria_id',
            dataIndex: 'cafeteria_id',
            hidden: true
        },{
	   		id: 'cafeteria_name',
            name: 'cafeteria_name',
            header: 'Cafeteria',
            width: 150,
            dataIndex: 'cafeteria_name',
            sortable: true
        },{
            id: 'chain_id',
            name : 'chain_id',
            dataIndex: 'chain_id',
            hidden: true
        },{
	   		id: 'chain_name',
            name: 'chain_name',
			header: "Cadena",
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
			dataIndex: 'cafeteria_deleted',
			renderer: state,
			sortable: true
		}]
    );


    /*
     * Creamos el grid
     */
    Cafeterias.CafeteriasGrid = new xg.GridPanel({
        id : 'ctr-Cafeterias-grid',
        store : CafeteriasDataStore,
        cm : Cafeterias.CafeteriasColumnMode,
        view: new Ext.grid.GroupingView({
        	forceFit:true,
          	groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Cafeterias" : "Cafeteria"]})'
        }),
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nueva Cafeteria',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar la Cafeteria seleccionada',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) Cafeteria(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: CafeteriasDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Cafeterias.CafeteriasGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = CafeteriasDataStore.getAt(row).data.Cafeteria_id;
        update_ventana(selectedId);
    });
    
    Cafeterias.filterForm = new Ext.FormPanel({
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
		   			width: 200,
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
		            fieldLabel : 'Nombre de la Cafeteria',
		            id: 'filter_cafeteria_name',
		            name : 'cafeteria_name',
		            width: 180,
		            xtype: 'textfield'
		        }
        ]
	});
    
    Cafeterias.filterForm.addButton({
        text : 'Borrar filtro',
        disabled : false,
        formBind: true,
        handler : function() {
     		Cafeterias.filterForm.getForm().reset();
     		CafeteriasDataStore.baseParams = {
 				name: '',
 				province: 0
     		};
     		CafeteriasDataStore.load({params: {start:0,limit:15}});
        }
     });
     
     Cafeterias.filterForm.addButton({
        	text : 'Filtrar',
        	disabled : false,
        	formBind: true,
        	handler : function() {;
	            var name = Ext.getCmp('filter_cafeteria_name').getValue();
	 			var province = Ext.getCmp('filter_province_id').getValue();
	 			CafeteriasDataStore.baseParams = {
	 				name: name,
	 				province: province
	            };
	 			CafeteriasDataStore.load({params: {start:0,limit:15}});
        	}
    });    
    
    function update_ventana(id){
	
		Cafeterias.CafeteriasRecordUpdate = new Ext.data.Record.create([
	        {name: 'cafeteria_id', type: 'int'},
	        {name: 'cafeteria_name', type: 'string'},
	        {name: 'province_id', type: 'int'},
	        {name: 'chain_id', type: 'int'},
	        {name: 'cafeteria_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Cafeterias.CafeteriasFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'cafeteria_id'
	        },Cafeterias.CafeteriasRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificacion
	     */

	    Cafeterias.Form = new Ext.FormPanel({
	        id: 'form-cafeterias',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 340,
	        minWidth: 340,
	        height: 200,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Cafeterias.CafeteriasFormReader,
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
	                			fieldLabel: 'Cadena Hotelera',
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
			            fieldLabel : 'Cafeteria',
			            id: 'frm_cafeteria_name',
			            name : 'cafeteria_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        }, 	{
			            id: 'frm_cafeteria_id',
			            name : 'cafeteria_id',
			            xtype: 'hidden'
			        }, 	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'cafeteria_deleted',
			   			valueField: 'cafeteria_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_cafeteria_deleted',
						hiddenName: 'cafeteria_deleted',
						name : 'frm_cafeteria_deleted'
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
	    Cafeterias.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Cafeterias.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_cafeterias/insert',
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
	                    CafeteriasDataStore.load({params: {start:0,limit:15}});
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
	                    Cafeterias.Form.getForm().reset();
	                    //updateWindow.destroy();
	                    sm2.clearSelections();
	                    CafeteriasDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Cafeterias.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Cafeterias.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Cafeterias.Form.load({url:baseUrl+'index.php/conf/conf_cafeterias/getById/'+id});
        	var title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Cafeteria',
				layout:'form',
				top: 200,
				width: 360,
				height:240,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Cafeterias.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Cafeterias.filterForm.render(Ext.get('cafeterias_grid'));
	Cafeterias.CafeteriasGrid.render(Ext.get('cafeterias_grid'));
    CafeteriasDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_cafeterias/delete/'+array[i].get('cafeteria_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		CafeteriasDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la Cafeteria.');
				   		sm2.clearSelections();
	                    CafeteriasDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
