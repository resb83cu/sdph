var dataStoreRequest;

Date.patterns = {
	    ISO8601Long:"Y-m-d H:i:s",
	    ISO8601Short:"Y-m-d",
	    ShortDate: "n/j/Y",
	    LongDate: "l, F d, Y",
	    FullDateTime: "l, F d, Y g:i:s A",
	    MonthDay: "F d",
	    ShortTime: "g:i A",
	    LongTime: "g:i:s A",
	    SortableDateTime: "Y-m-d\\TH:i:s",
	    UniversalSortableDateTime: "Y-m-d H:i:sO",
	    YearMonth: "F, Y"
	};
	var dt = new Date();
	var today = dt.format(Date.patterns.ISO8601Short);


Ext.apply(Ext.form.VTypes, {
		  
    daterange : function(val, field) {
        var date = field.parseDate(val);

        if(!date){
            return;
        }
        if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
            var start = Ext.getCmp(field.startDateField);
            start.setMaxValue(date);
            start.validate();
            this.dateRangeMax = date;
        } 
        else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
            var end = Ext.getCmp(field.endDateField);
            end.setMinValue(date);
            end.validate();
            this.dateRangeMin = date;
        }
        /*
         * Always return true since we're only using this vtype to set the
         * min/max allowed values (these are tested for after the vtype test)
         */
        return true;
    }    
    
});

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
        title: 'Reportes -> Reporte de Cantidad de hospedados por Centro de Costo en cada Provincia.',
        collapsible:false,
        renderTo: 'panel-basic',
        width:800,
        bodyCfg: {
	    }
    });

    Requests.dataRecordRequest= new Ext.data.Record.create([
        {name: 'center'},
        {name: 'pri'},
        {name: 'art'},
        {name: 'myb'},
        {name: 'hab'},
        {name: 'mtz'},
		{name: 'vcl'},
		{name: 'cfg'},
		{name: 'ssp'},
		{name: 'cav'},
		{name: 'cmg'},
		{name: 'ltu'},
		{name: 'hol'},
		{name: 'grm'},
		{name: 'scu'},
		{name: 'gtm'},
		{name: 'isj'},
		{name: 'total'}
	]);
