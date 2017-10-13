var centersDataStore;
var array;
var sm2;

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

var dataRecordWork = new Ext.data.Record.create([
                          						{name:'person_id'},
                          						{name:'person_fullname'}
                          					]);
var dataReaderWork = new Ext.data.JsonReader({root:'data'},dataRecordWork);
var dataProxyWork = new Ext.data.HttpProxy({
  						url:baseUrl+'index.php/person/person_workers/setDataByProvince',
  						method: 'POST'
  					});
var dataStoreWork = new Ext.data.Store({
  						proxy: dataProxyWork,
  						reader: dataReaderWork,
  						autoLoad: true
 					});	

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Centers');
    
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
                    Centers.centersGrid.removeButton.enable();
                } else {
                    Centers.centersGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Centro de Costo',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un center
     */
     
    Centers.centersRecord = new Ext.data.Record.create([
        {name: 'center_id', type: 'int'},
        {name: 'center_name', type: 'string'},
        {name: 'center_deleted', type: 'string'},
        {name: 'province_name', type: 'string'},
        {name: 'person_name', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Centers.centersGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'center_id'},
        Centers.centersRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Centers.centersDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_costcenters/setData',
        method: 'POST'
    });

    centersDataStore = new Ext.data.Store({
        id: 'centersDS',
        proxy: Centers.centersDataProxy,
        reader: Centers.centersGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Centers.centersColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'center_id',
            name : 'center_id',
            dataIndex: 'center_id',
            hidden: true
        },	{
	   		id: 'center_name',
            name: 'center_name',
            header: 'Centro de Costo',
            width: 200,
            dataIndex: 'center_name',
            sortable: true
        },	{
	   		id: 'province_name',
            name: 'province_name',
            header: 'Provincia',
            width: 200,
            dataIndex: 'province_name',
            sortable: true
        },	{
	   		id: 'person_name',
            name: 'person_name',
            header: 'Responsable',
            width: 200,
            dataIndex: 'person_name',
            sortable: true
        },	{
			header: "Eliminado",
			width: 80,
			dataIndex: 'center_deleted',
			renderer: state,
			sortable: true
		}]
    );


    /*
     * Creamos el grid de movimientos
     */
    Centers.centersGrid = new xg.GridPanel({
        id : 'ctr-centers-grid',
        store : centersDataStore,
        cm : Centers.centersColumnMode,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Centro de Costo',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar Centro de Costo seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Centro(s) de Costo?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: centersDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Centers.centersGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = centersDataStore.getAt(row).data.center_id;
        update_ventana(selectedId);
        
    });
    
    Centers.filterForm = new Ext.FormPanel({
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
		            fieldLabel : 'Centro de Costo',
		            id: 'filter_center_name',
		            name : 'center_name',
		            width: 180,
		            xtype: 'textfield'
		        }
        ]
	});

    Centers.filterForm.addButton({
        text : 'Borrar filtro',
        disabled : false,
        formBind: true,
        handler : function() {
     		Centers.filterForm.getForm().reset();
     		centersDataStore.baseParams = {
 				name: '',
 				province: 0
     		};
     		centersDataStore.load({params: {start:0,limit:15}});
        }
     });
     
     Centers.filterForm.addButton({
        	text : 'Filtrar',
        	disabled : false,
        	formBind: true,
        	handler : function() {;
	            var name = Ext.getCmp('filter_center_name').getValue();
	 			var province = Ext.getCmp('filter_province_id').getValue();
	 			centersDataStore.baseParams = {
	 				name: name,
	 				province: province
	            };
	 			centersDataStore.load({params: {start:0,limit:15}});
        	}
    });    

    
    function update_ventana(id){
	
		Centers.centersRecordUpdate = new Ext.data.Record.create([
	        {name: 'center_id', type: 'int'},
	        {name: 'center_name', type: 'string'},
	        {name: 'center_deleted', type: 'string'},
	        {name: 'province_id', type: 'string'},
	        {name: 'person_id', type: 'string'},
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
	    Centers.centersFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'center_id'
	        },Centers.centersRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Centers.Form = new Ext.FormPanel({
	        id: 'form-centers',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 317,
	        minWidth: 317,
	        height: 160,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Centers.centersFormReader,
	        items: [	new Ext.form.ComboBox({				  
							//disabled:Users.setMode,
			       			store: dataStoreProv,
			       			fieldLabel: 'Provincia',
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
						    id: 'frm_province_id',
				            name : 'province_id',
				            listeners: {
								'select': function(){
											dataStoreWork.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
											dataStoreWork.load();
											//dataStoreDirector.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
			            					//dataStoreDirector.load();
								},
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('frm_province_id').reset();
						    			return false;
						    		}
								}
					 		}
			            }),	new Ext.form.ComboBox({
			       			store: dataStoreWork,
							//disabled:Users.setMode,
			       			fieldLabel: 'Responsable',
			       			displayField: 'person_fullname',
			       			valueField: 'person_id',
			       			hiddenName: 'person_id',
			       			allowBlank: false,
			       			typeAhead: true,
			       			mode: 'local',
			       			triggerAction: 'all',
			       			emptyText: 'Seleccione un Trabajador...',
			       			selectOnFocus: true,
			       			width: 200,
						    id: 'frm_person_id',
				            name : 'person_id',
				            listeners: {
								'show': function(){
											dataStoreWork.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
											dataStoreWork.load();
								},
								'blur': function(){
									var flag = dataStoreWork.findExact( 'person_id', Ext.getCmp('frm_person_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('frm_person_id').reset();
						    			return false;
						    		}
								}
					 		}
			            }),	{
				            fieldLabel : 'Centro de Costo',
				            id: 'frm_center_name',
				            name : 'center_name',
				            allowBlank:false,
				            xtype: 'textfield'
				        }, {
				            id: 'frm_center_id',
				            name : 'center_id',
				            xtype: 'hidden'
				        },	new Ext.form.ComboBox({
				   			store:  ['No','Si'],
				   			fieldLabel: 'Eliminado',
				   			displayField: 'center_deleted',
				   			valueField: 'center_deleted',
				   			allowBlank: true,
				   			typeAhead: true,
				   			readOnly: true,
				   			mode: 'local',
				   			triggerAction: 'all',
				   			selectOnFocus: true,
				   			width: 50,
							id: 'frm_center_deleted',
							hiddenName: 'center_deleted',
							name : 'frm_center_deleted'
				        })
				    ]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Centers.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Centers.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_costcenters/insert',
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
	                    centersDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Centers.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    centersDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Centers.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Centers.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Centers.Form.load({url:baseUrl+'index.php/conf/conf_costcenters/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Centro de Costo',
				layout:'form',
				top: 200,
				width: 340,
				height: 200,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Centers.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Centers.filterForm.render(Ext.get('centers_grid'));
	Centers.centersGrid.render(Ext.get('centers_grid'));
    centersDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_costcenters/delete/'+array[i].get('center_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		centersDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el centro de Costo.');
				   		sm2.clearSelections();
	                    centersDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
    
