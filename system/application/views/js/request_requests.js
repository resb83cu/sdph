var updateForm;					

				
var dataRecordProv = new Ext.data.Record.create([
{
    name:'province_id'
},
{
    name:'province_name'
}
]);

var dataReaderProv = new Ext.data.JsonReader({
    root:'data'
},dataRecordProv);

var dataProxyProv = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_provinces/setDataGrid',
    method: 'POST'
});

var dataStoreProv = new Ext.data.Store({
    proxy: dataProxyProv,
    reader: dataReaderProv,
    autoLoad:true
});

var dataRecordCenter= new Ext.data.Record.create([
{
    name:'center_id'
},
{
    name:'center_name'
}
]);

var dataReaderCenter = new Ext.data.JsonReader({
    root:'data'
},dataRecordCenter);

var dataProxyCenter = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_costcenters/setDataGrid',
    method: 'POST'
});

var dataStoreCenter= new Ext.data.Store({
    proxy: dataProxyCenter,
    reader: dataReaderCenter,
    autoLoad:true
});

var dataRecordCenterSap = new Ext.data.Record.create([
    {
        name: 'center_id'
    },
    {
        name: 'center_name'
    }
]);

var dataReaderCenterSap = new Ext.data.JsonReader({
    root: 'data'
}, dataRecordCenterSap);

var dataProxyCenterSap = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_costcenters/getDataSap',
    method: 'POST'
});

var dataStoreCenterSap = new Ext.data.Store({
    proxy: dataProxyCenterSap,
    reader: dataReaderCenter,
    autoLoad: true
});

var dataRecordMotive= new Ext.data.Record.create([
{
    name:'motive_id'
},
{
    name:'motive_name'
}
]);

var dataReaderMotive = new Ext.data.JsonReader({
    root:'data'
},dataRecordMotive);

var dataProxyMotive = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_motives/setDataGrid',
    method: 'POST'
});

var dataStoreMotive= new Ext.data.Store({
    proxy: dataProxyMotive,
    reader: dataReaderMotive,
    autoLoad:true
});

var dataRecordLodgingTransport= new Ext.data.Record.create([
{
    name:'transport_id'
},
{
    name:'transport_name'
}
]);

var dataReaderLodgingTransport = new Ext.data.JsonReader({
    root:'data'
},dataRecordLodgingTransport);

var dataProxyLodgingTransport = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/conf/conf_lodgingtransports/setDataGrid',
    method: 'POST'
});

var dataStoreLodgingTransport = new Ext.data.Store({
    proxy: dataProxyLodgingTransport,
    reader: dataReaderLodgingTransport,
    autoLoad:true
});

var dataRecordPersons= new Ext.data.Record.create([
{
    name:'person_id'
},
{
    name:'person_fullname'
}
]);

var dataReaderPersons= new Ext.data.JsonReader({
    root:'data'
},dataRecordPersons);

var dataProxyPersons = new Ext.data.HttpProxy({
    url:baseUrl+'index.php/person/person_persons/setDataByProvinceId/',
    method: 'POST'
});

var dataStorePersons= new Ext.data.Store({
    proxy: dataProxyPersons,
    reader: dataReaderPersons
});

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
                } else {
                    Requests.requestGrid.removeButton.disable();
                }
            }
        }
    });
    
    var p = new Ext.Panel({
        title: 'Solicitud -> Solicitud General',
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
	
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><b>Detalle:</b> {request_details}</p>'
            )
    });

    Requests.dataRecordRequest= new Ext.data.Record.create([
    {
        name: 'request_id'
    },

    {
        name: 'request_date'
    },

    {
        name: 'request_details'
    },
        {
            name: 'request_consecutive'
        },

    {
        name: 'person_requestedby'
    },

    {
        name: 'person_licensedby'
    },

    {
        name: 'center_name'
    },

    {
        name: 'person_worker'
    },

    {
        name: 'motive_name'
    },

    {
        name: 'lodging'
    },

    {
        name: 'lodging_entrancedate'
    },

    {
        name: 'lodging_state'
    },

    {
        name: 'ticket'
    },

    {
        name: 'ticket_date'
    },

    {
        name: 'ticket_state'
    }
    ]);
    /*
     * Creamos el reader para el Grid de 
     */
    Requests.dataReaderRequest = new Ext.data.JsonReader({
        root: 'data',
        totalProperty: 'count',
        id: 'request_id'
    },
    Requests.dataRecordRequest
    );


    Requests.dataProxyRequest = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/request/request_requests/setDataGrid',
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
        expander,
        {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        }, {
            id: 'request_consecutive',
            name: 'request_consecutive',
            header: 'Consecutivo',
            width: 60,
            dataIndex: 'request_consecutive',
            sortable: false
         },{
            id: 'request_date',
            name: 'request_date',
            header: 'Fecha de Solicitud',
            format: 'dd-mm-YYYY',
            width: 95,
            dataIndex: 'request_date',
            sortable: true
        }/*,{
	   		id: 'request_details',
            name: 'request_details',
			header: "Detalles",
			width: 90,
			dataIndex: 'request_details',
			sortable: true
		}*/,{
            id: 'person_requestedby',
            name : 'person_requestedby',
            header: "Solicitado por",
            width: 130,
            dataIndex: 'person_requestedby',
            sortable: true
        },{
            id: 'person_licensedby',
            name : 'person_licensedby',
            header: "Autorizado por",
            width: 130,
            dataIndex: 'person_licensedby',
            sortable: true
        },{
            id: 'center_name',
            name: 'center_name',
            header: "Centro de Costo",
            width: 90,
            dataIndex: 'center_name',
            sortable: true
        },{
            id: 'person_worker',
            name: 'person_worker',
            header: "Trabajador",
            width: 130,
            dataIndex: 'person_worker',
            sortable: true
        },{
            id: 'motive_name',
            name: 'motive_name',
            header: "Motivo",
            width: 100,
            dataIndex: 'motive_name',
            sortable: true
        },{
            id: 'lodging',
            name: 'lodging',
            header: "Hospedaje",
            width: 70,
            renderer: color,
            dataIndex: 'lodging'
        },{
            id: 'ticket',
            name: 'ticket',
            header: "Pasaje",
            width: 50,
            renderer: color,
            dataIndex: 'ticket'
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
        plugins: expander,
        collapsible: true,
        width : 800,
        height : 500,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar Solicitud de Hospedaje',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Cancelar',
            tooltip:'Cancelar la(s) Solicitud(es) Seleccionada(s)',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
                array = sm2.getSelections();
                if (array.length > 0) {
                    Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea cancelar esta(s) Solicitud(es)?', delRecords);
                }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 25,
            store: dataStoreRequest,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    }); 

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
	
    Requests.requestGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = dataStoreRequest.getAt(row).data.request_id;
        var lodging_state = dataStoreRequest.getById(selectedId).data.lodging_state;
        var ticket_state = dataStoreRequest.getById(selectedId).data.ticket_state;
        var lodging_entrancedate = dataStoreRequest.getById(selectedId).data.lodging_entrancedate;
        var ticket_date = dataStoreRequest.getById(selectedId).data.ticket_date;
        if ((parseInt(ticket_state) > 0) || (parseInt(lodging_state) > 1)) {
            Ext.Msg.alert('Error', 'No se puede modificar esta solicitud porque ya ha sido editada.');
            return false;
        }
        if ((today > lodging_entrancedate) || (today > ticket_date)) {
            Ext.Msg.alert('Valor Inv&aacute;lido', 'No se puede modificar esta solicitud porque ya ha expirado la fecha de modificaci&oacute;n.');
            return false;
        }
        /*if ((today > lodging_entrancedate) || (today > ticket_date)) {
        	Ext.Msg.alert('Valor Inv&aacute;lido', 'No se puede modificar esta solicitud porque ya ha expirado la fecha de modificaci&oacute;n.');
        	return false;
		}*/
        
        update_ventana(selectedId);
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
            },	{
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
                dateStart: '',
                dateEnd: ''
            };
            dataStoreRequest.load({
                params: {
                    start:0,
                    limit:25
                }
            });
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
        dataStoreRequest.baseParams = {
            dateStart: startDate.dateFormat('Y-m-d'),
            dateEnd: endDate.dateFormat('Y-m-d')
        };
        dataStoreRequest.load({
            params: {
                start:0,
                limit:25
            }
        });
}
});    

