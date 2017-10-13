//para pasar ademas en los baseparams pues en el boton de exportar a pdf como parametros de la funcions,bueno eso ya no lo hago asi solo dejo estas variables para pasar al stored como baseparameters, luego se podria pasr directamente el valor de los componentes
var begindate;
var person_id;
var enddate;
var center_id;
var person_identity;
var motive_id;
var province_idworkers;//para si elige una provincia y no selecciona un trabajador
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

var dataRecordTransport= new Ext.data.Record.create([
						{name:'transport_id'},
						{name:'transport_name'}
					]);

var dataReaderTransport = new Ext.data.JsonReader({root:'data'},dataRecordTransport);

var dataProxyTransport = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_tickettransports/setDataGrid',
						method: 'POST'
					});

var dataStoreTransport= new Ext.data.Store({
						proxy: dataProxyTransport,
						reader: dataReaderTransport
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


var dataStoreReportLodging;  //las propiedades de este se ponen dentro del Ext.onReady

Ext.onReady(function() {
 function state(val){
        if(val == '---'  || val=='Cancelada' || val=='Denegada' || val=='No Show'){
            return '<span style="color:red;"><b>' + val + '</b></span>';
        }else {
            return '<span style="color:green;"><b>' + val + '</b></span>';
        }
        return val;
    }
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
        title: 'Hospedaje -> Listado Hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
	
    Requests.dataRecordReportLodging= new Ext.data.Record.create([
		{name: 'provinceLodging'},
		{name: 'hotel'},
		{name: 'person'},
		{name: 'identity'},
		{name: 'lodging_entrancedate'},
		{name: 'lodging_exitdate'},
		{name: 'motive'}
	]);


/*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderReportLodging = new Ext.data.JsonReader({
	    root: 'data',
        totalProperty: 'count',
        id: 'request_id'
		},Requests.dataRecordReportLodging
    );


    Requests.dataProxyReportLodging = new Ext.data.HttpProxy({
    	url: baseUrl+'index.php/report/reports/reportLodging/true/no/',     //ojo hay que obligado pasarle true
		method: 'POST'
    });

    dataStoreReportLodging= new Ext.data.Store({
		id: 'requestDS',
		reader: Requests.dataReaderReportLodging,
		proxy: Requests.dataProxyReportLodging
    });

/*
     * Creamos el modelo de columnas para el grid (conciden campos de dataindex con los del metodo requestsRecord para ponerlos en las columnas correspondientes en las filas)
     */
  Requests.ReportLodgingColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
		{
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
			renderer:state,
            width: 125,
            dataIndex: 'hotel',
            sortable: true
        },	{
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
            id: 'motive',
            name : 'motive',
            header: 'Motivo',
            width: 200,
            dataIndex: 'motive',
            sortable: true
        }]
    );
  
  	var combo = new Ext.form.ComboBox({
  		name : 'perpage',
  		id: 'perpage', 
	    width: 40,
	    store: new Ext.data.ArrayStore({
	      fields: ['id'],
	      data  : [
	        ['25'],
	        ['50'],
	        ['100']
	      ]
	    }),
	    mode : 'local',
	    value: '25',
	    listWidth     : 40,
	    triggerAction : 'all',
	    displayField  : 'id',
	    valueField    : 'id',
	    editable      : false,
	    forceSelection: true
	});
  	
  	var bbar = new Ext.PagingToolbar({
  		  store: dataStoreReportLodging, //the store you use in your grid
  		  displayInfo: true,
  		  displayMsg: 'Datos del  {0} - {1} de {2}',
  		  emptyMsg: "No hay datos",
  		  items   :    [
  		       '-',
  		       'Por P&aacute;gina: ',
  		       combo
  		  ]
  	});
  	combo.on('select', function(combo, record) {
  		bbar.pageSize = parseInt(record.get('id'), 10);
  		//bbar.doLoad(bbar.cursor);
  	}, this);

    /*
     * Creamos el grid 
     */
    Requests.requestGrid = new xg.GridPanel({
        id : 'ctr-requests-grid',
        store : dataStoreReportLodging,
        cm : Requests.ReportLodgingColumnMode,
        viewConfig: {
            forceFit:false
        },
        columnLines: true,
        frame:true,
        collapsible: true,
        width : 750,
        height : 460,
        bbar: bbar
    });


/////////////////hasta aqui lo del gridpanel, ahora ponermos lo del formulario
    Requests.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
		standardSubmit:true,
        frame: true,
        monitorValid: true,
        labelWidth: 150,
        height: 180,
        width: 750,
		title:'Par&aacute;metros para b&uacute;squeda de  solicitudes de hospedajes',
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
						}),	new Ext.form.ComboBox({
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
		                }),	new Ext.form.ComboBox({
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
							minLentgh:'11',
							maxLentgh:'11',
				            name: 'person_identity',
				            id: 'filter_person_identity'
				        }, 	new Ext.form.ComboBox({
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
		 	dataStoreReportLodging.baseParams = {
               person_id:0,
               enddate:'1900-01-01',
               begindate:'1900-01-01',
               person_identity:'',
               motive_id:0,
               province_idworkers:0,
               province_idlodging:0,
			   hotel_id:0
		 	};
		 	Requests.filterForm.findById('filter_person_identity').setValue('');
		 	dataStoreReportLodging.load();
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
			else 
				begindate='';
   			if ( Requests.filterForm.findById('filter_enddate').getValue()!='')
   				enddate = Requests.filterForm.findById('filter_enddate').getValue().format('Y/m/d');
   			else 
   				enddate='';
			province_idworkers = Requests.filterForm.findById('filter_province_idworkers').getValue(); 
			person_identity = Requests.filterForm.findById('filter_person_identity').getValue();
			person_id = Requests.filterForm.findById('filter_person_id').getValue();
			motive_id = Requests.filterForm.findById('filter_motive_id').getValue();
			limit = Ext.getCmp('perpage').getValue();
			hotel_id = Requests.filterForm.findById('filter_hotel_id').getValue();
			province_idlodging = Requests.filterForm.findById('filter_province_idlodging').getValue();
			dataStoreReportLodging.baseParams = { 
					province_idlodging:province_idlodging,
					hotel_id:hotel_id,
					motive_id: motive_id,
					province_idworkers:province_idworkers,
					person_id: person_id,
					person_identity: person_identity,
					enddate:enddate,          
					begindate: begindate,
					limit: limit
           	};
           	dataStoreReportLodging.load();
			Requests.requestGrid.getStore().reload();
       	}
   	});
	
	Requests.filterForm.render(Ext.get('requests_grid'));//primero el formulario de filtrado
	Requests.requestGrid.render(Ext.get('requests_grid'));
    
	
});
///////fin del onReady






  