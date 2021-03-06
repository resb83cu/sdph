var viazulstatesDataStore;
var array, sm2;

Ext.onReady(function() {
    
    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('ViazulStates');
    
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
                    ViazulStates.viazulstatesGrid.removeButton.enable();
                } else {
                    ViazulStates.viazulstatesGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Estado de pasaje de Viazul',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un viazulstate
     */
     
    ViazulStates.viazulstatesRecord = new Ext.data.Record.create([
        {name: 'viazulstate_id', type: 'int'},
        {name: 'viazulstate_name', type: 'string'},
        {name: 'viazulstate_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    ViazulStates.viazulstatesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'viazulstate_id'},
        ViazulStates.viazulstatesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    ViazulStates.viazulstatesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_ticketviazulstates/setData',
        method: 'POST'
    });

    viazulstatesDataStore = new Ext.data.Store({
        id: 'viazulstatesDS',
        proxy: ViazulStates.viazulstatesDataProxy,
        reader: ViazulStates.viazulstatesGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    ViazulStates.viazulstatesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'viazulstate_id',
            name : 'viazulstate_id',
            dataIndex: 'viazulstate_id',
            hidden: true
        },	{
	   		id: 'viazulstate_name',
            name: 'viazulstate_name',
            header: 'Estado',
            width: 250,
            dataIndex: 'viazulstate_name',
            sortable: true
        },	{
	   		id: 'viazulstate_deleted',
            name: 'viazulstate_deleted',
            header: 'Eliminado',
            renderer: state,
            width: 150,
            dataIndex: 'viazulstate_deleted',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    ViazulStates.viazulstatesGrid = new xg.GridPanel({
        id : 'ctr-viazulstates-grid',
        store : viazulstatesDataStore,
        cm : ViazulStates.viazulstatesColumnMode,
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
            tooltip:'Eliminar el ViazulStatee seleccionado',
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
            store: viazulstatesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    ViazulStates.viazulstatesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = viazulstatesDataStore.getAt(row).data.viazulstate_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		ViazulStates.viazulstatesRecordUpdate = new Ext.data.Record.create([
	        {name: 'viazulstate_id', type: 'int'},
	        {name: 'viazulstate_name', type: 'string'},
	        {name: 'viazulstate_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    ViazulStates.viazulstatesFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'viazulstate_id'
	        },ViazulStates.viazulstatesRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    ViazulStates.Form = new Ext.FormPanel({
	        id: 'form-viazulstates',
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
	        reader: ViazulStates.viazulstatesFormReader,
	        items: [{
			            fieldLabel : 'Estado',
			            id: 'frm_viazulstate_name',
			            name : 'viazulstate_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        }, new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'viazulstate_deleted',
			   			valueField: 'viazulstate_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_viazulstate_deleted',
						hiddenName: 'viazulstate_deleted',
						name : 'frm_viazulstate_deleted'
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
			            id: 'frm_viazulstate_id',
			            name : 'viazulstate_id',
			            xtype: 'hidden'
			        }]
			
	    });
	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    ViazulStates.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            ViazulStates.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_ticketviazulstates/insert',
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
	                    viazulstatesDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    ViazulStates.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    viazulstatesDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    ViazulStates.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            ViazulStates.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	ViazulStates.Form.load({url:baseUrl+'index.php/conf/conf_ticketviazulstates/getById/'+id});
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
				items: ViazulStates.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	ViazulStates.viazulstatesGrid.render(Ext.get('viazulstates_grid'));
    viazulstatesDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_ticketviazulstates/delete/'+array[i].get('viazulstate_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		viazulstatesDataStore.load({params: {start:0,limit:15}});
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
				   		viazulstatesDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
    
