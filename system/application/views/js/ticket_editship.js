var shipDataStore;
var array, sm2;

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

var dataRecordPers = new Ext.data.Record.create([
						{name:'person_id'},
						{name:'person_fullname'}
					]);
var dataReaderPers = new Ext.data.JsonReader({root:'data'},dataRecordPers);
var dataProxyPers = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/person/person_persons/setDataGrid',
						method: 'POST'
					});
var dataStorePers = new Ext.data.Store({
						proxy: dataProxyPers,
						reader: dataReaderPers
					});
						
var dataRecordState = new Ext.data.Record.create([
						{name:'viazulstate_id'},
						{name:'viazulstate_name'}
					]);
var dataReaderState = new Ext.data.JsonReader({root:'data'},dataRecordState);
var dataProxyState = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_ticketviazulstates/setDataGrid',
						method: 'POST'
					});
var dataStoreState = new Ext.data.Store({
						proxy: dataProxyState,
						reader: dataReaderState,
						autoLoad: true
					});	
					
var dataRecordMotive = new Ext.data.Record.create([
				        {name: 'motive_id', type: 'int'},
				        {name: 'motive_name', type: 'string'}
				    ]);
var dataRecordTransport = new Ext.data.Record.create([
				        {name: 'transport_id', type: 'int'},
				        {name: 'transport_name', type: 'string'}
				    ]);

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
var dataProxyTransport = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_tickettransports/setDataGrid',
        method: 'POST'
    });
    
var dataReaderTransport = new Ext.data.JsonReader({root:'data'},dataRecordTransport);

var dataStoreTransport = new Ext.data.Store({
        id: 'transportsDS',
        proxy: dataProxyTransport,
        reader: dataReaderTransport
    });				    

var dataReaderMotive =  new Ext.data.JsonReader({root:'data'},dataRecordMotive);

var dataProxyMotive = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/conf/conf_motives/setDataGrid',
        method: 'POST'
    });