/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
Requests.filterForm.render(Ext.get('requests_grid'));
    Requests.requestGrid.render(Ext.get('requests_grid'));
    dataStoreRequest.load({
        params: {
            start:0,
            limit:25
        }
    });
	
function person_ventana(){
		    
    var personWindow;
	    
    /*
	     * Creamos el formulario de alta/modificacion de motivos
	     */
    var ciExpr1 = /[0-9]/;
    var Persons = new Ext.FormPanel({
        id: 'form-persons',
        region: 'west',
        split: false,
        collapsible: true,
        frame: true,
        labelWidth: 120,
        width: 370,
        minWidth: 370,
        height: 380,
        waitMsgTarget: true,
        monitorValid: true,
        items: [
        {
            fieldLabel : 'CI',
            id: 'frm_person_identity',
            name : 'person_identity',
            allowBlank:false,
            maxLength: 11,
            minLength: 11,
            width: 180,
            regex:ciExpr1,
            invalidText: "Carnet de identidad no valido",
            validator: function(value){
                var flag = true;
								
                var temp=value;
                if (value.length == 11){//super de p!!! si no hago esto antes que ponga 11 siemrpe me invalidara porque buscara en la posicion 0, 1, 2,3 ...y no valida
                    temp=value.toString();
                    if (temp.substring(2,4) >12 || ( 
                        temp.substring(3,4)==0)&& temp.substring(2,3)==0 )
                        flag=false;	//hasta aqui validamos el mes
                    if (temp.substring(4,6) >31 ||  (temp.substring(4,6)==00))
                        flag=false;	//hasta aqui validamos el  dia
                    //ahora validar los dias de cada mes   
                    if (temp.substring(2,4) == 02 &&  temp.substring(4,6) >29 )
                        flag=false;	//hasta aqui validamos los 29 dias como maximo de febrero  
                    if (temp.substring(2,4) == 04 &&  temp.substring(4,6) >30 )
                        flag=false;	//hasta aqui validamos los 30 dias como maximo de abril  
                    if (temp.substring(2,4) == 06 &&  temp.substring(4,6) >30 )
                        flag=false;	//hasta aqui validamos los 30 dias como maximo de junio
                    if (temp.substring(2,4) == 09 &&  temp.substring(4,6) >30 )
                        flag=false;	//hasta aqui validamos los 30 dias como maximo de septiembre
                    if (temp.substring(2,4) == 11 &&  temp.substring(4,6) >30 )
                        flag=false;	//hasta aqui validamos los 30 dias como maximo de noviembre
								
                }
                return flag;
					
            },
            xtype: 'textfield'
        },	{
            fieldLabel : 'Nombre',
            id: 'frm_person_name',
            name : 'person_name',
            allowBlank:false,
            width: 180,
            xtype: 'textfield'
        },	{
            fieldLabel : '1er Apellido',
            id: 'frm_person_lastname',
            name : 'person_lastname',
            allowBlank:false,
            width: 180,
            xtype: 'textfield'
        },	{
            fieldLabel : '2do Apellido',
            id: 'frm_person_secondlastname',
            name : 'person_secondlastname',
            allowBlank:false,
            width: 180,
            xtype: 'textfield'
        }, 	new Ext.form.ComboBox({
            store: dataStoreProv,
            fieldLabel: 'Provincia',
            displayField: 'province_name',
            valueField: 'province_id',
            hiddenName: 'province_id',
            allowBlank: false,
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText: 'Seleccione una Provincia...',
            selectOnFocus: true,
            width: 200,
            id: 'frm_province_id',
            name : 'province_id',
            listeners: {
                'blur': function(){
                    var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_id').getValue());
                    if (flag == -1){
                        Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
                        Ext.getCmp('frm_province_id').reset();
                        return false;
                    }
                }
            }
        }),	{
            fieldLabel : 'Direcci&oacute;n particular',
            id: 'frm_person_address',
            name : 'person_address',
            allowBlank: true,
            width: 200,
            xtype: 'textarea'
        },	{
            fieldLabel : 'Tel&eacute;fono',
            id: 'frm_person_phone',
            name : 'person_phone',
            allowBlank:true,
            width: 180,
            xtype: 'textfield'
        },	{
            id: 'frm_person_id',
            name : 'person_id',
            xtype: 'hidden'
        },	{
            xtype: 'checkbox',
            id: 'frm_person_isworker',
            name: 'person_isworker',
            fieldLabel: 'Es Trabajador',
            checked: 'person_isworker',
            listeners: {
                'check' : function(chk) {
                    if (chk.checked){
                        Persons.findById('frm_worker_email').enable();
                        Persons.findById('frm_worker_phone').enable();
                    }else{
                        Persons.findById('frm_worker_email').disable();
                        Persons.findById('frm_worker_phone').disable();
                    }
                }
            }
        }, 	{
            fieldLabel : 'Correo',
            id: 'frm_worker_email',
            name : 'worker_email',
            allowBlank:true,
            xtype: 'textfield',
            width: 180,
            vtype: 'email'
        },	{
            fieldLabel : 'Tel&eacute;fono',
            id: 'frm_worker_phone',
            name : 'worker_phone',
            allowBlank:true,
            xtype: 'textfield',
            width: 180
        } 	/*new Ext.form.ComboBox({
							store: dataStoreCenter,
							fieldLabel: 'Centro de Costo',
							displayField: 'center_name',
							valueField: 'center_id',
							hiddenName: 'center_id',
							allowBlank: false,
							typeAhead: true,
							mode: 'local',
							triggerAction: 'all',
							//disabled:true, 					
							emptyText: 'Seleccione un Centro de costo...',
							selectOnFocus: true,
							width: '100%',
					    id: 'frm_center_id',
					    name : 'center_id'    
					}), new Ext.form.ComboBox({
							store: dataStorePos,
							fieldLabel: 'Plaza',
							displayField: 'position_name',
							valueField: 'position_id',
							hiddenName: 'position_id',
							allowBlank: true,
							typeAhead: true,
							mode: 'local',
							triggerAction: 'all',
							emptyText: 'Seleccione una Plaza...',
							selectOnFocus: true,
							width: 200,
					    id: 'frm_position_id',
					    name : 'position_id',
					    listeners: {
							'blur': function(){
								var flag = dataStorePos.findExact( 'position_id', Ext.getCmp('frm_position_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_position_id').reset();
					    			return false;
					    		}
							}
							}
					})*/]
			
    });
    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
    Persons.addButton({
        text : 'Cancelar',
        disabled : false,
        handler : function() {
            Persons.getForm().reset();
            personWindow.destroy();
        }
    });
	
    /*
	     * Anadimos el boton para guardar los datos del formulario
	     */
    Persons.addButton({
        text : 'Guardar',
        disabled : false,
        formBind: true,
        handler : function() {
            Persons.getForm().submit({
                url : baseUrl+'index.php/person/person_persons/insert',
                waitMsg : 'Salvando datos...',
                failure: function (form, action) {
                    obj = Ext.util.JSON.decode(action.response.responseText); 
                    Ext.Msg.alert('Fall&oacute; la operaci&oacute;n!', obj.errors.reason);	                          
                    dataStorePersons.load();
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
                    Persons.getForm().reset();
                    dataStorePersons.baseParams = {
                        province_id: Ext.getCmp('frm_province_idworker').getValue()
                        };
                    dataStorePersons.load(); 
                }
            });
        }
    });
		
    if(!personWindow){
	
        personWindow = new Ext.Window({
            title: 'Agregar nueva persona',
            layout:'form',
            top: 200,
            width: 400,
            height:423,
            resizable : false,
            modal: true,
            bodyStyle:'padding:5px;',
            items: Persons
        });
    }
    personWindow.show(this);
	
}
	