/*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderRequest = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'request_id'},
        Requests.dataRecordRequest
    );


    Requests.dataProxyRequest = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/request/request_requests/getAll',
        method: 'POST'
    });
   
	dataStoreRequest= new Ext.data.Store({
		id: 'requestDS',
		proxy: Requests.dataProxyRequest,
		reader: Requests.dataReaderRequest
	});

	/*
     * Creamos el modelo de columnas para el grid (conciden campos de dataindex con los del metodo requestsRecord para ponerlos en las columnas correspondientes en las filas)
     */
	Requests.lodgingRequestColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       {
            id: 'center',
            name : 'center',
            header: 'Centro Contable',
            dataIndex: 'center',
            width: 100,
            sortable: true
        },	{
	   		id: 'pri',
            name: 'pri',
            header: 'PRI',
            width: 40,
            dataIndex: 'pri',
            sortable: false
        },	{
	   		id: 'art',
            name: 'art',
            header: 'ART',
            width: 40,
            dataIndex: 'art',
            sortable: false
        },	{
	   		id: 'myb',
            name: 'myb',
            header: 'MYB',
            width: 40,
            dataIndex: 'myb',
            sortable: false
        },	{
	   		id: 'hab',
            name: 'hab',
            header: 'HAB',
            width: 40,
            dataIndex: 'hab',
            sortable: false
        },	{
	   		id: 'mtz',
            name: 'mtz',
            header: 'MTZ',
            width: 40,
            dataIndex: 'mtz',
            sortable: false
        },	{
	   		id: 'vcl',
            name: 'vcl',
            header: 'VCL',
            width: 40,
            dataIndex: 'vcl',
            sortable: false
        },	{
	   		id: 'cfg',
            name: 'cfg',
            header: 'CFG',
            width: 40,
            dataIndex: 'cfg',
            sortable: false
        },	{
	   		id: 'ssp',
            name: 'ssp',
            header: 'SSP',
            width: 40,
            dataIndex: 'ssp',
            sortable: false
        },	{
	   		id: 'cav',
            name: 'cav',
            header: 'CAV',
            width: 40,
            dataIndex: 'cav',
            sortable: false
        },	{
	   		id: 'cmg',
            name: 'cmg',
            header: 'CMG',
            width: 40,
            dataIndex: 'cmg',
            sortable: false
        },	{
	   		id: 'ltu',
            name: 'ltu',
            header: 'LTU',
            width: 40,
            dataIndex: 'ltu',
            sortable: false
        },	{
	   		id: 'hol',
            name: 'hol',
            header: 'HOL',
            width: 40,
            dataIndex: 'hol',
            sortable: false
        },	{
	   		id: 'grm',
            name: 'grm',
            header: 'GRM',
            width: 40,
            dataIndex: 'grm',
            sortable: false
        },	{
	   		id: 'scu',
            name: 'scu',
            header: 'SCU',
            width: 40,
            dataIndex: 'scu',
            sortable: false
        },	{
	   		id: 'gtm',
            name: 'gtm',
            header: 'GTM',
            width: 40,
            dataIndex: 'gtm',
            sortable: false
        },	{
	   		id: 'isj',
            name: 'isj',
            header: 'ISJ',
            width: 40,
            dataIndex: 'isj',
            sortable: false
        },	{
	   		id: 'total',
            name: 'total',
            header: 'TOTAL',
            width: 60,
            dataIndex: 'total',
            sortable: false
        }]
    );

    /*
     * Creamos el grid 
     */
    Requests.requestGrid = new xg.GridPanel({
        id : 'ctr-requests-grid',
		stripeRows: true,
        store : dataStoreRequest,
        cm : Requests.lodgingRequestColumnMode,
        viewConfig: {
            forceFit:false
        },
        columnLines: true,
        frame:true,
        collapsible: true,
        width : 800,
        height : 500,
        tbar:[{
            text:'Exportar a pdf',
            tooltip:'Exportar a pdf',
            iconCls:'pdf',
          //  ref: '../exportButton', //para hacer referencia a este boton en otro lugar en este caso desde el 
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function(){
        	 			if (dataStoreRequest.getCount()>0 ){
        	 				Requests.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/request/request_requests/exportToPdf';
        	 				Requests.filterForm.getForm().getEl().dom.method = 'POST';
        	 				Requests.filterForm.getForm().submit();
        	 			}
        	 			else{
        	 				Ext.Msg.alert('Mensaje','No hay datos que exportar!');
        	 			}
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 100,
            store: dataStoreRequest,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    }); 

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Requests.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 130,
        height: 120,
        width: 800,
        items: [{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	{
				            xtype: 'datefield',
				            width: 180,
				            allowBlank: false,
				            fieldLabel: 'Desde',
				            name: 'startdt',
				            id: 'startdt',
				            vtype: 'daterange',
				            invalidText: "El formato correcto de la fecha es aaaa/mm/dd. Ejemplo: 2010-01-01",
				            format: 'Y-m-d',
				            endDateField: 'enddt'
				        },	{
				            xtype: 'datefield',
				            width: 180,
				            allowBlank: false,
				            fieldLabel: 'Hasta',
				            name: 'enddt',
				            id: 'enddt',
				            vtype: 'daterange',
				            invalidText: "El formato correcto de la fecha es aaaa/mm/dd. Ejemplo: 2010-01-01",
				            format: 'Y-m-d',
				            startDateField: 'startdt'
				        }
		      		]
		    },{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	
		      		new Ext.form.ComboBox({
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
       text : 'Limpiar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Requests.filterForm.getForm().reset();
           dataStoreRequest.baseParams = {
               dateStart: '',
               dateEnd: ''
           };
           dataStoreRequest.load();
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Requests.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Requests.filterForm.findById('startdt').getValue();
           	var endDate = Requests.filterForm.findById('enddt').getValue();
           	var motive_id = Requests.filterForm.findById('filter_motive_id').getValue();
           	dataStoreRequest.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d'),
				motive_id: motive_id
           	};
			dataStoreRequest.load();
       	}
   	});    

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Requests.filterForm.render(Ext.get('requests_grid'));
	Requests.requestGrid.render(Ext.get('requests_grid'));
	//dataStoreRequest.load();
	

});
 