var dataStoreMotive = new Ext.data.Store({
        proxy: dataProxyMotive,
        reader: dataReaderMotive,
        autoLoad: true
    });	

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

Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Ship');
    

   	var xg = Ext.grid;
   	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Ship.shipGrid.pdfButton.enable();
                } else {
                    Ship.shipGrid.pdfButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Pasaje -> Gestionar pasaje Barco',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });    
    
    function state(val){
        if(val == 'OK' || val == 'Reservada' || val == 'Reintegrada'){
            return '<span style="color:green;">' + val + '</span>';
        }else {
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }    
    
    /*
     * Definimos el registro
     */
     
    Ship.shipRecord = new Ext.data.Record.create([
        {name: 'request_id', type: 'int'},
        {name: 'request_date'},
        {name: 'ticket_date'},
        {name: 'person_worker', type: 'string'},
        {name: 'province_namefrom', type: 'string'},
        {name: 'province_nameto', type: 'string'},
        {name: 'state'}
    ]);
   
    /*
     * Creamos el reader para el Grid de cadenas serviceeras
     */
    Ship.shipGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count'},
        Ship.shipRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Ship.shipDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/ticket/ticket_editship/setDataGrid',
        method: 'POST'
    });

    shipDataStore = new Ext.data.Store({
        id: 'shipDS',
        proxy: Ship.shipDataProxy,
        reader: Ship.shipGridReader
    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Ship.shipColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },{
	   		id: 'state',
            name: 'state',
            header: 'Estado',
            renderer: state,
            width: 90,
            dataIndex: 'state',
            sortable: true
        },{
	   		id: 'request_date',
            name: 'request_date',
            header: 'Solicitado',
            width: 120,
            format: 'dd-mm-YYYY',
            dataIndex: 'request_date',
            sortable: true
        },{
	   		id: 'ticket_date',
            name: 'ticket_date',
            header: 'Salida',
            width: 80,
            format: 'dd-mm-YYYY',
            dataIndex: 'ticket_date',
            sortable: true
        },{
	   		id: 'person_worker',
            name: 'person_worker',
            header: 'Nombre y Apellidos',
            width: 180,
            dataIndex: 'person_worker',
            sortable: true
        },{
            id: 'province_namefrom',
            name : 'province_namefrom',
            header: 'Origen',
            width: 150,
            dataIndex: 'province_namefrom',
            sortable: false
        },{
            id: 'province_nameto',
            name : 'province_nameto',
            header: 'Destino',
            width: 150,
            dataIndex: 'province_nameto',
            sortable: false
        }]
    );

    /*
     * Creamos el grid
     */
    Ship.shipGrid = new xg.GridPanel({
        id : 'ctr-ship-grid',
        store : shipDataStore,
        cm : Ship.shipColumnMode,
        viewConfig: {
            forceFit:false
        },
        //columnLines: true,
        frame:true,
        stripeRows: true,
        collapsible: true,
        width : 750,
        height : 380,
        tbar:[{
            text:'Exportar a pdf',
            tooltip:'Exportar a pdf',
            iconCls:'pdf',
            ref: '../pdfButton',
            disabled: true,
            handler: function(){
        				array = sm2.getSelections();
						var len = array.length;
						id = array[0].get('request_id');
				        date = array[0].get('ticket_date');
						if (len > 1){
							Ext.MessageBox.alert('Error', 'Debe seleccionar un solo pasaje para mostrar su informaci&oacute;n.');
							sm2.clearSelections();
							return false;
						} else {
							Ship.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/ticket/ticket_editship/shipPdf/'+id+'/'+date ;
							Ship.filterForm.getForm().getEl().dom.method = 'POST';
							Ship.filterForm.getForm().submit();							
						}
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: shipDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

	Ship.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 160,
        height: 130,
        width: 750,
        items: [new Ext.form.ComboBox({
   			store: dataStoreMotive,
   			fieldLabel: 'Motivo del viaje',
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
        }),{
            xtype: 'datefield',
            width: 200,
            allowBlank: false,
            fieldLabel: 'Desde',
            name: 'startdt',
            id: 'startdt',
            vtype: 'daterange',
            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
	    format: 'Y-m-d',
            endDateField: 'enddt' // id of the end date field
        },{
            xtype: 'datefield',
            width: 200,
            allowBlank: false,
            fieldLabel: 'Hasta',
            name: 'enddt',
            id: 'enddt',
            vtype: 'daterange',
            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
	    format: 'Y-m-d',
            startDateField: 'startdt' // id of the start date field
        }]
    });

	Ship.filterForm.addButton({
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Ship.filterForm.getForm().reset();
           shipDataStore.baseParams = {
               dateStart: '1900-01-01',
               dateEnd: '1900-01-01',
               motive: 0
           };
           shipDataStore.load({params: {start:0,limit:15}});
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Ship.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Ship.filterForm.findById('startdt').getValue();
           	var endDate = Ship.filterForm.findById('enddt').getValue();
			var motive = Ship.filterForm.findById('filter_motive_id').getValue();
           	shipDataStore.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d'),
				motive: motive
           	};
           	shipDataStore.load({params: {start:0,limit:15}});
       	}
   	});    

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Ship.shipGrid.on('rowdblclick',function( grid, row, evt) {
        selectedId = shipDataStore.getAt(row).data.request_id;
        selectedDate = shipDataStore.getAt(row).data.ticket_date;
        update_ventana(selectedId, selectedDate);
    });
    
	function update_ventana(id, date){
	
		recordUpdate = new Ext.data.Record.create([
	        {name: 'request_id', type: 'int'},
	        {name: 'request_date'},
	        {name: 'ticket_date'},
	        {name: 'person_namerequestedby', type: 'string'},
	        {name: 'center_name', type: 'string'},
	        {name: 'transport_name', type: 'string'},
	        {name: 'person_nameworker', type: 'string'},
	        {name: 'province_idfrom', type: 'string'},
	        {name: 'province_idto', type: 'string'},
	        {name: 'motive_name', type: 'string'},
	        {name: 'ship_mco', type: 'string'},
	        {name: 'ship_cheque', type: 'string'},
	        {name: 'ship_exithour'},
	        {name: 'ship_arrivalhour'},
	        {name: 'ship_price', type: 'float'},
	        {name: 'ship_ticketnumber', type: 'string'},
	        {name: 'state_id', type: 'int'}
	    ]);
	    
		formReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'request_id'
	        },recordUpdate
	    );
	
   
    	var updateWindow;

	    var updateForm = new Ext.FormPanel({
	        id: 'upd-ship',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        labelWidth: 150,
	        width: 400,
	        minWidth: 400,
	        height: 470,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: formReader,
	        items: [
	        		{
			            fieldLabel : 'Fecha Solicitud',
			            id: 'upd_request_date',
			            name : 'request_date',
			            disabled: true,
			            width: 140,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Fecha de Viaje',
			            id: 'upd_ticket_date',
			            name : 'ticket_date',
			            disabled: false,
			            width: 100,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Solicitado por',
			            id: 'upd_person_namerequestedby',
			            name : 'person_namerequestedby',
			            disabled: true,
			            width: 200,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Centro de Costo',
			            id: 'upd_center_name',
			            name : 'center_name',
			            disabled: true,
			            width: 200,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Transporte que usar&aacute',
			            id: 'upd_transport_name',
			            name : 'transport_name',
			            disabled: true,
			            width: 200,
			            xtype: 'textfield'
			        },{
			            fieldLabel : 'Nombre y Apellidos',
			            id: 'upd_person_nameworker',
			            name : 'person_nameworker',
			            disabled: true,
			            width: 200,
			            xtype: 'textfield'
			        }, new Ext.form.ComboBox({
	           			store: dataStoreProv,
	           			fieldLabel: 'Origen',
	           			displayField: 'province_name',
	           			valueField: 'province_id',
	           			hiddenName: 'province_idfrom',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione una Provincia...',
	           			selectOnFocus: true,
	           			width: 200,
					    id: 'upd_province_idfrom',
			            name : 'province_idfrom',
			            listeners: {
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idfrom').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('upd_province_idfrom').reset();
					    			return false;
					    		}
							}
				 		}
	                }),new Ext.form.ComboBox({
	          			store: dataStoreProv,
	          			fieldLabel: 'Destino',
	          			displayField: 'province_name',
	          			valueField: 'province_id',
	          			hiddenName: 'province_idto',
	          			allowBlank: false,
	          			typeAhead: true,
	          			mode: 'local',
	          			triggerAction: 'all',
	          			emptyText: 'Seleccione una Provincia...',
	          			selectOnFocus: true,
	          			width: 200,
					    id: 'upd_province_idto',
			            name : 'province_idto',
			            listeners: {
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('upd_province_idto').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('upd_province_idto').reset();
					    			return false;
					    		}
							}
				 		}
	                }),{
			            fieldLabel : 'Motivo del Viaje',
			            id: 'upd_motive_name',
			            name : 'motive_name',
			            disabled: true,
			            width: 180,
			            xtype: 'textfield'
			        }, {
			            fieldLabel : 'M.C.O',
			            id: 'upd_ship_mco',
			            name : 'ship_mco',
			            allowBlank: true,
			            xtype: 'numberfield'
			        }, {
			            fieldLabel : 'Cheque',
			            id: 'upd_ship_cheque',
			            name : 'ship_cheque',
			            allowBlank: true,
			            xtype: 'numberfield'
			        }, new Ext.form.TimeField({
					    minValue: '00:00',
					    maxValue: '23:00',
					    increment: 30,
					    format: 'H:i',
					    allowBlank: true,
					    width: 75,
	           			displayField: 'ship_exithour',
	           			valueField: 'ship_exithour',
	           			hiddenName: 'ship_exithour',
	           			fieldLabel: 'Hora salida',
					    id: 'upd_ship_exithour',
			            name : 'ship_exithour'
					}), new Ext.form.TimeField({
					    minValue: '00:00',
					    maxValue: '23:00',
					    increment: 30,
					    format: 'H:i',
					    allowBlank: true,
					    width: 75,
	           			displayField: 'ship_arrivalhour',
	           			valueField: 'ship_arrivalhour',
	           			hiddenName: 'ship_arrivalhour',
	           			fieldLabel: 'Hora llegada',
					    id: 'upd_ship_arrivalhour',
			            name : 'ship_arrivalhour'
					}), {
			            fieldLabel : 'Precio',
			            id: 'upd_ship_price',
			            name : 'ship_price',
			            allowBlank: true,
			            xtype: 'numberfield'
			        }, 	{
			            fieldLabel : 'No. de boleto',
			            id: 'upd_ship_ticketnumber',
			            name : 'ship_ticketnumber',
			            allowBlank: true,
			            xtype: 'textfield'
			        }, 	new Ext.form.ComboBox({
	           			store: dataStoreState,
	           			fieldLabel: 'Estado de la reserva',
	           			displayField: 'viazulstate_name',
	           			valueField: 'viazulstate_id',
	           			hiddenName: 'state_id',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione un Estado...',
	           			selectOnFocus: true,
	           			width: 200,
	           			id: 'upd_shipstate_id',
			            name : 'shipstate_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreState.findExact( 'viazulstate_id', Ext.getCmp('upd_shipstate_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('upd_shipstate_id').reset();
					    			return false;
					    		}
							}
				 		}
	                }), {
			            id: 'upd_request_id',
			            name : 'request_id',
			            xtype: 'hidden'
			        }]
			
	    });
	    
	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    updateForm.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
		    	var ticketdate = Ext.getCmp('upd_ticket_date').getValue();
		   		if (today > ticketdate && session_rollId < 5){
					Ext.MessageBox.alert('Error', 'No se puede editar este pasaje porque la fecha de viaje ya expir&oacute;.');
					return false;
	       		}
				if (Ext.getCmp('upd_shipstate_id').getValue() == 2){
					if (Ext.getCmp('upd_ship_exithour').getValue() == '' || Ext.getCmp('upd_ship_arrivalhour').getValue() == ''){
						Ext.MessageBox.alert('Error', 'Debe introducir Hora salida y Hora llegada.');
					} else {
						updateForm.getForm().submit({
			                url : baseUrl+'index.php/ticket/ticket_editship/insert',
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
			                    shipDataStore.load();
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
			                    updateForm.getForm().reset();
			                    updateWindow.destroy();
			                    sm2.clearSelections();
			                    shipDataStore.load();
			                }
			            });					
					}
				} else if (Ext.getCmp('upd_shipstate_id').getValue() == 1){
					if (Ext.getCmp('upd_ship_mco').getValue() == '' && Ext.getCmp('upd_ship_cheque').getValue() == ''){
						Ext.MessageBox.alert('Error', 'Debe introducir: M.C.O o Cheque.');
					} else if (Ext.getCmp('upd_ship_exithour').getValue() == '' || Ext.getCmp('upd_ship_arrivalhour').getValue() == '' || Ext.getCmp('upd_ship_price').getValue() == ''){
						Ext.MessageBox.alert('Error', 'Debe introducir: Hora salida, Hora llegada y Precio.');
					} else {
					
						updateForm.getForm().submit({
			                url : baseUrl+'index.php/ticket/ticket_editship/insert',
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
			                    shipDataStore.load();
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
			                    updateForm.getForm().reset();
			                    updateWindow.destroy();
			                    sm2.clearSelections();
			                    shipDataStore.load();
			                }
			            });					
					}
					
				} else if (Ext.getCmp('upd_shipstate_id').getValue() == 6){
					if (Ext.getCmp('upd_ship_exithour').getValue() == '' || Ext.getCmp('upd_ship_arrivalhour').getValue() == '' || Ext.getCmp('upd_ship_mco').getValue() == '' || Ext.getCmp('upd_ship_price').getValue() == ''){
						Ext.MessageBox.alert('Error', 'Debe introducir: Hora salida, Hora llegada, No. Voucher y Precio.');
					} else {
					
						updateForm.getForm().submit({
			                url : baseUrl+'index.php/ticket/ticket_editship/insert',
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
			                    shipDataStore.load();
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
			                    updateForm.getForm().reset();
			                    updateWindow.destroy();
			                    sm2.clearSelections();
			                    shipDataStore.load();
			                }
			            });					
					}
					
				} else {					
						updateForm.getForm().submit({
			                url : baseUrl+'index.php/ticket/ticket_editship/insert',
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
			                    shipDataStore.load();
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
			                    updateForm.getForm().reset();
			                    updateWindow.destroy();
			                    sm2.clearSelections();
			                    shipDataStore.load();
			                }
			            });					
					}
	        }
	    });
	    
	    /*
	     * A�adimos el bot�n para borrar el formulario
	     */
	    updateForm.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	            updateForm.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });	    
	    
	    updateForm.load({url:baseUrl+'index.php/ticket/ticket_editship/getById/'+id+'/'+date});
		
		if(!updateWindow){

				updateWindow = new Ext.Window({
				title: 'Editar Pasaje',
				layout: 'form',
				top: 200,
				width: 425,
				height: 510,
				resizable : false,
				modal: true,
				bodyStyle:'padding:5px;',
				items: updateForm
				
				});
			}
		updateWindow.show(this);

	}
    
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Ship.filterForm.render(Ext.get('ship_grid'));
	Ship.shipGrid.render(Ext.get('ship_grid'));
    //viazulDataStore.load({params: {start:0,limit:30}});
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/request/request_requests/deleteTicket/'+array[i].get('request_id')+'/'+array[i].get('ticket_date'),
				   method: 'GET',
				   disableCaching: false,
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la Solicitud.');
				   }
				});
		    }
			sm2.clearSelections();
		    shipDataStore.load({params: {start:0,limit:15}});
    	}
    }
    
	