function fecha( cadena ) {
    var separador = "-";
	
    //Separa por dia, mes y anno
    if ( cadena.indexOf( separador ) != -1 ) {
        var posi1 = 0;
        var posi2 = cadena.indexOf( separador, posi1 + 1 );
        var posi3 = cadena.indexOf( separador, posi2 + 1 );
        this.dia = cadena.substring( posi1, posi2 );
        this.mes = cadena.substring( posi2 + 1, posi3 );
        this.anio = cadena.substring( posi3 + 1, cadena.length );
    } else {
        this.dia = 0;
        this.mes = 0;
        this.anio = 0;  
    }
}
	
function diferencia(beginDate, endDate){

    //Obtiene dia, mes y a�o
    var fecha1 = new fecha( beginDate );
    var fecha2 = new fecha( endDate );
	   
    //Obtiene objetos Date
    var miFecha1 = new Date( fecha1.anio, fecha1.mes, fecha1.dia );
    var miFecha2 = new Date( fecha2.anio, fecha2.mes, fecha2.dia );
	
    //Resta fechas y redondea
    //   Math.floor((fecha1.getTime()-fecha2.getTime())/(3600000*24))
    var diferencia = miFecha2.getTime() - miFecha1.getTime();
    var dias = Math.floor(diferencia / (3600000*24));
//alert ('La diferencia es de ' + dias + ' dias,\no')
	   
//return dias;
}
	

	
function update_ventana(id){
	
    lodgingRecordUpdate = new Ext.data.Record.create([
    {
        name: 'request_id', 
        type: 'int'
    },

    {
        name: 'request_inversiontask', 
        type: 'string'
    },

    {
        name: 'motive_id', 
        type: 'int'
    },

    {
        name: 'request_details', 
        type: 'string'
    },

    {
        name: 'province_idworker', 
        type: 'int'
    },

    {
        name: 'person_idworker', 
        type: 'int'
    },
	        

    //request_lodgings

    {
        name: 'lodging_entrancedate'
    },

    {
        name: 'lodging_exitdate'
    },

    {
        name: 'lodging_requestreinforceddiet', 
        type: 'string'
    },

    {
        name: 'lodging_requestelongationdiet', 
        type: 'string'
    },

    {
        name: 'province_idlodging', 
        type: 'int'
    },

    {
        name: 'transport_idlodging', 
        type: 'int'
    },

    {
        name: 'transport_idreturnlodging', 
        type: 'int'
    },

    {
        name: 'lodging_state', 
        type: 'int'
    },

    {
        name: 'check_lodging', 
        type: 'string'
    },
			

    //request_ticket

    {
        name: 'ticket_idexit', 
        type: 'int'
    },

    {
        name: 'transport_idexit', 
        type: 'int'
    },

    {
        name: 'transport_itinerary', 
        type: 'string'
    },

    {
        name: 'ticket_exitdate'
    },

    {
        name: 'province_idfrom', 
        type: 'int'
    },

    {
        name: 'province_idto', 
        type: 'int'
    },

    {
        name: 'ticket_state', 
        type: 'int'
    },
			

    //request_ticketreturn

    {
        name: 'ticket_idreturn', 
        type: 'int'
    },

    {
        name: 'transport_idreturn', 
        type: 'int'
    },

    {
        name: 'transport_return_itinerary', 
        type: 'string'
    },

    {
        name: 'ticket_returndate'
    },

    {
        name: 'province_idfrom_return', 
        type: 'int'
    },

    {
        name: 'province_idto_return', 
        type: 'int'
    },

    {
        name: 'ticket_state_return', 
        type: 'int'
    }
	
    ]);
	    
    /*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
    requestsFormReader = new Ext.data.JsonReader({
        root : 'data',
        successProperty : 'success',
        totalProperty: 'count',
        id: 'request_id'
    },lodgingRecordUpdate 
    );
	
    /*
	     * Creamos el formulario de alta/modificaci�n de request
	     */
    updateForm = new Ext.FormPanel({
        id: 'form-requests',
        region: 'west',
        split: false,
        collapsible: true,
        frame: true,
        labelWidth: 140,
        width: 755,
        minWidth: 755,
            height: 530,
        waitMsgTarget: true,
        monitorValid: true,
        reader: requestsFormReader,
            items: [
                {
                    layout: 'column',
                    border: false,
                    items: [
                        {
                            columnWidth: .5,
                            layout: 'form',
                            border: false,
                            items: [
                                {
                                    id: 'frm_request_id',
                                    name: 'request_id',
                                    xtype: 'hidden'
                                },
                                {
                                    id: 'frm_ticket_idexit',
                                    name: 'ticket_idexit',
                                    xtype: 'hidden'
                                },
                                {
                                    id: 'frm_ticket_idreturn',
                                    name: 'ticket_idreturn',
                                    xtype: 'hidden'
                                },
                                {
                                    fieldLabel: 'Consecutivo',
                                    id: 'frm_request_consecutive',
                                    hiddenName: 'request_consecutive',
                                    name: 'request_consecutive',
                                    allowBlank: false,
                                    width: 80,
                                    xtype: 'textfield'                                
				    },
                                {
                                    fieldLabel: 'Tarea inversi&oacute;n',
                                    id: 'frm_request_inversiontask',
                                    hiddenName: 'request_inversiontask',
                                    name: 'request_inversiontask',
                                    allowBlank: true,
                                    width: 200,
                                    xtype: 'textfield'
                                },
                                new Ext.form.ComboBox({
                                    store: dataStoreMotive,
                                    fieldLabel: 'Motivo de solicitud',
                                    displayField: 'motive_name',
                                    valueField: 'motive_id',
                                    allowBlank: false,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione motivo ...',
                                    selectOnFocus: true,
                                    width: 200,
                                    id: 'frm_motive_id',
                                    hiddenName: 'motive_id',
                                    name: 'motive_id',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStoreMotive.findExact('motive_id', Ext.getCmp('frm_motive_id').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_motive_id').reset();
                                                return false;
                                            }
                                        }
                                    }
                                }),
                                new Ext.form.ComboBox({
                                    store: dataStoreCenter,
                                    fieldLabel: 'Centro de Costo',
                                    label: 'Centro de Costo',
                                    displayField: 'center_name',
                                    valueField: 'center_id',
                                    hiddenName: 'center_id',
                                    allowBlank: true,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    disabled: true,
                                    emptyText: 'Seleccione un centro de costo...',
                                    selectOnFocus: true,
                                    width: 200,
                                    id: 'frm_center_id',
                                    name: 'center_id',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStoreCenter.findExact('center_id', Ext.getCmp('frm_center_id').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_center_id').reset();
                                                return false;
                                            }
                                        },
                                        'beforerender': function () {
                                            Ext.getCmp('frm_center_id').setValue(session_centerId);
                                            if (session_rollId == 6 || session_rollId == 5 || session_personId == 24 || session_personId == 27 || session_personId == 514 || session_personId == 19 || session_personId == 16533) {
                                                Ext.getCmp('frm_center_id').enable();
                                            }
                                        }

                                    }
                                }),
                                {
                                    fieldLabel: 'Area de trabajo',
                                    id: 'frm_request_area',
                                    hiddenName: 'request_area',
                                    name: 'request_area',
                                    allowBlank: false,
                                    width: 200,
                                    xtype: 'textfield'
                                },
                                new Ext.form.ComboBox({
                                    store: dataStoreCenterSap,
                                    fieldLabel: 'CC entrega anticipo',
                                    label: 'Centro de Costo',
                                    displayField: 'center_name',
                                    valueField: 'center_id',
                                    hiddenName: 'center_idadvance',
                                    allowBlank: true,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione un centro de costo...',
                                    selectOnFocus: true,
                                    width: 200,
                                    id: 'frm_center_idadvance',
                                    name: 'center_idadvance',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStoreCenterSap.findExact('center_id', Ext.getCmp('frm_center_idadvance').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_center_idadvance').reset();
                                                return false;
                                            }
                                        }
                                    }
                                }),
                                {
                                    fieldLabel: 'Persona Autorizada del Grupo',
                                    id: 'frm_person_groupresponsable',
                                    hiddenName: 'person_groupresponsable',
                                    name: 'person_groupresponsable',
                                    allowBlank: true,
                                    width: 200,
                                    xtype: 'textfield'
                                }
                            ]
                        },
                        {
                            columnWidth: .5,
                            layout: 'form',
                            border: false,
                            items: [new Ext.form.ComboBox({
                                store: dataStoreProv,
                                fieldLabel: 'Provincia de la persona',
                                displayField: 'province_name',
                                valueField: 'province_id',
                                hiddenName: 'province_idworker',
                                allowBlank: false,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione una Provincia...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_province_idworker',
                                name: 'province_idworker',
                                listeners: {
                                    'select': function () {
                                        Ext.getCmp('frm_person_idworker').reset();
                                        Ext.getCmp('frm_person_idworker').enable();
                                        Ext.getCmp('frm_btn_person').enable();
                                        dataStorePersons.baseParams = {
                                            province_id: Ext.getCmp('frm_province_idworker').getValue()
                                        };
                                        dataStorePersons.load();
                                    },
                                    'blur': function () {
                                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_idworker').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_province_idworker').reset();
                                            return false;
                                        }
                                    }
                                }
                            }),
                                new Ext.form.ComboBox({
                                    store: dataStorePersons,
                                    fieldLabel: 'Persona',
                                    disabled: true,
                                    //minChars: 3,
                                    editable: true,
                                    displayField: 'person_fullname',
                                    valueField: 'person_id',
                                    hiddenName: 'person_idworker',
                                    allowBlank: false,
                                    forceSelection: false,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione una persona...',
                                    selectOnFocus: false,
                                    width: 200,
                                    value: '',
                                    id: 'frm_person_idworker',
                                    name: 'person_idworker',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStorePersons.findExact('person_id', Ext.getCmp('frm_person_idworker').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_person_idworker').reset();
                                                return false;
                                            }
                                        }
                                        /*,
                                        'keypress': function () {
                                            var personSearch = Ext.getCmp('frm_person_idworker').getRawValue();
                                            if(personSearch.length > 2){
                                                dataStorePersons.baseParams = {
                                                    province_id: Ext.getCmp('frm_province_idworker').getValue(),
                                                    name: Ext.getCmp('frm_person_idworker').getRawValue()
                                                };
                                                dataStorePersons.load();
                                            }
                                        }*/
                                    }
                                }), {
                                    fieldLabel: 'Nueva Persona',
                                    text: 'Agregar ...',
                                    id: 'frm_btn_person',
                                    xtype: 'button',
                                    disabled: true,
                                    handler: function () {
                                        person_ventana();
                                    }
                                }, {
                                    fieldLabel: 'Detalles',
                                    id: 'frm_request_details',
                                    hiddenName: 'request_details',
                                    name: 'request_details',
                                    allowBlank: false,
                                    width: 200,
                                    heigth: 200,
                                    xtype: 'textarea'
                                }, {
                                    fieldLabel: 'Dieta desde',
                                    id: 'frm_diet_entrancedate',
                                    name: 'diet_entrancedate',
                                    hiddenName: 'diet_entrancedate',
                                    //vtype: 'daterange',
                                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                                    format: 'Y-m-d',
                                    //endDateField: 'frm_diet_exitdate',
                                    allowBlank: true,
                                    width: 200,
                                    xtype: 'datefield'
                                }, {
                                    fieldLabel: 'Dieta hasta',
                                    id: 'frm_diet_exitdate',
                                    name: 'diet_exitdate',
                                    hiddenName: 'diet_exitdate',
                                    allowBlank: true,
                                    width: 200,
                                    format: 'Y-m-d',
                                    //vtype: 'daterange',
                                    //startDateField: 'frm_diet_entrancedate',
                                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                                    xtype: 'datefield'
                                }]
                        }
                    ]
                },
                {
                    xtype: 'tabpanel',
                    plain: true,
                    activeTab: 0,
                    labelWidth: 160,
                    height: 280,
                    defaults: {
                        bodyStyle: 'padding:5px'
                    },
                    items: [
                        {
                            title: 'Pasaje de ida',
                            layout: 'form',
                            defaultType: 'textfield',
                            items: [new Ext.form.ComboBox({
                                store: dataStoreLodgingTransport,
                                fieldLabel: 'Transporte',
                                displayField: 'transport_name',
                                valueField: 'transport_id',
                                hiddenName: 'transport_idexit',
                                allowBlank: true,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione un transporte...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_transport_idexit',
                                name: 'transport_idexit',
                                listeners: {
                                    'select': function () {
                                        Ext.getCmp('frm_transport_idlodging').setValue(Ext.getCmp('frm_transport_idexit').getValue());
                                        var id = Ext.getCmp('frm_transport_idexit').getValue() - 1;
                                        if (id == 0) {
                                            updateForm.findById('frm_transport_itinerary').enable();
                                            updateForm.findById('frm_transport_itinerary').setValue('');
                                        } else {
                                            updateForm.findById('frm_transport_itinerary').disable();
                                            Ext.getCmp('frm_ticket_exitdate').setDisabledDays([]);
                                            updateForm.findById('frm_transport_itinerary').setValue('');
                                        }
                                    },
                                    'blur': function () {
                                        var flag = dataStoreLodgingTransport.findExact('transport_id', Ext.getCmp('frm_transport_idexit').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_transport_idexit').reset();
                                            return false;
                                        }
                                    }
                                }

                            }), new Ext.form.ComboBox({
                                store: ['Santiago-Habana', 'Habana-Santiago'],
                                fieldLabel: 'Itinerario',
                                displayField: 'transport_itinerary',
                                valueField: 'transport_itinerary',
                                allowBlank: false,
                                readOnly: true,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione un itinerario...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_transport_itinerary',
                                hiddenName: 'transport_itinerary',
                                name: 'transport_itinerary',
                                disabled: true,
                                listeners: {
                                    'select': function () {
                                        var id = Ext.getCmp('frm_transport_itinerary').getValue();
                                        if (id == 'Santiago-Habana') {
                                            Ext.getCmp('frm_ticket_exitdate').setDisabledDays(['1', '2', '3', '4', '5', '6']);
                                        } else if (id == 'Habana-Santiago') {
                                            Ext.getCmp('frm_ticket_exitdate').setDisabledDays(['0', '1', '2', '3', '4', '5']);
                                        }
                                    }
                                }
                            }), {
                                fieldLabel: 'Fecha Salida',
                                id: 'frm_ticket_exitdate',
                                name: 'ticket_exitdate',
                                hiddenName: 'ticket_exitdate',
                                allowBlank: true,
                                width: 180,
                                vtype: 'daterange',
                                endDateField: 'frm_ticket_returndate',
                                invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                                format: 'Y-m-d',
                                xtype: 'datefield',
                                listeners: {
                                    'select': function () {
                                        /*var date = Ext.getCmp('frm_ticket_exitdate').getValue();
                                         var a = new Date('2011-03-12');
                                         var b = new Date('2011-04-01');
                                         if (date > a && date < b) {
                                         newDate = actualizar(date);
                                         Ext.getCmp('frm_ticket_exitdate').setValue(newDate);
                                         }*/
                                        Ext.getCmp('frm_lodging_entrancedate').setValue(Ext.getCmp('frm_ticket_exitdate').getValue());
                                    }
                                }
                            }, new Ext.form.ComboBox({
                                store: dataStoreProv,
                                fieldLabel: 'Provincia Origen',
                                displayField: 'province_name',
                                valueField: 'province_id',
                                allowBlank: true,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione una Provincia...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_province_idfrom',
                                name: 'province_idfrom',
                                hiddenName: 'province_idfrom',
                                listeners: {
                                    'blur': function () {
                                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_idfrom').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_province_idfrom').reset();
                                            return false;
                                        }
                                    },
                                    'select': function () {
                                        var itinerary = Ext.getCmp('frm_transport_itinerary').getValue();
                                        var from = parseInt(Ext.getCmp('frm_province_idfrom').getValue());
                                        var to = parseInt(Ext.getCmp('frm_province_idto').getValue());
                                        if (itinerary == 'Santiago-Habana') {
                                            if (from < to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idfrom').reset();
                                                return false;
                                            }
                                        } else if (itinerary == 'Habana-Santiago') {
                                            if (from > to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idfrom').reset();
                                                return false;
                                            }
                                        }
                                        Ext.getCmp('frm_province_idto_return').setValue(Ext.getCmp('frm_province_idfrom').getValue());
                                    }

                                }
                            }), new Ext.form.ComboBox({
                                store: dataStoreProv,
                                fieldLabel: 'Provincia Destino',
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
                                id: 'frm_province_idto',
                                name: 'province_idto',
                                hiddenName: 'province_idto',
                                listeners: {
                                    'select': function () {
                                        var itinerary = Ext.getCmp('frm_transport_itinerary').getValue();
                                        var from = parseInt(Ext.getCmp('frm_province_idfrom').getValue());
                                        var to = parseInt(Ext.getCmp('frm_province_idto').getValue());
                                        if (itinerary == 'Santiago-Habana') {
                                            if (from < to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idto').reset();
                                                return false;
                                            }
                                        } else if (itinerary == 'Habana-Santiago') {
                                            if (from > to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idto').reset();
                                                return false;
                                            }
                                        }
                                        Ext.getCmp('frm_province_idlodging').setValue(Ext.getCmp('frm_province_idto').getValue());
                                        Ext.getCmp('frm_province_idfrom_return').setValue(Ext.getCmp('frm_province_idto').getValue());
                                    },
                                    'blur': function () {
                                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_idto').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_province_idto').reset();
                                            return false;
                                        }
                                    }
                                }
                            })]
                        },
                        {
                            title: 'Pasaje de regreso',
                            layout: 'form',
                            defaultType: 'textfield',
                            items: [new Ext.form.ComboBox({
                                store: dataStoreLodgingTransport,
                                fieldLabel: 'Transporte',
                                displayField: 'transport_name',
                                valueField: 'transport_id',
                                hiddenName: 'transport_idreturn',
                                allowBlank: true,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione un transporte...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_transport_idreturn',
                                name: 'transport_idreturn',
                                listeners: {
                                    'select': function () {
                                        Ext.getCmp('frm_transport_idreturnlodging').setValue(Ext.getCmp('frm_transport_idreturn').getValue());
                                        var id = updateForm.findById('frm_transport_idreturn').getValue() - 1;
                                        if (id == 0) {
                                            updateForm.findById('frm_transport_return_itinerary').enable();
                                            updateForm.findById('frm_transport_return_itinerary').setValue('');
                                        } else {
                                            updateForm.findById('frm_transport_return_itinerary').disable();
                                            Ext.getCmp('frm_ticket_returndate').setDisabledDays([]);
                                            updateForm.findById('frm_transport_return_itinerary').setValue('');
                                        }
                                    },
                                    'blur': function () {
                                        var flag = dataStoreLodgingTransport.findExact('transport_id', Ext.getCmp('frm_transport_idreturn').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_transport_idreturn').reset();
                                            return false;
                                        }
                                    }
                                }

                            }), new Ext.form.ComboBox({
                                store: ['Santiago-Habana', 'Habana-Santiago'],
                                fieldLabel: 'Itinerario',
                                displayField: 'transport_itinerary',
                                valueField: 'transport_itinerary',
                                allowBlank: false,
                                typeAhead: true,
                                readOnly: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione un itinerario...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_transport_return_itinerary',
                                hiddenName: 'transport_return_itinerary',
                                name: 'transport_return_itinerary',
                                disabled: true,
                                listeners: {
                                    'select': function () {
                                        var id = Ext.getCmp('frm_transport_return_itinerary').getValue();
                                        if (id == 'Santiago-Habana') {
                                            Ext.getCmp('frm_ticket_returndate').setDisabledDays(['1', '2', '3', '4', '5', '6']);
                                        } else if (id == 'Habana-Santiago') {
                                            Ext.getCmp('frm_ticket_returndate').setDisabledDays(['0', '1', '2', '3', '4', '5']);
                                        }
                                    }
                                }
                            }), {
                                fieldLabel: 'Fecha Regreso',
                                id: 'frm_ticket_returndate',
                                name: 'ticket_returndate',
                                hiddenName: 'ticket_returndate',
                                vtype: 'daterange',
                                startDateField: 'frm_ticket_exitdate',
                                allowBlank: true,
                                format: 'Y-m-d',
                                disabledDaysText: 'No sale el Omnibus',
                                width: 180,
                                invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                                xtype: 'datefield',
                                listeners: {
                                    'select': function () {
                                        /*var date = Ext.getCmp('frm_ticket_returndate').getValue();
                                         var a = new Date('2011-03-13');
                                         var b = new Date('2011-04-01');
                                         if (date > a && date < b) {
                                         newDate = actualizar(date);
                                         Ext.getCmp('frm_ticket_returndate').setValue(newDate);
                                         }*/
                                        Ext.getCmp('frm_lodging_exitdate').setValue(Ext.getCmp('frm_ticket_returndate').getValue());
                                    }
                                }
                            }, new Ext.form.ComboBox({
                                store: dataStoreProv,
                                fieldLabel: 'Provincia Origen',
                                displayField: 'province_name',
                                valueField: 'province_id',
                                allowBlank: true,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione una Provincia...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_province_idfrom_return',
                                name: 'province_idfrom_return',
                                hiddenName: 'province_idfrom_return',
                                listeners: {
                                    'blur': function () {
                                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_idfrom_return').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_province_idfrom_return').reset();
                                            return false;
                                        }
                                    },
                                    'select': function () {
                                        var itinerary = Ext.getCmp('frm_transport_return_itinerary').getValue();
                                        var from = parseInt(Ext.getCmp('frm_province_idfrom_return').getValue());
                                        var to = parseInt(Ext.getCmp('frm_province_idto_return').getValue());
                                        if (itinerary == 'Santiago-Habana') {
                                            if (from < to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idfrom_return').reset();
                                                return false;
                                            }
                                        } else if (itinerary == 'Habana-Santiago') {
                                            if (from >= to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idfrom_return').reset();
                                                return false;
                                            }
                                        }
                                    }
                                }
                            }), new Ext.form.ComboBox({
                                store: dataStoreProv,
                                fieldLabel: 'Provincia Destino',
                                displayField: 'province_name',
                                valueField: 'province_id',
                                allowBlank: true,
                                typeAhead: true,
                                mode: 'local',
                                triggerAction: 'all',
                                emptyText: 'Seleccione una Provincia...',
                                selectOnFocus: true,
                                width: 200,
                                id: 'frm_province_idto_return',
                                name: 'province_idto_return',
                                hiddenName: 'province_idto_return',
                                listeners: {
                                    'blur': function () {
                                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_idto_return').getValue());
                                        if (flag == -1) {
                                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                            Ext.getCmp('frm_province_idto_return').reset();
                                            return false;
                                        }
                                    },
                                    'select': function () {
                                        var itinerary = Ext.getCmp('frm_transport_return_itinerary').getValue();
                                        var from = parseInt(Ext.getCmp('frm_province_idfrom_return').getValue());
                                        var to = parseInt(Ext.getCmp('frm_province_idto_return').getValue());
                                        if (itinerary == 'Santiago-Habana') {
                                            if (from < to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idto_return').reset();
                                                return false;
                                            }
                                        } else if (itinerary == 'Habana-Santiago') {
                                            if (from >= to) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Rectifique que el origen y el destino est&eacute;n en el orden correcto.');
                                                Ext.getCmp('frm_province_idto_return').reset();
                                                return false;
                                            }
                                        }
                                    }
                                }
                            })]
                        },
                        {
                            title: 'Hospedaje',
                            layout: 'form',
                            labelWidth: 200,
                            defaultType: 'textfield',
                            items: [
                                {
                                    xtype: 'checkbox',
                                    id: 'frm_check_lodging',
                                    hiddenName: 'check_lodging',
                                    fieldLabel: 'Solicitar Hospedaje',
                                    checked: false,
                                    listeners: {
                                        'check': function () {
                                            var check = Ext.getCmp('frm_check_lodging').getValue();
                                            if (check == true) {
                                                Ext.getCmp('frm_lodging_entrancedate').enable();
                                                Ext.getCmp('frm_lodging_exitdate').enable();
                                                Ext.getCmp('frm_province_idlodging').enable();
                                                Ext.getCmp('frm_transport_idlodging').enable();
                                                Ext.getCmp('frm_transport_idreturnlodging').enable();
                                                Ext.getCmp('frm_lodging_requestreinforceddiet').enable();
                                                Ext.getCmp('frm_lodging_requestelongationdiet').enable();
                                            } else {
                                                Ext.getCmp('frm_lodging_entrancedate').disable();
                                                Ext.getCmp('frm_lodging_exitdate').disable();
                                                Ext.getCmp('frm_province_idlodging').disable();
                                                Ext.getCmp('frm_transport_idlodging').disable();
                                                Ext.getCmp('frm_transport_idreturnlodging').disable();
                                                Ext.getCmp('frm_lodging_requestreinforceddiet').disable();
                                                Ext.getCmp('frm_lodging_requestelongationdiet').disable();
                                            }
                                        }
                                    }
                                },
                                {
                                    fieldLabel: 'Fecha Entrada',
                                    id: 'frm_lodging_entrancedate',
                                    name: 'lodging_entrancedate',
                                    hiddenName: 'lodging_entrancedate',
                                    vtype: 'daterange',
                                    disabled: true,
                                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                                    format: 'Y-m-d',
                                    endDateField: 'frm_lodging_exitdate',
                                    allowBlank: true,
                                    width: 180,
                                    xtype: 'datefield'
                                },
                                {
                                    fieldLabel: 'Fecha Salida',
                                    id: 'frm_lodging_exitdate',
                                    name: 'lodging_exitdate',
                                    hiddenName: 'lodging_exitdate',
                                    allowBlank: true,
                                    disabled: true,
                                    width: 180,
                                    format: 'Y-m-d',
                                    vtype: 'daterange',
                                    startDateField: 'frm_lodging_entrancedate',
                                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                                    xtype: 'datefield'
                                },
                                {
                                    xtype: 'checkbox',
                                    id: 'frm_lodging_requestreinforceddiet',
                                    name: 'lodging_requestreinforceddiet',
                                    hiddenName: 'lodging_requestreinforceddiet',
                                    fieldLabel: 'Solicito Dieta reforzada',
                                    disabled: true,
                                    checked: false
                                },
                                {
                                    xtype: 'checkbox',
                                    id: 'frm_lodging_requestelongationdiet',
                                    name: 'lodging_requestelongationdiet',
                                    hiddenName: 'lodging_requestelongationdiet',
                                    fieldLabel: 'Solicito Alargamiento de dieta',
                                    disabled: true,
                                    checked: false
                                },
                                new Ext.form.ComboBox({
                                    store: dataStoreProv,
                                    fieldLabel: 'Provincia de Hospedaje',
                                    displayField: 'province_name',
                                    valueField: 'province_id',
                                    allowBlank: true,
                                    disabled: true,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione una Provincia...',
                                    selectOnFocus: true,
                                    width: 200,
                                    id: 'frm_province_idlodging',
                                    name: 'province_idlodging',
                                    hiddenName: 'province_idlodging',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_idlodging').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_province_idlodging').reset();
                                                return false;
                                            }
                                        }
                                    }
                                }),
                                new Ext.form.ComboBox({
                                    store: dataStoreLodgingTransport,
                                    fieldLabel: 'Transporte de Ida',
                                    displayField: 'transport_name',
                                    valueField: 'transport_id',
                                    hiddenName: 'transport_idlodging',
                                    allowBlank: true,
                                    disabled: true,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione un transporte...',
                                    selectOnFocus: true,
                                    width: 200,
                                    id: 'frm_transport_idlodging',
                                    name: 'transport_idlodging',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStoreLodgingTransport.findExact('transport_id', Ext.getCmp('frm_transport_idlodging').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_transport_idlodging').reset();
                                                return false;
                                            }
                                        }
                                    }
                                }),
                                new Ext.form.ComboBox({
                                    store: dataStoreLodgingTransport,
                                    fieldLabel: 'Transporte de Regreso',
                                    displayField: 'transport_name',
                                    valueField: 'transport_id',
                                    hiddenName: 'transport_idreturnlodging',
                                    allowBlank: true,
                                    disabled: true,
                                    typeAhead: true,
                                    mode: 'local',
                                    triggerAction: 'all',
                                    emptyText: 'Seleccione un transporte...',
                                    selectOnFocus: true,
                                    width: 200,
                                    id: 'frm_transport_idreturnlodging',
                                    name: 'transport_idreturnlodging',
                                    listeners: {
                                        'blur': function () {
                                            var flag = dataStoreLodgingTransport.findExact('transport_id', Ext.getCmp('frm_transport_idreturnlodging').getValue());
                                            if (flag == -1) {
                                                Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                                                Ext.getCmp('frm_transport_idreturnlodging').reset();
                                                return false;
                                            }
                                        }
                                    }
                                })
                            ]
                        }
                    ]
                }
            ]
        });
    /*fin del formulario
 	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
    updateForm.addButton({
        text : 'Guardar',
        disabled : false,
        formBind: true,
        handler : function() {
                //en caso de que solamente seleccione una de las fechas de la dieta
                if (((updateForm.findById('frm_diet_entrancedate').getValue() == '') && (updateForm.findById('frm_diet_exitdate').getValue() != '')) || ((updateForm.findById('frm_diet_exitdate').getValue() == '') && (updateForm.findById('frm_diet_entrancedate').getValue() != ''))) {
                    Ext.MessageBox.alert('Error', 'No puede seleccionar solamente una fecha de dieta. Debe seleccionar las dos o ninguna.');
                    return false;
                }
                //en caso de que seleccione las fechas de dieta y no el centro de costo que paga el anticipo
                if (updateForm.findById('frm_diet_exitdate').getValue() != '' && updateForm.findById('frm_diet_entrancedate').getValue() != '' && updateForm.findById('frm_center_idadvance').getValue() == ''){
                    Ext.MessageBox.alert('Error', 'Debe seleccionar un Centro de Costo que pague el Anticipo.');
                    return false;
                }
                //en el caso que seleccione el centro de costo que paga el anticipo y no las fechas de las dieta
                if (updateForm.findById('frm_center_idadvance').getValue() != '' && updateForm.findById('frm_diet_exitdate').getValue() == '' && updateForm.findById('frm_diet_entrancedate').getValue() == ''){
                    Ext.MessageBox.alert('Error', 'Debe seleccionar el intervalo de fecha de la dieta.');
                    return false;
                }
				//evitar q la fecha hasta de la dieta no sea menor a la desde
                if (updateForm.findById('frm_diet_exitdate').getValue() != '' && updateForm.findById('frm_diet_entrancedate').getValue() != '') {
                    var d = updateForm.findById('frm_diet_entrancedate').getValue();
                    var h = updateForm.findById('frm_diet_exitdate').getValue();
                    var dietaDesde = d.format(Date.patterns.ISO8601Short);
                    var dietaHasta = h.format(Date.patterns.ISO8601Short);
                    if (dietaDesde > dietaHasta) {
                        Ext.MessageBox.alert('Error', 'La fecha de la dieta desde no puede ser mayor a la fecha de la dieta hasta.');
                        return false;
                    }
                }
            if ((updateForm.findById('frm_ticket_exitdate').getValue() == '') && (updateForm.findById('frm_ticket_returndate').getValue() == '') && ((updateForm.findById('frm_lodging_entrancedate').getValue() == '') || (updateForm.findById('frm_lodging_exitdate').getValue() == ''))){
                Ext.MessageBox.alert('Error', 'Debe llenar al menos una de las fechas de solicitudes.');
                    return false;
            } else {
                if ((updateForm.findById('frm_ticket_exitdate').getValue() != '')){
                    var ticket = updateForm.findById('frm_ticket_exitdate').getValue();
                    var ticket_date = ticket.format(Date.patterns.ISO8601Short);
                    //var viazul_exit = (Math.round((ticket - dt)/(24*60*60*1000))*1) + 1; 
                    if (today > ticket_date){
                        Ext.MessageBox.alert('Error', 'La fecha de solicitud debe ser mayor o igual a la fecha actual.');
                        return false;
                    }
                    /*if ((updateForm.findById('frm_transport_idexit').getValue() == 2) && (viazul_exit < 7)){
                        Ext.MessageBox.alert('Error', 'Los pasajes de Viazul deben ser solicitados con una semanan de antelacion');
                        return false;
                    }*/
                    if ((updateForm.findById('frm_transport_idexit').getValue() == '') || (updateForm.findById('frm_province_idfrom').getValue() == '') || (updateForm.findById('frm_province_idto').getValue() == '')){
                        Ext.MessageBox.alert('Error', 'Debe llenar todos los datos de la solicitud de pasaje de ida.');
                        return false;
                    }
                } 
                if ((updateForm.findById('frm_ticket_returndate').getValue() != '')){
                    var ticket_return = updateForm.findById('frm_ticket_returndate').getValue();
                    var ticket_returndate = ticket_return.format(Date.patterns.ISO8601Short);
                    var viazul_return = (Math.round((ticket_return - dt)/(24*60*60*1000))*1) + 1; 
                    if (today > ticket_returndate){
                        Ext.MessageBox.alert('Error', 'La fecha de solicitud debe ser mayor o igual a la fecha actual.');
                        return false;
                    }
                    /*if ((updateForm.findById('frm_transport_idreturn').getValue() == 2) && (viazul_return < 7)){
                        Ext.MessageBox.alert('Error', 'Los pasajes de Viazul deben ser solicitados con una semanan de antelacion');
                        return false;
                    }*/
                    if ((updateForm.findById('frm_transport_idreturn').getValue() == '') || (updateForm.findById('frm_province_idfrom_return').getValue() == '') || (updateForm.findById('frm_province_idto_return').getValue() == '')){
                        Ext.MessageBox.alert('Error', 'Debe llenar todos los datos de la solicitud de pasaje de regreso.');
                        return false;
                    }
                } 
                if ((updateForm.findById('frm_lodging_entrancedate').getValue() != '') && (updateForm.findById('frm_lodging_exitdate').getValue() != '')){
                    var lodging_entrance = updateForm.findById('frm_lodging_entrancedate').getValue();
                    var lodging_entrancedate = lodging_entrance.format(Date.patterns.ISO8601Short);
                    var lodging_exitdate = updateForm.findById('frm_lodging_exitdate').getValue();
                    //var diferencia = (Math.round((lodging_exitdate - lodging_entrance)/(24*60*60*1000))*1) + 1; 
                    //Ext.MessageBox.alert('Error', diferencia);
                    //return false;
                    if (today > lodging_entrancedate){
                        Ext.MessageBox.alert('Error', 'La fecha de solicitud debe ser mayor o igual a la fecha actual.');
                        return false;
                    }
                    /*if (diferencia > 29){
                        Ext.MessageBox.alert('Error', 'El hospedaje no puede sobrepasar los 29 d&iacute;as.');
                        return false;
                    }*/
                    if ((updateForm.findById('frm_transport_idreturnlodging').getValue() == '') || (updateForm.findById('frm_transport_idlodging').getValue() == '') || (updateForm.findById('frm_province_idlodging').getValue() == '')){
                        Ext.MessageBox.alert('Error', 'Debe llenar todos los datos de la solicitud de hospedaje.');
                        return false;
                    }
                }
							
                updateForm.getForm().submit({
                    url : baseUrl+'index.php/request/request_requests/insert',
                    waitMsg : 'Salvando datos...',
                    failure: function (form, action) {
                        if(action.failureType == 'server'){ 
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            if (obj.prorogation == 'si') {
                                Ext.MessageBox.confirm('Mensaje', obj.errors.reason, prorogation);
                            } else {
                                Ext.Msg.alert('Error!', obj.errors.reason); 
                            }
                        }else{ 
                            Ext.Msg.alert('Advertencia!', 'Authentication server is unreachable : ' + action.response.responseText); 
                        }
                        sm2.clearSelections();
                        dataStoreRequest.load();
                    },
                    success: function (form, request) {
                        Ext.MessageBox.show({
                            title: 'Datos salvados correctamente',
                            msg: 'Datos salvados correctamente',
                            width: 300,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.INFO
                        });
                        dataStoreRequest.load();
                        sm2.clearSelections();
									
                    }
								
                });
            }
        }
    });
	    
    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
    updateForm.addButton({
        text : 'Salir',
        disabled : false,
        handler : function() {
            updateForm.getForm().reset();
            updateWindow.destroy();
            selectedId = 0;
            sm2.clearSelections();
        }
    });	
	    
    updateForm.on({
        actioncomplete: function(form, action){
            if(action.type == 'load'){
                var transport_id = Ext.getCmp('frm_transport_idexit').getValue();
                var transport_id_return = Ext.getCmp('frm_transport_idreturn').getValue();
                if (transport_id == 1) {
                    Ext.getCmp('frm_transport_itinerary').enable();
                }
                if (transport_id_return == 1) {
                    Ext.getCmp('frm_transport_return_itinerary').enable();
                }
            }
        }
    });
	
    var title = 'Hacer';
    var updateWindow;
    if (id > 0){
        title = 'Editar';
        Ext.getCmp('frm_person_idworker').enable();
        updateForm.load({
            url:baseUrl+'index.php/request/request_requests/getById/'+id
            });
    }
		
    if(! updateWindow){

        updateWindow = new Ext.Window({
            title: title + ' Solicitud',
            layout:'form',
            top: 100,
            width: 783,
                height: 570,
            resizable : false,
            modal: true,
            x:25,
            y:250,
            bodyStyle:'padding:5px;',
            items: updateForm
				
        });
    }
    updateWindow.show(this);

}

});

