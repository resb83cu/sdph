//para pasar ademas en los baseparams pues en el boton de exportar a pdf como parametros de la funcions,bueno eso ya no lo hago asi solo dejo estas variables para pasar al stored como baseparameters, luego se podria pasr directamente el valor de los componentes
var begindate;
var person_id;
var enddate;
var center_id;
var person_identity;
var motive_id;
var province_idworkers;//para si elige una provincia y no selecciona un trabajador
var province_idfrom;
var province_idto;
var transport_id;
var transport_itinerary;

var estado;//aun porgusto, sera para filtrar por estado

var dataRecordTransport= new Ext.data.Record.create([
						{name:'transport_id'},
						{name:'transport_name'}
					]);

var dataReaderTransport = new Ext.data.JsonReader({root:'data'},dataRecordTransport);

var dataProxyTransport = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_lodgingtransports/setDataGrid',
						method: 'POST'
					});

var dataStoreTransport= new Ext.data.Store({
						proxy: dataProxyTransport,
						reader: dataReaderTransport,
						autoLoad: true
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

//
var dataRecordPersons= new Ext.data.Record.create([
						{name:'person_id'},
						{name:'person_fullname'}
						//Super ojo este person_fullname es un  campo del arreglo devuelto en el setdatagrid del controlador que a su vez llama al getdata del model
					]);

var dataReaderPersons= new Ext.data.JsonReader({root:'data'},dataRecordPersons);

var dataProxyPersons = new Ext.data.HttpProxy({
					   /*notar importante aqui que a este data se le pasa un baseparams en otro lugar y en el metodo del controlador setDatagrid se coge con $this->input->post(  ' con el  nombre que se le ponga en el listener  y el baseparams, se pasan los que sean , todo va por post'        )*/					   
						url:baseUrl+'index.php/person/person_persons/setDataByProvinceId/',   //ver que este metodo devuelve el name con la concatenacion name+lastname+secondlastname y ademas se le esta pasando por el post parametros por algun baseparams el idprov, para que no cargue a todo el mundo a la vez, super verdad
						method: 'POST'
					});

var dataStorePersons= new Ext.data.Store({
						proxy: dataProxyPersons,
						reader: dataReaderPersons,
						autoLoad:true
						});
//
var dataRecordCenter= new Ext.data.Record.create([
						{name:'center_id'},
						{name:'center_name'}
					]);

var dataReaderCenter = new Ext.data.JsonReader({root:'data'},dataRecordCenter);

var dataProxyCenter = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_costcenters/setDataGrid',
						method: 'POST'
					});

