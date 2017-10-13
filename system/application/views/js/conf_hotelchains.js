var chainsDataStore;
var array, sm2;

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Chains');
    
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
                    Chains.chainsGrid.removeButton.enable();
                } else {
                    Chains.chainsGrid.removeButton.disable();
                }
            }
        }
    });
    

    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Cadena Hotelera',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un chain
     */
     
    Chains.chainsRecord = new Ext.data.Record.create([
        {name: 'chain_id', type: 'int'},
        {name: 'chain_name', type: 'string'},
        {name: 'chain_deleted', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Chains.chainsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'chain_id'},
        Chains.chainsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Chains.chainsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_hotelchains/setData',
        method: 'POST'
    });

    chainsDataStore = new Ext.data.Store({
        id: 'chainsDS',
        proxy: Chains.chainsDataProxy,
        reader: Chains.chainsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Chains.chainsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'chain_id',
            name : 'chain_id',
            dataIndex: 'chain_id',
            hidden: true
        },{
	   		id: 'chain_name',
            name: 'chain_name',
            header: 'Cadena Hotelera',
            width: 150,
            dataIndex: 'chain_name',
            sortable: true
        },	{
	   		id: 'frm_chain_deleted',
            name: 'chain_deleted',
            header: 'Eliminado',
            renderer: state,
            width: 150,
            dataIndex: 'chain_deleted',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Chains.chainsGrid = new xg.GridPanel({
        id : 'ctr-chains-grid',
        store : chainsDataStore,
        cm : Chains.chainsColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar nueva Cadena Hotelera',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar la Cadena Hotelera seleccionada',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) Cadena(s) hotelera(s)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: chainsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Chains.chainsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = chainsDataStore.getAt(row).data.chain_id;
        update_ventana(selectedId);
        
    });
    
    function update_ventana(id){
	
		Chains.chainsRecordUpdate = new Ext.data.Record.create([
	        {name: 'chain_id', type: 'int'},
	        {name: 'chain_name', type: 'string'},
	        {name: 'chain_deleted', type: 'string'}
	    ]);
	    
	    /*
	     * Creamos el reader para el formulario de alta/modificaci�n de movimientos
	     */
	    Chains.chainsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'chain_id'
	        },Chains.chainsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n
	     */

	    Chains.Form = new Ext.FormPanel({
	        id: 'form-chains',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        width: 290,
	        minWidth: 290,
	        height: 110,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: Chains.chainsFormReader,
	        items: [{
			            fieldLabel : 'Cadena Hotelera',
			            id: 'frm_chain_name',
			            name : 'chain_name',
			            allowBlank:false,
			            xtype: 'textfield'
			        },	new Ext.form.ComboBox({
			   			store:  ['No','Si'],
			   			fieldLabel: 'Eliminado',
			   			displayField: 'chain_deleted',
			   			valueField: 'chain_deleted',
			   			allowBlank: false,
			   			typeAhead: true,
			   			readOnly: true,
			   			mode: 'local',
			   			triggerAction: 'all',
			   			selectOnFocus: true,
			   			width: 50,
						id: 'frm_chain_deleted',
						hiddenName: 'chain_deleted',
						name : 'frm_chain_deleted'
			        }), {
			            id: 'frm_chain_id',
			            name : 'chain_id',
			            xtype: 'hidden'
			        }]
			
	    });

	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Chains.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Chains.Form.getForm().submit({
	                url : baseUrl+'index.php/conf/conf_hotelchains/insert',
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
	                    chainsDataStore.load({params: {start:0,limit:15}});
	                },
	                success: function (form, request) {
	                    Ext.MessageBox.show({
	                        title: 'Datos salvados correctamente',
	                        msg: 'Datos salvados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
	                    responseData = Ext.util.JSON.decode(request.response.responseText);
	                    Chains.Form.getForm().reset();
	                    updateWindow.destroy();
	                    sm2.clearSelections();
	                    chainsDataStore.load({params: {start:0,limit:15}});
	                }
	            });
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    Chains.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Chains.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	
	  	var title = 'Agregar';
		if (id > 0){
        	Chains.Form.load({url:baseUrl+'index.php/conf/conf_hotelchains/getById/'+id});
        	title = 'Editar';
		} else {
			Ext.getCmp('frm_chain_deleted').disable();
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Cadena Hotelera',
				layout:'form',
				top: 200,
				width: 315,
				height: 150,
				modal: true,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: Chains.Form
				
				});
			}
		updateWindow.show(this);
	
	}
   
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Chains.chainsGrid.render(Ext.get('chains_grid'));
    chainsDataStore.load({params: {start:0,limit:15}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/conf/conf_hotelchains/delete/'+array[i].get('chain_id'),
				   method: 'GET',
				   disableCaching: false,
				   success: function(){
				   		chainsDataStore.load({params: {start:0,limit:15}});
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
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la Cadena Hotelera.');
				   		sm2.clearSelections();
	                    chainsDataStore.load({params: {start:0,limit:15}});
				   }
				});
		    }
    	}
    }
    