function validate() {

    //var result = true;
    if ((updateForm.findById('frm_ticket_exitdate').getValue() != '')){
        var ticket = updateForm.findById('frm_ticket_exitdate').getValue();
        var ticket_date = ticket.format(Date.patterns.ISO8601Short);
        if (today > ticket_date){
            Ext.MessageBox.alert('Error', 'La fecha de solicitud debe ser mayor o igual a la fecha actual.');
            return false;
        }
        if ((updateForm.findById('frm_transport_idexit').getValue() == '') || (updateForm.findById('frm_province_idfrom').getValue() == '') || (updateForm.findById('frm_province_idto').getValue() == '')){
            Ext.MessageBox.alert('Error', 'Debe llenar todos los datos de la solicitud de pasaje de ida.');
            return false;
        }
    } else if ((updateForm.findById('frm_ticket_returndate').getValue() != '')){
        var ticket_return = updateForm.findById('frm_ticket_returndate').getValue();
        var ticket_returndate = ticket_return.format(Date.patterns.ISO8601Short);
        if (today > ticket_returndate){
            Ext.MessageBox.alert('Error', 'La fecha de solicitud debe ser mayor o igual a la fecha actual.');
            return false;
        }
        if ((updateForm.findById('frm_transport_idreturn').getValue() == '') || (updateForm.findById('frm_province_idfrom_return').getValue() == '') || (updateForm.findById('frm_province_idto_return').getValue() == '')){
            Ext.MessageBox.alert('Error', 'Debe llenar todos los datos de la solicitud de pasaje de regreso.');
            return false;
        }
    } else if ((updateForm.findById('frm_lodging_entrancedate').getValue() != '') && (updateForm.findById('frm_lodging_exitdate').getValue() != '')){
        var lodging_entrance = updateForm.findById('frm_lodging_entrancedate').getValue();
        var lodging_entrancedate = lodging_entrance.format(Date.patterns.ISO8601Short);
        if (today > lodging_entrancedate){
            Ext.MessageBox.alert('Error', 'La fecha de solicitud debe ser mayor o igual a la fecha actual.');
            return false;
        }
        if ((updateForm.findById('frm_transport_idlodging').getValue() == '') || (updateForm.findById('frm_province_idlodging').getValue() == '')){
            Ext.MessageBox.alert('Error', 'Debe llenar todos los datos de la solicitud de hospedaje.');
            return false;
        }
    }
    return true;	    
}	

