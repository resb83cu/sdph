var documentsDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Documents');
    
 
   	var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Documents.documentsGrid.removeButton.enable();
                } else {
                    Documents.documentsGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuracion -> Documentos',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un center
     */
     
    Documents.documentsRecord = new Ext.data.Record.create([
        {name: 'document_id', type: 'int'},
		{name: 'dateput'},
        {name: 'pathname', type: 'string'},
		{name: 'name', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Documents.documentsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'document_id'},
        Documents.documentsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Documents.documentsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/documentsAdmin/setDataGrid',
        method: 'POST'
    });

    documentsDataStore = new Ext.data.Store({
        id: 'DocumentsDS',
        proxy: Documents.documentsDataProxy,
        reader: Documents.documentsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Documents.DocumentsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'document_id',
            name : 'document_id',
            dataIndex: 'document_id',
            hidden: true
        },{
	   		id: 'dateput',
            name: 'dateput',
            header: 'Fecha Actualizacion',
            width: 150,
            dataIndex: 'dateput',
            sortable: true
        },
		{
            id: 'pathname',
            name : 'pathname',
			header: 'Nombre Camino',
            width: 150,
            dataIndex: 'pathname',
            sortable: true
        }
		,
		{
            id: 'name',
            name : 'pame',
			header: 'Nombre a mostrar',
            width: 150,
            dataIndex: 'name',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Documents.documentsGrid = new xg.GridPanel({
        id : 'ctr-Documents-grid',
        store : documentsDataStore,
        cm : Documents.DocumentsColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nuevo Documento( Hasta 2 MB)',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar Documento seleccionado',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Documento(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: documentsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
   
   
   
   
   
   
   
   //aqui no hay actualizacion
   
   
   
   /* Documents.documentsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = documentsDataStore.getAt(row).data.document_id;
        update_ventana(selectedId);
        
    });
    */
    function update_ventana(id){
	
		Documents.documentsRecordUpdate = new Ext.data.Record.create([
	        {name: 'document_id', type: 'int'},
			//{name: 'dateput'},
	        {name: 'name', type: 'string'},
			{name: 'pathname', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
	    Documents.DocumentsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'document_id'
	        },Documents.documentsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Documents.Form = new Ext.FormPanel({
	        
			
			fileUpload: true,
			
			id: 'form-Documents',
	        region: 'west',
	        labelWidth: 120,
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 380,
	        minWidth: 290,
	        height: 120,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Documents.DocumentsFormReader,
			defaults: {
            anchor: '95%',
            allowBlank: false,
            msgTarget: 'side'
        },

	        items: [
					
			{
			xtype: 'fileuploadfield',
            id: 'form_file',
            emptyText: 'Seleccione un documento',
            fieldLabel: 'Documento',
            name: 'pathname',
			anchor: '95%',
			buttonText: '',
            buttonCfg: {
                iconCls: 'grid'
            }
			
     	     }

                     ,
					{
			           xtype: 'textfield',
		               width: 180,
		               allowBlank: false,
		               fieldLabel: 'Nombre a mostrar',
		               name: 'name',
		               id: 'frm_name'
				    }
					, {
			            id: 'frm_document_id',
			            name : 'document_id',
			            xtype: 'hidden'
			        }]
			
	    });
	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Documents.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Documents.Form.getForm().submit({
	                url : baseUrl+'index.php/documentsAdmin/insert',
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
	                    documentsDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Documents.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    documentsDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Documents.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Documents.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Documents.Form.load({url:baseUrl+'index.php/documentsAdmin/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Documento',
				layout:'form',
				top: 200,
				width: 405,
				height: 160,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Documents.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Documents.documentsGrid.render(Ext.get('documents_grid'));
    documentsDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/documentsAdmin/delete/'+array[i].get('document_id'),
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
		        		sm2.clearSelections();
				   		documentsDataStore.load({params: {start:0,limit:15}});
				   },
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar el Documento.');
				   		sm2.clearSelections();
	                    documentsDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