var dataStoreCenter= new Ext.data.Store({
						proxy: dataProxyCenter,
						reader: dataReaderCenter,
						autoLoad:true
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

//



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
        title: 'Reportes -> Reporte Pasaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });

    Requests.dataRecordReportInternalTicket = new Ext.data.Record.create([
       // {name: 'request_id'},
		{name: 'person'},
		{name: 'identity'},
		{name: 'ticket_date'},
		{name: 'provinceFrom'},
		{name: 'provinceTo'},
		{name: 'estado'},
		{name: 'center'},
		{name: 'motive'},
		{name: 'details'}
		]);


/*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderReportInternalTicket = new Ext.data.JsonReader({
	    root: 'data',
        totalProperty: 'count',
        id: 'request_id'
	
		},     Requests.dataRecordReportInternalTicket
    );




/*ojo ver que el que se llama aqui es el setDataConditional para el reporte, que este a su vez llama al getData Conditional, pudo haberse hecho junto pero para mejor entendimiento lo hice separado*/
/*en el getdataconditional se cogen los parametros con la funcion de asistente uri que a su vez se cargo en el autoload en los helper*/ 

	Requests.dataProxyReportInternalTicket = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/report/reports/reportInternalTicket/true/no/',     //ojo hay que obligado pasarle true
		 method: 'POST'
	});


   
   dataStoreReportInternalTicket= new Ext.data.Store({
						id: 'requestDS',
				           reader :Requests.dataReaderReportInternalTicket,
						   proxy	:Requests.dataProxyReportInternalTicket
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
				          Requests.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/report/reports/reportInternalTicket/true/si/' ;
	                      Requests.filterForm.getForm().getEl().dom.method = 'POST';
                          Requests.filterForm.getForm().submit();
                         //el codigo anterior es si el standarSubmit del fomulario esta en true, sino seria la linea de abajo, pero en este caso hace falyta que no devuelva nada sino vaya al controlador y ejecute la funcion y ya(ext opor defecto todo es ajax...)                      
					      //Requests.filterForm.getForm().submit({url : baseUrl+'index.php/report/reports/reportInternalTicket/false/si/' });
	            	  //exportarPDF();
					} else{
						  Ext.Msg.alert('Mensaje','No hay datos que exportar!');	  
					}
            }
        }],
       bbar: new Ext.PagingToolbar({
            pageSize: 15,
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
        height: 230,
        width: 750,
		title:'Par&aacute;metros del reporte general de pasajes',
        items:[{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	new Ext.form.ComboBox({
		           			store: dataStoreTransport,
		           			fieldLabel: 'Transporte',
		           			displayField: 'transport_name',
		           			valueField: 'transport_id',
		           			hiddenName: 'transport_id',//como se ve igual no tiene que coincidir los nombres, el value field es el name definido en el datarecord arriba y el hidden  el nombre que se ercibe en el input del model a la hora de coger el valor
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione un transporte...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_transport_id',
				            name : 'transport_id',
				            listeners: {
								'select' : function() {
									var idv =  Requests.filterForm.findById('filter_transport_id').getValue() - 1;
							       if (idv==0){
								      Requests.filterForm.findById('filter_transport_itinerary').enable();
									 }else{ Requests.filterForm.findById('filter_transport_itinerary').disable();}
							  	},
							  	'blur': function(){
									var flag = dataStoreTransport.findExact( 'transport_id', Ext.getCmp('filter_transport_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_transport_id').reset();
						    			return false;
						    		}
								}
						 	}
		                }),	new Ext.form.ComboBox({
		           			store:  ['Santiago-Habana','Habana-Santiago'],
		           			fieldLabel: 'Itinerario',
		           			displayField: 'transport_itinerary',
		           			valueField: 'transport_itinerary',
		           			disabled: true,
		           			allowBlank: true,
		           			readOnly: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione un itinerario...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_transport_itinerary',
							hiddenName: 'transport_itinerary',
				            name : 'transport_itinerary'
		                }),	new Ext.form.ComboBox({
		           			store: dataStoreProv,
		           			fieldLabel: 'Provincia origen',
		           			displayField: 'province_name',
		           			valueField: 'province_id',
		           			hiddenName: 'province_idfrom',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione una Provincia...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_province_idfrom',
				            name : 'province_idfrom',
				            listeners: {
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_idfrom').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_province_idfrom').reset();
						    			return false;
						    		}
								}
					 		}
						}), new Ext.form.ComboBox({
		           			store: dataStoreProv,
		           			fieldLabel: 'Provincia destino',
		           			displayField: 'province_name',
		           			valueField: 'province_id',
		           			hiddenName: 'province_idto',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione una Provincia...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_province_idto',
				            name : 'province_idto',
				            listeners: {
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_idto').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_province_idto').reset();
						    			return false;
						    		}
								}
					 		}
						}),	{
				            xtype: 'datefield',
				            width: 200,
				            allowBlank: true,
				            fieldLabel: 'Desde',
				            name: 'begindate',
				            id: 'filter_begindate',
				            vtype: 'daterange',
				            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
				            format: 'Y-m-d',
				            endDateField: 'filter_enddate'
				        },	{
				            xtype: 'datefield',
				            width: 200,
				            allowBlank: true,
				            fieldLabel: 'Hasta',
				            name: 'enddate',
				            id: 'filter_enddate',
				            vtype: 'daterange',
				            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
				            format: 'Y-m-d',
				            startDateField: 'filter_begindate'
				        }	 
		             ]
		    },{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	new Ext.form.ComboBox({
		           			store: dataStoreProv,
		           			fieldLabel: 'Provincia del trabajador',
		           			displayField: 'province_name',
		           			valueField: 'province_id',
		           			hiddenName: 'province_idworkers',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione una Provincia...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_province_idworkers',//no pongo filter para poner otro combo donde se filtre por provincia destino de viaje y asi diferenciarlo , ademas este id no me hace falta, ya que este combo solo es para filtrar el combo de persona
				            name : 'province_idworkers',
				            listeners: {
								'select': function(){
											dataStorePersons.baseParams = {province_id: Ext.getCmp('filter_province_idworkers').getValue()};
											dataStorePersons.load();
								},
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_idworkers').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_province_idworkers').reset();
						    			return false;
						    		}
								}
					 		}
		                }), new Ext.form.ComboBox({
		          			store: dataStorePersons,
		          			fieldLabel: 'Trabajador',
		          			displayField: 'person_fullname',
		          			valueField: 'person_id',
		          			hiddenName: 'person_id', //este campo (da igual el nombre por ahora)no se pasa por el url sino que sirve para en dependencia de la prov marcada pues carga solamente las personas de la misma, ahorra recursos hacere sto en evz de cargar a todos las personas del pais
		          			allowBlank: true,
		          			typeAhead: true,
		          			mode: 'local',
		          			triggerAction: 'all',
		          			emptyText: 'Seleccione un trabajador...',
		          			selectOnFocus: true,
		          			width: 200,
						    id: 'filter_person_id',
				            name : 'person_id',
				            listeners: {
								'blur': function(){
									var flag = dataStorePersons.findExact( 'person_id', Ext.getCmp('filter_person_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_person_id').reset();
						    			return false;
						    		}
								}
					 		}
		                }), {
				            xtype: 'textfield',
				            width: 200,
				            allowBlank: true,
				            fieldLabel: 'Carnet',
							minLentgh:11,
							maxLentgh:11,
				            name: 'person_identity',
				            id: 'filter_person_identity'
				        },	new Ext.form.ComboBox({
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
							readOnly:'true',
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
				        }), new Ext.form.ComboBox({
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
           dataStoreReportInternalTicket.baseParams = {
			       center_id: 0,
				   motive_id:0,
				   province_idworkers:0,
				   person_id:0,
				   person_identity:'',
				   transport_id:0,
				   transport_itinerary:'',
		           enddate:'1900-01-01',   
		           begindate: '1900-01-01',
				   province_idfrom:0,
				   province_idto:0
           };
		   Requests.filterForm.findById('filter_person_identity').setValue('');
           dataStoreReportInternalTicket.load({params: {start:0,limit:15}});
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
			
			if ( Requests.filterForm.findById('filter_begindate').getValue()!='')
           	 begindate = Requests.filterForm.findById('filter_begindate').getValue().format('Y/m/d');
			     else begindate='';
           	 if ( Requests.filterForm.findById('filter_enddate').getValue()!='')
           	 enddate = Requests.filterForm.findById('filter_enddate').getValue().format('Y/m/d');
			     else enddate='';
			 center_id= Requests.filterForm.findById('filter_center_id').getValue();
			 
			province_idworkers = Requests.filterForm.findById('filter_province_idworkers').getValue(); 
			person_identity = Requests.filterForm.findById('filter_person_identity').getValue();
			person_id = Requests.filterForm.findById('filter_person_id').getValue();
			motive_id = Requests.filterForm.findById('filter_motive_id').getValue();
			province_idfrom = Requests.filterForm.findById('filter_province_idfrom').getValue();
			province_idto = Requests.filterForm.findById('filter_province_idto').getValue();
			
			transport_id= Requests.filterForm.findById('filter_transport_id').getValue();
			transport_itinerary= Requests.filterForm.findById('filter_transport_itinerary').getValue();
			dataStoreReportInternalTicket.baseParams = {//las sig variables estan declaradas arriba arriba para futuro uso de pasar parametros apara  pdf
				  
				center_id: center_id,
				motive_id: motive_id,
				province_idto:province_idto,
				province_idfrom:province_idfrom,
				province_idworkers:province_idworkers,
				transport_id:transport_id,
				transport_itinerary:transport_itinerary,
				person_id: person_id,
				person_identity: person_identity,
				enddate:enddate,          
				begindate: begindate
           	};
           	dataStoreReportInternalTicket.load({params: {start:0,limit:15}});
			Requests.requestGrid.getStore().reload();
			
       	}
		
   	});    
	
	Requests.filterForm.render(Ext.get('requests_grid'));
	Requests.requestGrid.render(Ext.get('requests_grid'));
	
});







  