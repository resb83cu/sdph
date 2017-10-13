var rollsDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Rolls');
    

   	var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Rolls.rollsGrid.removeButton.enable();
                } else {
                    Rolls.rollsGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Administraci&oacute;n -> Roles',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un roll
     */
     
    Rolls.rollsRecord = new Ext.data.Record.create([
        {name: 'roll_id', type: 'int'},
        {name: 'roll_name', type: 'string'},
        {name: 'roll_description', type: 'string'}
        
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Rolls.rollsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'roll_id'},
        Rolls.rollsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Rolls.rollsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/user/user_rolls/setDataGrid',
        method: 'POST'
    });

    rollsDataStore = new Ext.data.Store({
        id: 'rollsDS',
        proxy: Rolls.rollsDataProxy,
        reader: Rolls.rollsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Rolls.rollsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,{
            id: 'roll_id',
            name : 'roll_id',
            dataIndex: 'roll_id',
            hidden: true
        },	{
	   		id: 'roll_name',
            name: 'roll_name',
            header: 'Rol',
            width: 150,
            dataIndex: 'roll_name',
            sortable: true
        },	{
	   		id: 'roll_description',
            name: 'roll_description',
            header: 'Descripci&oacute;n',
            width: 150,
            dataIndex: 'roll_description',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Rolls.rollsGrid = new xg.GridPanel({
        id : 'ctr-rolls-grid',
        store : rollsDataStore,
        cm : Rolls.rollsColumnMode,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Rol',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar Rol seleccionado',
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
            store: rollsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Rolls.rollsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = rollsDataStore.getAt(row).data.roll_id;
        update_ventana(selectedId);
        
    });
        
    function update_ventana(id){
	
		Rolls.rollsRecordUpdate = new Ext.data.Record.create([
	        {name: 'roll_id', type: 'int'},
	        {name: 'roll_name', type: 'string'},
	        {name: 'roll_description', type: 'string'}
	        
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
	    Rolls.rollsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'roll_id'
	        },Rolls.rollsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Rolls.Form = new Ext.FormPanel({
	        id: 'form-rolls',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 290,
	        minWidth: 290,
	        height: 120,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Rolls.rollsFormReader,
	        items: [{
			            fieldLabel : 'Rol',
			            id: 'frm_roll_name',
			            name : 'roll_name',
			            allowBlank: false,
			            xtype: 'textfield'
			        }, 	{
			            fieldLabel : 'Descripci&oacute;n',
			            id: 'frm_roll_description',
			            name : 'roll_description',
			            allowBlank: false,
			            xtype: 'textfield'
			        },	{
			            id: 'frm_roll_id',
			            name : 'roll_id',
			            xtype: 'hidden'
			        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Rolls.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Rolls.Form.getForm().submit({
	                url : baseUrl+'index.php/user/user_rolls/insert',
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
	                    rollsDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Rolls.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    rollsDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Rolls.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	            Rolls.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Rolls.Form.load({url:baseUrl+'index.php/user/user_rolls/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Rol',
				layout:'form',
				top: 200,
				width: 315,
				height: 160,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Rolls.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Rolls.rollsGrid.render(Ext.get('rolls_grid'));
    rollsDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/user/user_rolls/delete/'+array[i].get('roll_id'),
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el centro de Costo.');
				   }
				});
		    }
			sm2.clearSelections();
            rollsDataStore.load({params: {start:0,limit:15}});
    	}
    }
    
