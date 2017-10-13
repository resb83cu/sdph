var newsDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('News');
    
   	var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    News.newsGrid.removeButton.enable();
                } else {
                    News.newsGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuracion -> Noticias',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un center
     */
     
    News.newsRecord = new Ext.data.Record.create([
        {name: 'new_id', type: 'int'},
		 {name: 'priority', type: 'int'},
		{name: 'dateput'},
        {name: 'name', type: 'string'},
		{name: 'content', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    News.newsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'new_id'},
        News.newsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    News.newsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/newsAdmin/setDataGrid',
        method: 'POST'
    });

    newsDataStore = new Ext.data.Store({
        id: 'NewsDS',
        proxy: News.newsDataProxy,
        reader: News.newsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    News.NewsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'new_id',
            name : 'new_id',
            dataIndex: 'new_id',
            hidden: true
        },{
            id: 'priority',
			header: 'Prioridad',
            name : 'priority',
            dataIndex: 'priority',
            
			sortable:true
        },{
	   		id: 'dateput',
            name: 'dateput',
            header: 'Fecha Actualizacion',
            width: 150,
            dataIndex: 'dateput',
            sortable: true
        },
		{
            id: 'name',
            name : 'pame',
			header: 'Nombre a mostrar',
            width: 150,
            dataIndex: 'name',
            sortable: true
        },{
            id: 'content',
            name : 'content',
			header: 'Contenido',
            width: 450,
            dataIndex: 'content',
            sortable: true
        }
		]
    );


    /*
     * Creamos el grid de movimientos
     */
    News.newsGrid = new xg.GridPanel({
        id : 'ctr-News-grid',
        store : newsDataStore,
        cm : News.NewsColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nueva noticia',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar Noticia seleccionada',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(as) Noticia(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: newsDataStore,
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
   
   
   
    News.newsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = newsDataStore.getAt(row).data.new_id;
        update_ventana(selectedId);
        
    });
   
    function update_ventana(id){
	
		News.newsRecordUpdate = new Ext.data.Record.create([
	        {name: 'new_id', type: 'int'},
			//{name: 'dateput'}, //coge el actual dels ervidor en el model
	        {name: 'name', type: 'string'},
			{name: 'priority', type: 'int'},
			{name: 'content', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
	    News.NewsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'new_id'
	        },News.newsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    News.Form = new Ext.FormPanel({
	        
			
			fileUpload: true,
			
			id: 'form-News',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 900,
	        minWidth: 290,
	        height: 250,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: News.NewsFormReader,
			defaults: {
            anchor: '95%',
            allowBlank: false,
            msgTarget: 'side'
        },

	        items: [
					{
			           xtype: 'textfield',
		               width: 180,
		               allowBlank: false,
		               fieldLabel: 'Nombre a mostrar',
		               name: 'name',
		               id: 'frm_name'
				    },
					{
			           xtype: 'textfield',
		               anchor: '15%',
					   maxLength:3,
		               allowBlank: false,
		               fieldLabel: 'Prioridad',
		               name: 'priority',
		               id: 'frm_priority'
				    }
					,
					{
						xtype: 'htmleditor',
					fieldLabel:'Contenido',
					 allowBlank: false,
					 name:'content',
					  heigth:350,
					  width:80,
                     enableColors: true,
                     enableAlignments: false
					}
			               
					
					, {
			            id: 'frm_new_id',
			            name : 'new_id',
			            xtype: 'hidden'
			        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    News.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            News.Form.getForm().submit({
	                url : baseUrl+'index.php/newsAdmin/insert',
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
	                    newsDataStore.load({params: {start:0,limit:15}});	                    
	                },
	                success: function (form, request) {
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    News.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    newsDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    News.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            News.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	News.Form.load({url:baseUrl+'index.php/newsAdmin/getById/'+id});
        	title = 'Editar';
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Noticia',
				layout:'form',
				top: 200,
				width: 910,
				height: 280,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: News.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	News.newsGrid.render(Ext.get('news_grid'));
    newsDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/newsAdmin/delete/'+array[i].get('new_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		newsDataStore.load({params: {start:0,limit:15}});
				   		Ext.MessageBox.show({
	                        title: 'Datos eliminados correctamente',
	                        msg: 'Datos eliminados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
				   },
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la noticia.');
				   }
				});
		    }
			sm2.clearSelections();
            newsDataStore.load({params: {start:0,limit:15}});
    	}
    }
