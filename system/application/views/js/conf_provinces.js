var provincesDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Provinces');
    
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
                    Provinces.provincesGrid.removeButton.enable();
                } else {
                    Provinces.provincesGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Provincias',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro para un province
     */
     
    Provinces.provincesRecord = new Ext.data.Record.create([
        {name: 'province_id', type: 'int'},
        {name: 'province_name', type: 'string'},
        {name: 'province_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Provinces.provincesGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'province_id'},
        Provinces.provincesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Provinces.provincesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_provinces/setData',
        method: 'POST'
    });

    provincesDataStore = new Ext.data.Store({
        id: 'provincesDS',
        proxy: Provinces.provincesDataProxy,
        reader: Provinces.provincesGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Provinces.provincesColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'province_id',
            name : 'province_id',
            dataIndex: 'province_id',
            hidden: true
        },	{
	   		id: 'province_name',
            name: 'province_name',
            header: 'Provincia',
            width: 250,
            dataIndex: 'province_name',
            sortable: true
        },	{
	   		id: 'province_deleted',
            name: 'province_deleted',
            header: 'Eliminado',
            renderer: state,
            width: 150,
            dataIndex: 'province_deleted',
            sortable: true
        }]
    );


    /*
     * Creamos el grid
     */
    Provinces.provincesGrid = new xg.GridPanel({
        id : 'ctr-provinces-grid',
        store : provincesDataStore,
        cm : Provinces.provincesColumnMode,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Provincia',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar el Provincia seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) Provincia(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 30,
            store: provincesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Provinces.provincesGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = provincesDataStore.getAt(row).data.province_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		Provinces.provincesRecordUpdate = new Ext.data.Record.create([
	        {name: 'province_id', type: 'int'},
	        {name: 'province_name', type: 'string'},
	        {name: 'province_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Provinces.provincesFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'province_id'
	        },Provinces.provincesRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Provinces.Form = new Ext.FormPanel({
	        id: 'form-provinces',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 300,
	        minWidth: 300,
	        labelWidth: 100,
	        height: 100,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Provinces.provincesFormReader,
	        items: [{
			            fieldLabel : 'Provincia',
			            id: 'frm_province_name',
			            name : 'province_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        },	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'province_deleted',
			   			valueField: 'province_deleted',
			   			allowBlank: false,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_province_deleted',
						hiddenName: 'province_deleted',
						name : 'frm_province_deleted'
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
			            id: 'frm_province_id',
			            name : 'province_id',
			            xtype: 'hidden'
			        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Provinces.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Provinces.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_provinces/insert',
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
	                    provincesDataStore.load({params: {start:0,limit:30}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Provinces.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    provincesDataStore.load({params: {start:0,limit:30}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Provinces.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Provinces.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Provinces.Form.load({url:baseUrl+'index.php/conf/conf_provinces/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Provincia',
				layout:'form',
				top: 200,
				width: 325,
				height: 140,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Provinces.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Provinces.provincesGrid.render(Ext.get('provinces_grid'));
    provincesDataStore.load({params: {start:0,limit:30}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_provinces/delete/'+array[i].get('province_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		provincesDataStore.load({params: {start:0,limit:30}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Provincia.');
				   		sm2.clearSelections();
	                    provincesDataStore.load({params: {start:0,limit:30}});
				   }
				});
		    }
    	}
    }