//para pasar ademas en los baseparams pues en el boton de exportar a pdf como parametros de la funcions,bueno eso ya no lo hago asi solo dejo estas variables para pasar al stored como baseparameters, luego se podria pasr directamente el valor de los componentes
var begindate;

var dataStoreReportInternalTicket;  //las propiedades de este se ponen dentro del Ext.onReady

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
        title: 'Reportes -> Reporte Merienda',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });

    Requests.dataRecordReportInternalTicket = new Ext.data.Record.create([
		{name: 'person'},
		{name: 'identity'},
		{name: 'ticket_date'},
		{name: 'provinceFrom'},
		{name: 'provinceTo'},
		{name: 'estado'}
	]);


/*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderReportInternalTicket = new Ext.data.JsonReader({
	    root: 'data',
        totalProperty: 'count'},
        Requests.dataRecordReportInternalTicket
    );
/*ojo ver que el que se llama aqui es el setDataConditional para el reporte, que este a su vez llama al getData Conditional, pudo haberse hecho junto pero para mejor entendimiento lo hice separado*/
/*en el getdataconditional se cogen los parametros con la funcion de asistente uri que a su vez se cargo en el autoload en los helper*/ 

	Requests.dataProxyReportInternalTicket = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/report/reports/reportSnack/true/no/',
		 method: 'POST'
	});
   
   dataStoreReportInternalTicket= new Ext.data.Store({
	   id: 'requestDS',
       reader :Requests.dataReaderReportInternalTicket,
	   proxy :Requests.dataProxyReportInternalTicket
   });

/*
     * Creamos el modelo de columnas para el grid (conciden campos de dataindex con los del metodo requestsRecord para ponerlos en las columnas correspondientes en las filas)
     */
  Requests.ReportInternalTicketColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
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
           id: 'ticket_date',
           name : 'ticket_date',
           header: 'Fecha de Viaje',
           width: 90,
           dataIndex: 'ticket_date',
           sortable: true
       },	{
           id: 'provinceFrom',
           name : 'provinceFrom',
           header: 'Origen',
           width: 125,
           dataIndex: 'provinceFrom',
           sortable: true
       },	{
           id: 'provinceTo',
           name : 'provinceTo',
           header: 'Destino',
           width: 125,
           dataIndex: 'provinceTo',
           sortable: true
       },	{
           id: 'estado',
           name : 'estado',
           header: 'Transporte',
           width: 125,
           dataIndex: 'estado',
           sortable: true
       }]
    );


    /*
     * Creamos el grid 
     */
    Requests.requestGrid = new xg.GridPanel({
        id : 'ctr-requests-grid',
        store : dataStoreReportInternalTicket,
        cm : Requests.ReportInternalTicketColumnMode,
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
            disabled: false,
            handler: function(){
					if (dataStoreReportInternalTicket.getCount()>0 ){
				          Requests.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/report/reports/reportSnack/true/si/' ;
	                      Requests.filterForm.getForm().getEl().dom.method = 'POST';
                          Requests.filterForm.getForm().submit();
					} else{
						  Ext.Msg.alert('Mensaje','No hay datos que exportar!');	  
					}
            }
        }],
       bbar: new Ext.PagingToolbar({
			store: dataStoreReportInternalTicket,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    });


    Requests.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
		standardSubmit:true,
        frame: true,
        monitorValid: true,
        labelWidth: 140,
        height: 100,
        width: 750,
		title:'Par&aacute;metros del reporte de meriendas',
        items:[{
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

	});


	Requests.filterForm.addButton({
		id:'unfilter',					  
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
		 	Requests.filterForm.getForm().reset();
		 	dataStoreReportInternalTicket.baseParams = {  
		           begindate: '1900-01-01'
		 	};
		 	dataStoreReportInternalTicket.load();
		 	Requests.requestGrid.getStore().reload();
       }
   });

   /*
    * A�adimos el boton para filtrar
    */
   	Requests.filterForm.addButton({
       	text : 'Filtrar',
		id:'filter',
       	disabled : false,
       	formBind: true,
       	handler : function() {
   			begindate = Requests.filterForm.findById('filter_begindate').getValue().format('Y-m-d');
			dataStoreReportInternalTicket.baseParams = {          
				begindate: begindate
           	};
           	dataStoreReportInternalTicket.load();
			Requests.requestGrid.getStore().reload();
			
       	}
		
   	});    
	
	Requests.filterForm.render(Ext.get('requests_grid'));
	Requests.requestGrid.render(Ext.get('requests_grid'));
	
});