function delRecords(btn) {
    if (btn == 'yes') {
        for (var i = 0, len = array.length; i < len; i++) {
            var selectedId = array[i].get('request_id');
            var lodging_state = array[i].get('lodging_state');
            var ticket_state = array[i].get('ticket_state');
            var lodging_entrancedate = array[i].get('lodging_entrancedate');
            var ticket_date = array[i].get('ticket_date');
            if ((parseInt(ticket_state) > 0) || (parseInt(lodging_state) > 0)) {
                Ext.Msg.alert('Error', 'No se puede cancelar esta solicitud porque ya ha sido editada.');
                return false;
            }
            if ((today > lodging_entrancedate) || (today > ticket_date)) {
                Ext.Msg.alert('Valor Inv&aacute;lido', 'No se puede cancelar esta solicitud porque ya ha expirado la fecha de modificaci&oacute;n.');
                return false;
            }
            Ext.Ajax.request({
                url: baseUrl+'index.php/request/request_requests/delete/'+array[i].get('request_id'),
                method: 'POST',
                success: function(){
                    Ext.MessageBox.show({
                        title: 'Solicitud cancelada correctamente',
                        msg: 'Solicitud cancelada correctamente',
                        width: 300,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.INFO
                    });
                },
                failure: function(form, action){
                    Ext.MessageBox.alert('Error', 'No se pudo cancelar la solicitud.');
                }
            });//cierro Ext.Ajax.request
        }//cierro el for
        dataStoreRequest.load({
            params: {
                start:0,
                limit:25
            }
        });
}//cierro el if
dataStoreRequest.load({
    params: {
        start:0,
        limit:25
    }
});
sm2.clearSelections();
}//cierro la funcion
    
function prorogation(btn) {
    if (btn == 'yes') {
        updateForm.getForm().submit({
            url : baseUrl+'index.php/request/request_requests/requestProrogation',
            waitMsg : 'Salvando datos...',
            failure: function (form, action) {
                if(action.failureType == 'server'){ 
                    obj = Ext.util.JSON.decode(action.response.responseText);
                    Ext.Msg.alert('Error!', obj.errors.reason); 
                }else{ 
                    Ext.Msg.alert('Advertencia!', 'Authentication server is unreachable : ' + action.response.responseText); 
                } 
            },
            success: function (form, request) {
                dataStoreRequest.load();
                //updateForm.getForm().reset();
            }
					
        });
    }
}
    
function actualizar(fecha) {
    var milisegundos = parseInt(1 * 24 * 60 * 60 * 1000);
    var tmp = Date.parse(fecha);
    var date = new Date(tmp);
    date.setDate(date.getDate() + 1);
    return date;
}
    
 