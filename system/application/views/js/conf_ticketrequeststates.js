var statesDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('States');
    
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
                    States.statesGrid.removeButton.enable();
                } else {
                    States.statesGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Estado de Pasajes',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un state
     */
     
    States.statesRecord = new Ext.data.Record.create([
        {name: 'state_id', type: 'int'},
        {name: 'state_name', type: 'string'},
        {name: 'state_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    States.statesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'state_id'},
        States.statesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    States.statesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_ticketrequeststates/setData',
        method: 'POST'
    });

    statesDataStore = new Ext.data.Store({
        id: 'statesDS',
        proxy: States.statesDataProxy,
        reader: States.statesGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    States.statesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'state_id',
            name : 'state_id',
            dataIndex: 'state_id',
            hidden: true
        },	{
	   		id: 'state_name',
            name: 'state_name',
            header: 'Estado',
            width: 250,
            dataIndex: 'state_name',
            sortable: true
        },	{
	   		id: 'state_deleted',
            name: 'state_deleted',
            header: 'Eliminado',
            renderer: state,
            width: 150,
            dataIndex: 'state_deleted',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    States.statesGrid = new xg.GridPanel({
        id : 'ctr-states-grid',
        store : statesDataStore,
        cm : States.statesColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Estado',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Statee seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Estado(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: statesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    States.statesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = statesDataStore.getAt(row).data.state_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		States.statesRecordUpdate = new Ext.data.Record.create([
	        {name: 'state_id', type: 'int'},
	        {name: 'state_name', type: 'string'},
	        {name: 'state_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    States.statesFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'state_id'
	        },States.statesRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    States.Form = new Ext.FormPanel({
	        id: 'form-states',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 300,
	        minWidth: 300,
	        labelWidth: 120,
	        height: 100,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: States.statesFormReader,
	        items: [{
			            fieldLabel : 'Estado',
			            id: 'frm_state_name',
			            name : 'state_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        },	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'state_deleted',
			   			valueField: 'state_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_state_deleted',
						hiddenName: 'state_deleted',
						name : 'frm_state_deleted'
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
			        }),	{
			            id: 'frm_state_id',
			            name : 'state_id',
			            xtype: 'hidden'
			        }]
			
	    });
	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    States.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            States.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_ticketrequeststates/insert',
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
	            		statesDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    States.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    statesDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    States.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	            States.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	States.Form.load({url:baseUrl+'index.php/conf/conf_ticketrequeststates/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Estado',
				layout:'form',
				top: 200,
				width: 325,
				height: 140,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: States.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	States.statesGrid.render(Ext.get('states_grid'));
    statesDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_ticketrequeststates/delete/'+array[i].get('state_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		statesDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Estado.');
	            		sm2.clearSelections();
	            		statesDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
    
