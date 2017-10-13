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
                    Requests.requestGrid.editButton.enable();
                } else {
                    Requests.requestGrid.editButton.disable();
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
        url: baseUrl+'index.php/request/request_requests/setDataChangeMotive',
        method: 'POST'
    });
    
    dataStoreRequest = new Ext.data.GroupingStore({
        id: 'requestDS',
        proxy: Requests.dataProxyRequest,
        reader: Requests.dataReaderRequest,
        sortInfo:{
            field: 'request_details', 
            direction: "ASC"
        },
        groupField:'request_details'
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
        },  {
            id: 'request_details',
            name: 'request_details',
            header: "Detalle",
            dataIndex: 'request_details',
            hidden: true
        },  {
            id: 'person_requestedby',
            name : 'person_requestedby',
            header: "Solicitado por",
            width: 130,
            dataIndex: 'person_requestedby',
            sortable: true
        },  {
            id: 'center_name',
            name: 'center_name',
            header: "Centro de Costo",
            width: 90,
            dataIndex: 'center_name',
            sortable: true
        },  {
            id: 'person_worker',
            name: 'person_worker',
            header: "Trabajador",
            width: 160,
            dataIndex: 'person_worker',
            sortable: true
        },  {
            id: 'motive_name',
            name: 'motive_name',
            header: "Motivo",
            width: 100,
            dataIndex: 'motive_name',
            sortable: true
        }/*,{
            id: 'lodging',
            name: 'lodging',
            header: "Hospedaje",
            width: 70,
            renderer: color,
            dataIndex: 'lodging'
        },  {
            id: 'ticket',
            name: 'ticket',
            header: "Pasaje",
            width: 50,
            renderer: color,
            dataIndex: 'ticket'
        }*/]
        );

    /*
     * Creamos el grid 
     */
    Requests.requestGrid = new xg.GridPanel({
        id : 'ctr-requests-grid',
        stripeRows: true,
        store : dataStoreRequest,
        cm : Requests.lodgingRequestColumnMode,
        view: new Ext.grid.GroupingView({
            forceFit:true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Personas" : "Persona"]})'
        }),
        columnLines: true,
        frame:true,
        collapsible: true,
        width : 800,
        height : 500,
        tbar:[{
            text:'Editar',
            tooltip:'Editar solicitud(es) seleccionada(s)',
            iconCls:'add',
            ref: '../editButton',
            disabled: true,
            handler: function(){
                array = sm2.getSelections();
                var chain = '';
                var len = array.length;

                if (len <= 0) {
                    Ext.MessageBox.alert('Error', 'Debe seleccionar al menos una Solicitud para repartir realizar el cambio.');
                    return;
                }
                
                for (var i = 0; i < len; i++) {
                    if (i > 0){
                        chain = chain + '-' + array[i].get('request_id');
                    } else {
                        chain = array[i].get('request_id');
                    }
                }
            
                Ext.Ajax.request({
                    url: baseUrl+'index.php/request/request_requests/changeMotive',
                    disableCaching: false,
                    params: {
                        chain: chain,
                        motive: Ext.getCmp('edit_motive_id').getValue()
                    },
                    success: function(){
                        Ext.MessageBox.show({
                            title: 'Solicitud modificada correctamente',
                            msg: 'Solicitud modificada correctamente',
                            width: 300,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.INFO
                        });
                        sm2.clearSelections();
                        var startDate = Ext.getCmp('startdt').getValue();
                        var endDate = Ext.getCmp('enddt').getValue();
                        var motive = Ext.getCmp('filter_motive_id').getValue();
                        dataStoreRequest.baseParams = {
                            dateStart: startDate.dateFormat('Y-m-d'),
                            dateEnd: endDate.dateFormat('Y-m-d'),
                            motive: motive
                        };
                        dataStoreRequest.load({
                            params: {
                                start:0,
                                limit:150
                            }
                        });
                        Ext.getCmp('edit_motive_id').setValue('');
                    },
                    failure: function(){
                        Ext.MessageBox.alert('Error', 'No se pudo modificar la solicitud correctamente.');
                        sm2.clearSelections();
                    }
					   
                });
				           	
            }
        }, new Ext.form.ComboBox({
            store: dataStoreMotive,
            fieldLabel: 'Motivo de la solicitud',
            displayField: 'motive_name',
            valueField: 'motive_id',
            hiddenName: 'motive_id',
            allowBlank: true,
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText: 'Seleccione un Motivo...',
            selectOnFocus: true,
            width: 200,
            id: 'edit_motive_id',
            name : 'edit_motive_id'
        })],
        bbar: new Ext.PagingToolbar({
            pageSize: 150,
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
        labelWidth: 140,
        height: 120,
        width: 800,
        items: [{
            layout:'column',
            border:false,
            items:[{
                columnWidth:.5,
                layout: 'form',
                border:false,
                items: [	{
                    xtype: 'datefield',
                    width: 200,
                    allowBlank: false,
                    fieldLabel: 'Desde',
                    name: 'startdt',
                    id: 'startdt',
                    vtype: 'daterange',
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    endDateField: 'enddt'
                },	{
                    xtype: 'datefield',
                    width: 200,
                    allowBlank: false,
                    fieldLabel: 'Hasta',
                    name: 'enddt',
                    id: 'enddt',
                    vtype: 'daterange',
                    invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
                    format: 'Y-m-d',
                    startDateField: 'startdt'
                }
                ]
            },{
                columnWidth:.5,
                layout: 'form',
                border:false,
                items: [	new Ext.form.ComboBox({
                    store: dataStoreMotive,
                    fieldLabel: 'Motivo de la solicitud',
                    displayField: 'motive_name',
                    valueField: 'motive_id',
                    hiddenName: 'motive_id',
                    allowBlank: true,
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Motivo...',
                    selectOnFocus: true,
                    width: 200,
                    id: 'filter_motive_id',
                    name : 'filter_motive_id',
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
                })]
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
                    limit:150
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
                dateEnd: endDate.dateFormat('Y-m-d'),
                motive: Requests.filterForm.findById('filter_motive_id').getValue()
            };
            dataStoreRequest.load({
                params: {
                    start:0,
                    limit:150
                }
            });
        }
    });    

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Requests.filterForm.render(Ext.get('requests_grid'));
    Requests.requestGrid.render(Ext.get('requests_grid'));
	
	
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
                limit:15
            }
        });
    }//cierro el if
    dataStoreRequest.load({
        params: {
            start:0,
            limit:15
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
                updateForm.getForm().reset();
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
    
 