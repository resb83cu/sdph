var transportsDataStore;
var array;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Transports');
    

   	var xg = Ext.grid;

    var sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Transports.transportsGrid.removeButton.enable();
                } else {
                    Transports.transportsGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Tipo de Transporte de Hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un transport
     */
     
    Transports.transportsRecord = new Ext.data.Record.create([
        {name: 'transport_id', type: 'int'},
        {name: 'transport_name', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Transports.transportsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'transport_id'},
        Transports.transportsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Transports.transportsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_lodgingtransports/setDataGrid',
        method: 'POST'
    });

    transportsDataStore = new Ext.data.Store({
        id: 'transportsDS',
        proxy: Transports.transportsDataProxy,
        reader: Transports.transportsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Transports.transportsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'transport_id',
            name : 'transport_id',
            dataIndex: 'transport_id',
            hidden: true
        },{
	   		id: 'transport_name',
            name: 'transport_name',
            header: 'Tipo de Transporte',
            width: 150,
            dataIndex: 'transport_name',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Transports.transportsGrid = new xg.GridPanel({
        id : 'ctr-transports-grid',
        store : transportsDataStore,
        cm : Transports.transportsColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Tipo de Transporte',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Transporte seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Tipo(s) de Transporte?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 30,
            store: transportsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Transports.transportsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = transportsDataStore.getAt(row).data.transport_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		Transports.transportsRecordUpdate = new Ext.data.Record.create([
	        {name: 'transport_id', type: 'int'},
	        {name: 'transport_name', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Transports.transportsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'transport_id'
	        },Transports.transportsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Transports.Form = new Ext.FormPanel({
	        id: 'form-transports',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 300,
	        minWidth: 300,
	        labelWidth: 120,
	        height: 80,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Transports.transportsFormReader,
	        items: [{
			            fieldLabel : 'Tipo de Transporte',
			            id: 'frm_transport_name',
			            name : 'transport_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        }, {
			            id: 'frm_transport_id',
			            name : 'transport_id',
			            xtype: 'hidden'
			        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Transports.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Transports.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_lodgingtransports/insert',
	                waitMsg : 'Salvando datos...',
	                failure: function (form, action) {
	                    Ext.MessageBox.show({
	                        title: 'Error al salvar los datos',
	                        msg: 'Error al salvar los datos.',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.ERROR
	                    });
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Transports.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    transportsDataStore.load({params: {start:0,limit:30}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Transports.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Transports.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Transports.Form.load({url:baseUrl+'index.php/conf/conf_lodgingtransports/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Tipo de Transporte',
				layout:'form',
				top: 200,
				width: 325,
				height: 120,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Transports.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Transports.transportsGrid.render(Ext.get('transports_grid'));
    transportsDataStore.load({params: {start:0,limit:30}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_lodgingtransports/delete/'+array[i].get('transport_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		transportsDataStore.load({params: {start:0,limit:30}});
				   		Ext.MessageBox.show({
	                        title: 'Datos eliminados correctamente',
	                        msg: 'Datos eliminados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
				   },
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Tipo de Transporte.');
				   }
				});
		    }
    	}
    }
    
