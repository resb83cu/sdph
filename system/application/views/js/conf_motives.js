var motivesDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Motives');
    
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
                    Motives.motivesGrid.removeButton.enable();
                } else {
                    Motives.motivesGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Motivos',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un motive
     */
     
    Motives.motivesRecord = new Ext.data.Record.create([
        {name: 'motive_id', type: 'int'},
        {name: 'motive_name', type: 'string'},
        {name: 'motive_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Motives.motivesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'motive_id'},
        Motives.motivesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Motives.motivesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_motives/setData',
        method: 'POST'
    });

    motivesDataStore = new Ext.data.Store({
        id: 'motivesDS',
        proxy: Motives.motivesDataProxy,
        reader: Motives.motivesGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Motives.motivesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'motive_id',
            name : 'motive_id',
            dataIndex: 'motive_id',
            hidden: true
        },	{
	   		id: 'motive_name',
            name: 'motive_name',
            header: 'Motivo',
            width: 150,
            dataIndex: 'motive_name',
            sortable: true
        },	{
	   		id: 'motive_deleted',
            name: 'motive_deleted',
            header: 'Eliminado',
            renderer: state,
            width: 150,
            dataIndex: 'motive_deleted',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Motives.motivesGrid = new xg.GridPanel({
        id : 'ctr-motives-grid',
        store : motivesDataStore,
        cm : Motives.motivesColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Motivo',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Motivo seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Motivo(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: motivesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Motives.motivesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = motivesDataStore.getAt(row).data.motive_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		Motives.motivesRecordUpdate = new Ext.data.Record.create([
	        {name: 'motive_id', type: 'int'},
	        {name: 'motive_name', type: 'string'},
	        {name: 'motive_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Motives.motivesFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'motive_id'
	        },Motives.motivesRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Motives.Form = new Ext.FormPanel({
	        id: 'form-motives',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 300,
	        minWidth: 300,
	        labelWidth: 90,
	        height: 100,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Motives.motivesFormReader,
	        items: [{
			            fieldLabel : 'Motivo',
			            id: 'frm_motive_name',
			            name : 'motive_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        },	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'motive_deleted',
			   			valueField: 'motive_deleted',
			   			allowBlank: true,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_motive_deleted',
						hiddenName: 'motive_deleted',
						name : 'frm_motive_deleted'
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
			        }), {
			            id: 'frm_motive_id',
			            name : 'motive_id',
			            xtype: 'hidden'
			        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Motives.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Motives.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_motives/insert',
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
	                    motivesDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Motives.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    motivesDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Motives.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Motives.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Motives.Form.load({url:baseUrl+'index.php/conf/conf_motives/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Motivo',
				layout:'form',
				top: 200,
				width: 325,
				height: 140,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Motives.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Motives.motivesGrid.render(Ext.get('motives_grid'));
    motivesDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_motives/delete/'+array[i].get('motive_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		motivesDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Motivo.');
				   		sm2.clearSelections();
	                    motivesDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
    
