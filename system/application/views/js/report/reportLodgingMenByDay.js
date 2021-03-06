//para pasar ademas en los baseparams pues en el boton de exportar a pdf como parametros de la funcions,bueno eso ya no lo hago asi solo dejo estas variables para pasar al stored como baseparameters, luego se podria pasr directamente el valor de los componentes
var begindate;
var center_id;
var motive_id;
var province_idlodging;
var hotel_id;


var dataRecordHotel = new Ext.data.Record.create([
						{name:'hotel_id'},
						{name:'hotel_name'}
					]);

var dataReaderHotel = new Ext.data.JsonReader({root:'data'},dataRecordHotel);

var dataProxyHotel = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_hotels/setDataByProvince',
						method: 'POST'
					});

var dataStoreHotel = new Ext.data.Store({
						proxy: dataProxyHotel,
						reader: dataReaderHotel
						//autoLoad:true
						});						

var dataRecordProv = new Ext.data.Record.create([
						{name:'province_id'},
						{name:'province_name'}
					]);
var dataReaderProv = new Ext.data.JsonReader({root:'data'},dataRecordProv);
var dataProxyProv = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_provinces/setDataGrid',
						method: 'POST'
					});
var dataStoreProv = new Ext.data.Store({
						proxy: dataProxyProv,
						reader: dataReaderProv,
						autoLoad: true
						});

var dataRecordCenter= new Ext.data.Record.create([
						{name:'center_id'},
						{name:'center_name'}
					]);

var dataReaderCenter = new Ext.data.JsonReader({root:'data'},dataRecordCenter);

var dataProxyCenter = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_costcenters/setDataGrid',
						method: 'POST'
					});

var dataStoreCenter = new Ext.data.Store({
						proxy: dataProxyCenter,
						reader: dataReaderCenter,
						autoLoad: true
						});
//
var dataRecordMotive= new Ext.data.Record.create([
						{name:'motive_id'},
						{name:'motive_name'}
					]);

var dataReaderMotive = new Ext.data.JsonReader({root:'data'},dataRecordMotive);

var dataProxyMotive = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_motives/setDataGrid',
						method: 'POST'
					});

var dataStoreMotive= new Ext.data.Store({
						proxy: dataProxyMotive,
						reader: dataReaderMotive,
						autoLoad:true
						});

var dataStorereportInternalLodging;  //las propiedades de este se ponen dentro del Ext.onReady

Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
	
	
	/*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Requests');
   
   var xg = Ext.grid;
   
	var p = new Ext.Panel({
        title: 'Reportes -> Reporte Hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });



     /*datos que recibira el grid del getdata del model que es llamado por el setdatagrid del controlador, tienen que coicidir estos name
	 con el nombre de los campos que se usan en el arreglo  devuelto pro el getdata, ojo no tienen porque coincidir todos con el nombre de los campos en la base de datos, ejemplo un id de tipo int que lo que paso en el arreglo es el nombre full tipo string,
	 cuando se ponga type debajo es una sugerencia al grid, ejemplo los datos date no se pone el tipo
 */
    Requests.dataRecordreportInternalLodging= new Ext.data.Record.create([
       // {name: 'request_id'},
		{name: 'person'},
		{name: 'identity'},
		{name: 'lodging_entrancedate'},
		{name: 'lodging_exitdate'},
		{name: 'hotel'},
		{name: 'provinceLodging'},
		{name: 'center'},
		{name: 'motive'},
		{name: 'details'}
	]);


/*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderreportInternalLodging = new Ext.data.JsonReader({
	    root: 'data',
        totalProperty: 'count',
        id: 'request_id'
		},Requests.dataRecordreportInternalLodging
    );




/*ojo ver que el que se llama aqui es el setDataConditional para el reporte, que este a su vez llama al getData Conditional, pudo haberse hecho junto pero para mejor entendimiento lo hice separado*/
/*en el getdataconditional se cogen los parametros con la funcion de asistente uri que a su vez se cargo en el autoload en los helper*/ 

    Requests.dataProxyreportInternalLodging = new Ext.data.HttpProxy({
    	url: baseUrl+'index.php/report/reports/reportLodgingMenByDay/true/no/',     //ojo hay que obligado pasarle true
    	method: 'POST'
	});


   
   dataStorereportInternalLodging= new Ext.data.Store({
	   id: 'requestDS',
	   reader :Requests.dataReaderreportInternalLodging,
	   proxy :Requests.dataProxyreportInternalLodging
   });




