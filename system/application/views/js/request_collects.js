var dataStoreRequest;
var sm2;

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

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Requests.requestGrid.removeButton.enable();
                    Requests.requestGrid.addButton.enable();
                } else {
                    Requests.requestGrid.removeButton.disable();
                    Requests.requestGrid.addButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Solicitud -> Solicitud Recogida CFN',
        collapsible:false,
        renderTo: 'panel-basic',
        width:800,
        bodyCfg: {
	    }
    });
    
    
	function color(val){
        if(val == 'SI'){
            return '<span style="color:green;">' + val + '</span>';
        }else {
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }

    Requests.dataRecordRequest= new Ext.data.Record.create([                                                        
        {name: 'request_id'},
        {name: 'person'},
		{name: 'province_from'},
		{name: 'province_to'},
		{name: 'transport'},
		{name: 'state'},
		{name: 'exithour'},
		{name: 'arrivalhour'},
		{name: 'ticketdate'}
	]);
/*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderRequest = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count'},
        //id: 'request_id'},
        Requests.dataRecordRequest
    );


    Requests.dataProxyRequest = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/request/request_tickets/getDataCollect',
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
       	sm2,
       {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },	{
	   		id: 'state',
            name: 'state',
			header: "Solicitado",
			width: 65,
			renderer: color,
			dataIndex: 'state'
		},	{
	   		id: 'ticketdate',
            name: 'ticketdate',
            header: 'Fecha',
			format: 'dd-mm-YYYY',
            width: 70,
            dataIndex: 'ticketdate',
            sortable: true
        },	{
	   		id: 'transport',
            name: 'transport',
			header: "Transporte",
			width: 110,
			dataIndex: 'transport',
			sortable: true
		},	{
	   		id: 'exithour',
            name: 'exithour',
			header: "Salida",
			width: 60,
			dataIndex: 'exithour',
			sortable: true
		},	{
	   		id: 'arrivalhour',
            name: 'arrivalhour',
			header: "Llegada",
			width: 60,
			dataIndex: 'arrivalhour',
			sortable: true
		},	{
            id: 'person',
            name : 'person',
            header: "Trabajador",
            width: 180,
            dataIndex: 'person',
            sortable: true
        },	{
	   		id: 'province_from',
            name: 'province_from',
			header: "Origen",
			width: 120,
			dataIndex: 'province_from',
			sortable: true
		},	{
            id: 'province_to',
            name : 'province_to',
            header: "Destino",
            width: 120,
            dataIndex: 'province_to',
            sortable: true
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
        //plugins: expander,
        collapsible: true,
        width : 800,
        height : 500,
        tbar:[{
            text:'Recoger',
            tooltip:'Hacer Solicitud de Recogida',
            iconCls:'add',
            ref: '../addButton',
            disabled: true,
            handler: function(){
			    	    array = sm2.getSelections();
						for (var i = 0, len = array.length; i < len; i++) {
							var exitdate = array[i].get('ticketdate');
					   		if (today > exitdate){
								Ext.MessageBox.alert('Error', 'No se puede realizar esta solicitud porque la fecha del pasaje ya expir&oacute;.');
								return false;
				       		}
					        Ext.Ajax.request({
							   url: baseUrl+'index.php/request/request_tickets/insertCollect/'+array[i].get('request_id')+'/'+array[i].get('ticketdate'),
							   method: 'GET',
							   disableCaching: false,
							   success: function(){
					        					
							   },
							   failure: function(){
							   		Ext.MessageBox.alert('Error', 'No se pudo completar la solicitud.');
							   }
							});
					    }
					    sm2.clearSelections();
			           	var startDate = Ext.getCmp('startdt').getValue();
			           	var endDate = Ext.getCmp('enddt').getValue();
			           	dataStoreRequest.baseParams = {
							dateStart: startDate.dateFormat('Y-m-d'),
							dateEnd: endDate.dateFormat('Y-m-d')
			           	};
			           	dataStoreRequest.load();
			        }
        },'-',{
            text:'Cancelar',
            tooltip:'Cancelar la(s) Solicitud(es) Seleccionada(s)',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
			    	    array = sm2.getSelections();
						for (var i = 0, len = array.length; i < len; i++) {
							var exitdate = array[i].get('ticketdate');
					   		if (today > exitdate){
								Ext.MessageBox.alert('Error', 'No se puede realizar esta solicitud porque la fecha del pasaje ya expir&oacute;.');
								return false;
				       		}
					        Ext.Ajax.request({
							   url: baseUrl+'index.php/request/request_tickets/cancelCollect/'+array[i].get('request_id')+'/'+array[i].get('ticketdate'),
							   method: 'GET',
							   disableCaching: false,
							   success: function(){
					        					
							   },
							   failure: function(){
							   		Ext.MessageBox.alert('Error', 'No se pudo completar la solicitud.');
							   }
							});
					    }
					    sm2.clearSelections();
			           	var startDate = Ext.getCmp('startdt').getValue();
			           	var endDate = Ext.getCmp('enddt').getValue();
			           	dataStoreRequest.baseParams = {
							dateStart: startDate.dateFormat('Y-m-d'),
							dateEnd: endDate.dateFormat('Y-m-d')
			           	};
			           	dataStoreRequest.load();
            }
        }],
        bbar: new Ext.PagingToolbar({
            store: dataStoreRequest,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    }); 

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Requests.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        monitorValid: true,
        labelWidth: 100,
        height: 80,
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
				        }
		      		]
		    },{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	{
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
               dateStart: '1900-01-01',
               dateEnd: '1900-01-01'
           };
           dataStoreRequest.load();
       }
   });

   /*
    * A�adimos el boton para filtrar
    */
   	Requests.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Requests.filterForm.findById('startdt').getValue();
           	var endDate = Requests.filterForm.findById('enddt').getValue();
           	dataStoreRequest.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d')
           	};
			dataStoreRequest.load();
       	}
   	});    

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Requests.filterForm.render(Ext.get('requests_grid'));
	Requests.requestGrid.render(Ext.get('requests_grid'));
	
});	