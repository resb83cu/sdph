var positionsDataStore;
var array;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Positions');
    
    function state(val){
        if(val == 'No'){
            return '<span style="color:green;"><b>' + 'No' + '</b></span>';
        }else {
            return '<span style="color:red;"><b>' + 'Si' + '</b></span>';
        }
        return val;
    }
    
   	var xg = Ext.grid;

    var sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Positions.positionsGrid.removeButton.enable();
                } else {
                    Positions.positionsGrid.removeButton.disable();
                }
            }
        }
    });
    
    var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Plazas',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un position
     */
     
    Positions.positionsRecord = new Ext.data.Record.create([
        {name: 'position_id', type: 'int'},
        {name: 'position_name', type: 'string'},
        {name: 'position_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Positions.positionsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'position_id'},
        Positions.positionsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Positions.positionsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_positions/setData',
        method: 'POST'
    });

    positionsDataStore = new Ext.data.Store({
        id: 'positionsDS',
        proxy: Positions.positionsDataProxy,
        reader: Positions.positionsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Positions.positionsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'position_id',
            name : 'position_id',
            dataIndex: 'position_id',
            hidden: true
        },	{
	   		id: 'position_name',
            name: 'position_name',
            header: 'Plaza',
            width: 250,
            dataIndex: 'position_name',
            sortable: true
        },	{
			header: "Eliminado",
			width: 80,
			dataIndex: 'position_deleted',
			renderer: state,
			sortable: true
		}]
    );


    /*
     * Creamos el grid de movimientos
     */
    Positions.positionsGrid = new xg.GridPanel({
        id : 'ctr-positions-grid',
        store : positionsDataStore,
        cm : Positions.positionsColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nueva Plaza',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar la Plaza seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) Plaza(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: positionsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Positions.positionsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = positionsDataStore.getAt(row).data.position_id;
        update_ventana(selectedId);
        
    });
    
	Positions.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 160,
        height: 75,
        width: 750,
        items: [{
		            fieldLabel : 'Plaza',
		            id: 'filter_position_name',
		            name : 'position_name',
		            width: 180,
		            xtype: 'textfield'
		        }
        ]
	});
	
    Positions.filterForm.addButton({
        text : 'Borrar filtro',
        disabled : false,
        formBind: true,
        handler : function() {
     		Positions.filterForm.getForm().reset();
     		positionsDataStore.baseParams = {
 				name: ''
     		};
     		positionsDataStore.load({params: {start:0,limit:15}});
        }
     });
     
     Positions.filterForm.addButton({
        	text : 'Filtrar',
        	disabled : false,
        	formBind: true,
        	handler : function() {;
	            var name = Ext.getCmp('filter_position_name').getValue();
	 			positionsDataStore.baseParams = {
	 				name: name
	            };
	 			positionsDataStore.load({params: {start:0,limit:15}});
        	}
    });
    
    function update_ventana(id){
	
		Positions.positionsRecordUpdate = new Ext.data.Record.create([
	        {name: 'position_id', type: 'int'},
	        {name: 'position_name', type: 'string'},
	        {name: 'position_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Positions.positionsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'position_id'
	        },Positions.positionsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Positions.Form = new Ext.FormPanel({
	        id: 'form-positions',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 340,
	        minWidth: 340,
	        labelWidth: 90,
	        height: 100,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Positions.positionsFormReader,
	        items: [{
			            fieldLabel : 'Plaza',
			            id: 'frm_position_name',
			            name : 'position_name',
			            allowBlank:false,
			            width: 225,
			            xtype: 'textfield'
			        }, 	{
			            id: 'frm_position_id',
			            name : 'position_id',
			            xtype: 'hidden'
			        },	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'position_deleted',
			   			valueField: 'position_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_position_deleted',
						hiddenName: 'position_deleted',
						name : 'frm_position_deleted'
			        })]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Positions.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Positions.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_positions/insert',
	                waitMsg : 'Salvando datos...',
	                failure: function (form, action) {
	            		if(action.failureType == 'server'){ 
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.alert('Error!', obj.errors.reason);
	            		}
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Positions.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    positionsDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Positions.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Positions.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Positions.Form.load({url:baseUrl+'index.php/conf/conf_positions/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Plaza',
				layout:'form',
				top: 200,
				width: 365,
				height: 140,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Positions.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Positions.filterForm.render(Ext.get('positions_grid'));
	Positions.positionsGrid.render(Ext.get('positions_grid'));
    positionsDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_positions/delete/'+array[i].get('position_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		positionsDataStore.load({params: {start:0,limit:15}});
				   		Ext.MessageBox.show({
	                        title: 'Datos eliminados correctamente',
	                        msg: 'Datos eliminados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
				   },
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la Plaza.');
				   }
				});
		    }
    	}
    }
    
