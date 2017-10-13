var logsDataStore;
var array;

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
        return true;
    }

});

Ext.onReady(function() {

    Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
    Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Logs');
    

   	var xg = Ext.grid;
    
	var p = new Ext.Panel({
        title: 'Administraci&oacute;n -> Trazas de Sesiones',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro para un log
     */
     
    Logs.logsRecord = new Ext.data.Record.create([
        {name: 'session_id', type: 'int'},
        {name: 'user_name', type: 'string'},
        {name: 'session_ip', type: 'string'},
        {name: 'session_user_agent', type: 'string'},
        {name: 'session_begindate'},
        {name: 'session_enddate'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Logs.logsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'session_id'},
        Logs.logsRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Logs.logsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/sys/sys_sessions/setDataGrid',
        method: 'POST'
    });

    logsDataStore = new Ext.data.Store({
        id: 'logsDS',
        proxy: Logs.logsDataProxy,
        reader: Logs.logsGridReader        
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Logs.logsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       {
            id: 'session_id',
            name : 'session_id',
            dataIndex: 'session_id',
            hidden: true
        }, {
	   		id: 'user_name',
            name: 'user_name',
            header: 'Usuario',
            width: 80,
            dataIndex: 'user_name',
            sortable: true
        }, {
	   		id: 'session_ip',
            name: 'session_ip',
            header: 'IP',
            width: 100,
            dataIndex: 'session_ip',
            sortable: true
        }, {
	   		id: 'session_begindate',
            name: 'session_begindate',
            header: 'Inicio de sesi&oacute;n',
            width: 120,
            dataIndex: 'session_begindate',
            sortable: true
        }, {
	   		id: 'session_enddate',
            name: 'session_enddate',
            header: 'Fin de sesi&oacute;n',
            width: 120,
            dataIndex: 'session_enddate',
            sortable: true
        }, {
	   		id: 'session_user_agent',
            name: 'session_user_agent',
            header: 'Navegador',
            width: 275,
            dataIndex: 'session_user_agent',
            sortable: true
        }]
    );


    /*
     * Creamos el grid de movimientos
     */
    Logs.logsGrid = new xg.GridPanel({
        id : 'ctr-sessions-grid',
        store : logsDataStore,
        cm : Logs.logsColumnMode,
        //view: forceFit:true,
        stripeRows: true,
        frame:true,
        width : 750,
        height : 380,
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: logsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        })
    });
    
    Logs.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        monitorValid: true,
        labelWidth: 100,
        height: 80,
        width: 750,
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

	Logs.filterForm.addButton({
       text : 'Limpiar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Logs.filterForm.getForm().reset();
           logsDataStore.baseParams = {
               dateStart: '',
               dateEnd: ''
           };
           logsDataStore.load({params: {start:0,limit:15}});
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Logs.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Logs.filterForm.findById('startdt').getValue();
           	var endDate = Logs.filterForm.findById('enddt').getValue();
			logsDataStore.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d')
           	};
			logsDataStore.load({params: {start:0,limit:15}});
       	}
   	});    

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Logs.filterForm.render(Ext.get('sessions_grid'));
	Logs.logsGrid.render(Ext.get('sessions_grid'));
    logsDataStore.load({params: {start:0,limit:15}});
});
    