/*
     * Creamos el modelo de columnas para el grid (conciden campos de dataindex con los del metodo requestsRecord para ponerlos en las columnas correspondientes en las filas)
     */
  Requests.reportInternalLodgingColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
     //  	sm2,  /*es la variable que define los checkbox*/
       	//expander,
      /* {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id', //header:'Id', 
			 //no tiene header porque no se muestra
            hidden: false
        },*/
			{
    	   	id: 'person',
            name : 'person',
            header: 'Trabajador',
            width: 180,
            dataIndex: 'person',
            sortable: true
        },	{
        	id: 'identity',
            name : 'identity',
            header: 'Carnet',
            width: 100,
            dataIndex: 'identity',
            sortable: true
        },	{
            id: 'lodging_entrancedate',
            name : 'lodging_entrancedate',
            header: 'Fecha entrada',
            width: 90,
            dataIndex: 'lodging_entrancedate',
            sortable: true
        },	{
            id: 'lodging_exitdate',
            name : 'lodging_exitdate',
            header: 'Fecha salida',
            width: 90,
            dataIndex: 'lodging_exitdate',
            sortable: true
        },	{
            id: 'provinceLodging',
            name : 'provinceLodging',
            header: 'Provincia',
            width: 125,
            dataIndex: 'provinceLodging',
            sortable: true
        },	{
            id: 'hotel',
            name : 'hotel',
            header: 'Hotel',
            width: 125,
            dataIndex: 'hotel',
            sortable: true
        },	{
            id: 'center',
            name : 'center',
            header: 'Centro de costo',
            width: 125,
            dataIndex: 'center',
            sortable: true
        },	{
            id: 'motive',
            name : 'motive',
            header: 'Motivo',
            width: 200,
            dataIndex: 'motive',
            sortable: true
        },	{
            id: 'details',
            name : 'details',
            header: 'Detalles',
            width: 220,
            dataIndex: 'details',
            sortable: true
        }]
    );


    /*
     * Creamos el grid 
     */
    Requests.requestGrid = new xg.GridPanel({
        id : 'ctr-requests-grid',
        store : dataStorereportInternalLodging,
        cm : Requests.reportInternalLodgingColumnMode,
        viewConfig: {
            forceFit:false
        },
        columnLines: true,
        frame:true,
        collapsible: true,
        width : 750,
        height : 460,
        tbar:[{
            text:'Exportar a pdf',
            tooltip:'Exportar a pdf',
            iconCls:'pdf',
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function(){
					if (dataStorereportInternalLodging.getCount()>0 ){
						
						Requests.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/report/reports/reportLodgingMenByDay/true/si/' ;
						Requests.filterForm.getForm().getEl().dom.method = 'POST';
						Requests.filterForm.getForm().submit();
					} else{
						Ext.Msg.alert('Mensaje','No hay datos que exportar!');	  
					}
            }
        }],
       bbar: new Ext.PagingToolbar({
            pageSize: 50,
			store: dataStorereportInternalLodging,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    });


/////////////////hasta aqui lo del gridpanel, ahora ponermos lo del formulario
    Requests.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
		standardSubmit:true,
        frame: true,
        monitorValid: true,
        labelWidth: 130,
        height: 150,
        width: 750,
		title:'Par&aacute;metros del reporte de hospedaje hombres por dias',
        items:[{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	new Ext.form.ComboBox({
		         			store: dataStoreProv,
		           			fieldLabel: 'Provincia hospedaje',
		           			displayField: 'province_name',
		           			valueField: 'province_id',
		           			hiddenName: 'province_idlodging',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione una Provincia...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_province_idlodging',
				            name : 'province_idlodging',
							 listeners: {
								'select': function(){
											dataStoreHotel.baseParams = {province_id: Ext.getCmp('filter_province_idlodging').getValue()};
											dataStoreHotel.load();
								},
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_idlodging').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_province_idlodging').reset();
						    			return false;
						    		}
								}
					 		}
					 	}), new Ext.form.ComboBox({
		           			store: dataStoreHotel,
		           			fieldLabel: 'Hotel',
		           			displayField: 'hotel_name',
		           			valueField: 'hotel_id',
		           			hiddenName: 'hotel_id',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione un hotel...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_hotel_id',
				            name : 'hotel_id',
				            listeners: {
								'blur': function(){
									var flag = dataStoreHotel.findExact( 'hotel_id', Ext.getCmp('filter_hotel_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_hotel_id').reset();
						    			return false;
						    		}
								}
					 		}
				            
					 	}),	{
				            xtype: 'datefield',
				            width: 200,
				            allowBlank: false,
				            fieldLabel: 'Fecha',
				            name: 'begindate',
				            id: 'filter_begindate',
				            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
				            format: 'Y-m-d'
				        }	
		             ]
		    },{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	new Ext.form.ComboBox({
				   			store: dataStoreCenter,
				   			fieldLabel: 'Centro de Costo',
				   			displayField: 'center_name',
				   			valueField: 'center_id',
				   			hiddenName: 'center_id',
				   			allowBlank: true,
				   			typeAhead: true,
				   			mode: 'local',
				   			triggerAction: 'all', 
				   			emptyText: 'Seleccione un Centro de Costo...',
				   			selectOnFocus: true,
				   			width: 200,
							id: 'filter_center_id',
							name : 'center_id',
							listeners: {
								'blur': function(){
									var flag = dataStoreCenter.findExact( 'center_id', Ext.getCmp('filter_center_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_center_id').reset();
						    			return false;
						    		}
								}
					 		}
				        }),	new Ext.form.ComboBox({
			      			store: dataStoreMotive,
			      			fieldLabel: 'Motivo de solicitud',
			      			displayField: 'motive_name',
			      			valueField: 'motive_id',
			      			allowBlank: true,
			      			typeAhead: true,
			      			mode: 'local',
			      			triggerAction: 'all',
			      			emptyText: 'Seleccione motivo ...',
			      			selectOnFocus: true,
			      			width: 200,
						    id: 'filter_motive_id',
							hiddenName: 'motive_id',
				            name : 'motive_id',
				            listeners: {
								'blur': function(){
									var flag = dataStoreMotive.findExact( 'motive_id', Ext.getCmp('filter_motive_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_motive_id').reset();
						    			return false;
						    		}
								}
					 		}
						 })
		             ]
            }]
        }]
	});

	Requests.filterForm.addButton({
		id:'unfilter',					  
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
		 	Requests.filterForm.getForm().reset();
		 	dataStorereportInternalLodging.baseParams = {
			       center_id: 0,
				   motive_id:0,           
		           begindate: '1900-01-01',
				   province_idlodging:0,
				   hotel_id:0
		 	};
		 	dataStorereportInternalLodging.load({params: {start:0,limit:50}});
		 	Requests.requestGrid.getStore().reload(); //
		   
		}
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Requests.filterForm.addButton({
       	text : 'Filtrar',
		id:'filter',
       	disabled : false,
       	formBind: true,
       	handler : function() {
			motive_id = Requests.filterForm.findById('filter_motive_id').getValue();
			center_id = Requests.filterForm.findById('filter_center_id').getValue();
			begindate = Requests.filterForm.findById('filter_begindate').getValue().format('Y-m-d');
			hotel_id = Requests.filterForm.findById('filter_hotel_id').getValue();
			province_idlodging = Requests.filterForm.findById('filter_province_idlodging').getValue();
			dataStorereportInternalLodging.baseParams = {//las sig variables estan declaradas arriba arriba para futuro uso de pasar parametros apara  pdf
					center_id: center_id,
					motive_id: motive_id,
					province_idlodging:province_idlodging,
					hotel_id:hotel_id,
					begindate: begindate
           	};
           	dataStorereportInternalLodging.load({params: {start:0,limit:50}});
			Requests.requestGrid.getStore().reload();
			
       	}
   	});    
    

	Requests.filterForm.render(Ext.get('requests_grid'));
	Requests.requestGrid.render(Ext.get('requests_grid'));
    
	
});







  