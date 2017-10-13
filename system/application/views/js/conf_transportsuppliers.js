var suppliersDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Suppliers');
    
    function state(val){
        if(val == 'no'){
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
                    Suppliers.suppliersGrid.removeButton.enable();
                } else {
                    Suppliers.suppliersGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Proveedores de Transporte',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un supplier
     */
     
    Suppliers.suppliersRecord = new Ext.data.Record.create([
        {name: 'supplier_id', type: 'int'},
        {name: 'supplier_name', type: 'string'},
        {name: 'supplier_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Suppliers.suppliersGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'supplier_id'},
        Suppliers.suppliersRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Suppliers.suppliersDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_transportsuppliers/setDataGrid',
        method: 'POST'
    });

    suppliersDataStore = new Ext.data.Store({
        id: 'suppliersDS',
        proxy: Suppliers.suppliersDataProxy,
        reader: Suppliers.suppliersGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Suppliers.suppliersColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'supplier_id',
            name : 'supplier_id',
            dataIndex: 'supplier_id',
            hidden: true
        },	{
	   		id: 'supplier_name',
            name: 'supplier_name',
            header: 'Proveedor',
            width: 250,
            dataIndex: 'supplier_name',
            sortable: true
        },	{
	   		id: 'supplier_deleted',
            name: 'supplier_deleted',
            header: 'Eliminado',
            renderer: state,
            width: 150,
            dataIndex: 'supplier_deleted',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Suppliers.suppliersGrid = new xg.GridPanel({
        id : 'ctr-suppliers-grid',
        store : suppliersDataStore,
        cm : Suppliers.suppliersColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Proveedor',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Proveedor seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Proveedor(es)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: suppliersDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Suppliers.suppliersGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = suppliersDataStore.getAt(row).data.supplier_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		Suppliers.suppliersRecordUpdate = new Ext.data.Record.create([
	        {name: 'supplier_id', type: 'int'},
	        {name: 'supplier_name', type: 'string'},
	        {name: 'supplier_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Suppliers.suppliersFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'supplier_id'
	        },Suppliers.suppliersRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Suppliers.Form = new Ext.FormPanel({
	        id: 'form-suppliers',
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
	        reader: Suppliers.suppliersFormReader,
	        items: [{
			            fieldLabel : 'Proveedor',
			            id: 'frm_supplier_name',
			            name : 'supplier_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        }, 	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'supplier_deleted',
			   			valueField: 'supplier_deleted',
			   			allowBlank: false,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_supplier_deleted',
						hiddenName: 'supplier_deleted',
						name : 'frm_supplier_deleted'
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
			            id: 'frm_supplier_id',
			            name : 'supplier_id',
			            xtype: 'hidden'
			        }]
			
	    });
	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Suppliers.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Suppliers.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_transportsuppliers/insert',
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
	                    suppliersDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Suppliers.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    suppliersDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Suppliers.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Suppliers.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });
	
	  	var title = 'Agregar';
		if (id > 0){
        	Suppliers.Form.load({url:baseUrl+'index.php/conf/conf_transportsuppliers/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Proveedor',
				layout:'form',
				top: 200,
				width: 325,
				height: 120,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Suppliers.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Suppliers.suppliersGrid.render(Ext.get('suppliers_grid'));
    suppliersDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_transportsuppliers/delete/'+array[i].get('supplier_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		suppliersDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Proveedor.');
				   }
				});
		    }
	   		sm2.clearSelections();
	   		suppliersDataStore.load({params: {start:0,limit:15}});
    	}
    }
    
