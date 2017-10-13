var menuRollDataStore;
var array, sm2;

var dataRecordMenu = new Ext.data.Record.create([
						{name:'menus_id'},
						{name:'menus_title'}
					]);
var dataReaderMenu = new Ext.data.JsonReader({root:'data'},dataRecordMenu);
var dataProxyMenu = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/sys/sys_menu/setDataGrid',
						method: 'POST'
					});
var dataStoreMenu = new Ext.data.Store({
						proxy: dataProxyMenu,
						reader: dataReaderMenu,
						autoLoad: true
						});	

var dataRecordRoll = new Ext.data.Record.create([
 						{name:'roll_id'},
 						{name:'roll_description'}
 					]);
var dataReaderRoll = new Ext.data.JsonReader({root:'data'},dataRecordRoll);
var dataProxyRoll = new Ext.data.HttpProxy({
 						url:baseUrl+'index.php/user/user_rolls/setDataGrid',
 						method: 'POST'
 					});
var dataStoreRoll = new Ext.data.Store({
 						proxy: dataProxyRoll,
 						reader: dataReaderRoll,
 						autoLoad: true
 						});
				
Ext.onReady(function() {
 	
	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    /*
     * Creamos un espacio de nombres

     */
    Ext.namespace('MenuRoll');
    MenuRoll.setMode=false;
   	var xg = Ext.grid;
	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    MenuRoll.menurollGrid.removeButton.enable();
                } else {
                    MenuRoll.menurollGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Administraci&oacute;n -> Menu por Roll',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });

    /*
     * Definimos el registro
     */

    MenuRoll.menurollRecord = new Ext.data.Record.create([
        {name: 'roll_id', type: 'int'},
        {name: 'roll_name', type: 'string'},
        {name: 'menus_id', type: 'string'},
        {name: 'menus_title', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid
     */
    MenuRoll.menurollGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count'},
        MenuRoll.menurollRecord
    );
    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    MenuRoll.menurollDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/sys/sys_menu_roll/setDataGrid',
        method: 'POST'
    });

    menurollDataStore = new Ext.data.GroupingStore({
        id: 'menurollDS',
        proxy: MenuRoll.menurollDataProxy,
        reader: MenuRoll.menurollGridReader,
        sortInfo:{field: 'roll_name', direction: "DESC"},
		groupField:'roll_name'
    });

    MenuRoll.menurollColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'roll_id',
            name : 'roll_id',
            dataIndex: 'roll_id',
            hidden: true
        },{
            id: 'menus_id',
            name : 'menus_id',
            dataIndex: 'menus_id',
            hidden: true
        },{
	   		id: 'roll_name',
            name: 'roll_name',
            header: 'Rol',
            width: 100,
            dataIndex: 'roll_name',
            sortable: true
        },{
	   		id: 'menus_title',
            name: 'menus_title',
            header: 'Menu',
            width: 100,
            dataIndex: 'menus_title',
            sortable: true
        }]
    );

    /*
     * Creamos el grid
     */
    MenuRoll.menurollGrid = new xg.GridPanel({
        id : 'ctr-menuroll-grid',
        store : menurollDataStore,
        cm : MenuRoll.menurollColumnMode,
        stripeRows: true,
		view: new Ext.grid.GroupingView({
          forceFit:true,
          groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Menus" : "Menu"]})'
        }),
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
	            text:'Agregar',
	            tooltip:'Agregar nueva relaci&oacute;n',
	            iconCls:'add',
	            handler: function(){
	                update_ventana();
	            }
	        },'-',{
	            text:'Eliminar',
	            tooltip:'Eliminar la relaci&oacute;n seleccionada',
	            iconCls:'del',
	            ref: '../removeButton',
	            disabled: true,
	            handler: function(){
	            	    array = sm2.getSelections();
					    if (array.length > 0) {
					        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) relacion(es)?', delRecords);
					    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 200,
            store: menurollDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });
    
    
	MenuRoll.menurollGrid.render(Ext.get('menuroll_grid'));
    menurollDataStore.load();
    
    function update_ventana(){
	
	    var updateWindow;
	    
	    MenuRoll.Form = new Ext.FormPanel({
	        id: 'form-menuroll',
	        region: 'west',
	        split: false,
	        frame: true,
	        labelWidth: 120,
	        width: 370,
	        minWidth: 370,
	        height: 100,
	        waitMsgTarget: true,
	        monitorValid: true,
	        items: [new Ext.form.ComboBox({
	           			store: dataStoreMenu,
	           			fieldLabel: 'Opci&oacute;n de men&uacute;',
	           			displayField: 'menus_title',
	           			valueField: 'menus_id',
	           			hiddenName: 'menus_id',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione una Opción...',
	           			selectOnFocus: true,
	           			width: 200,
					    id: 'frm_menus_id',
			            name : 'menus_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreMenu.findExact( 'menus_id', Ext.getCmp('frm_menus_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_menus_id').reset();
					    			return false;
					    		}
							}
				 		}
	                }), new Ext.form.ComboBox({
	           			store: dataStoreRoll,
	           			fieldLabel: 'Tipo de usuario',
	           			displayField: 'roll_description',
	           			valueField: 'roll_id',
	           			hiddenName: 'roll_id',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione un Rol...',
	           			selectOnFocus: true,
	           			width: 200,
					    id: 'frm_roll_id',
			            name : 'roll_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreRoll.findExact( 'roll_id', Ext.getCmp('frm_roll_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_roll_id').reset();
					    			return false;
					    		}
							}
				 		}
	                })]
			
	    });
	    
	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    MenuRoll.Form.addButton({
	    	text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	    		MenuRoll.Form.getForm().submit({
	                url : baseUrl+'index.php/sys/sys_menu_roll/insert',
	                waitMsg : 'Salvando datos...',
	                failure: function (form, action) {
	                	if(action.failureType == 'server'){
	                        obj = Ext.util.JSON.decode(action.response.responseText);
	                        Ext.MessageBox.show({
		                        title: 'Error',
		                        msg: obj.errors.reason,
		                        width: 450,
		                        buttons: Ext.MessageBox.OK,
		                        icon: Ext.MessageBox.ERROR
		                    });
	                        MenuRoll.Form.getForm().reset();
	                    }
	                	menurollDataStore.load();
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
	                    MenuRoll.Form.getForm().reset();
	                    //updateWindow.destroy();
	                    menurollDataStore.load();
						sm2.clearSelections();
						
	                }
					
	            });
				
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    MenuRoll.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	        	//Ext.example.msg('Prueba', 'You clicked the button');
	            MenuRoll.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: 'Agregar Men&uacute; por Rol',
				layout:'form',
				top: 200,
				width: 395,
				height: 140,
				resizable : false,
				modal: true,
				bodyStyle:'padding:5px;',
				items: MenuRoll.Form
				
				});
			}
		updateWindow.show(this);
	
	}
    
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/sys/sys_menu_roll/delete/'+array[i].get('roll_id')+'/'+array[i].get('menus_id'),
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Usuario.');
				   }
				});
		    }
			sm2.clearSelections();
		    menurollDataStore.load({params: {start:0,limit:30}});
    	}
    }
