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
						autoLoad:true
						});
						
var dataRecordCafeteria = new Ext.data.Record.create([
						{name:'cafeteria_id'},
						{name:'cafeteria_name'}
					]);

var dataReaderCafeteria = new Ext.data.JsonReader({root:'data'},dataRecordCafeteria);

var dataProxyCafeteria = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_cafeterias/setDataByProvince',
						method: 'POST'
					});

var dataStoreCafeteria = new Ext.data.Store({
						proxy: dataProxyCafeteria,
						reader: dataReaderCafeteria,
						autoLoad:true
						});	

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
  						reader: dataReaderHotel,
  						autoLoad:true
					});	

var dataRecordLinearity = new Ext.data.Record.create([
						{name:'linearity_id'},
						{name:'linearity_name'}
					]);

var dataReaderLinearity = new Ext.data.JsonReader({root:'data'},dataRecordLinearity);

var dataProxyLinearity = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_hotellinearities/setDataGrid',
						method: 'POST'
					});

var dataStoreLinearity = new Ext.data.Store({
						proxy: dataProxyLinearity,
						reader: dataReaderLinearity,
						autoLoad:true
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

var dataStoreCenter= new Ext.data.Store({
						proxy: dataProxyCenter,
						reader: dataReaderCenter,
						autoLoad:true
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

var dataRecordTransport= new Ext.data.Record.create([
						{name:'transport_id'},
						{name:'transport_name'}
					]);

var dataReaderTransport = new Ext.data.JsonReader({root:'data'},dataRecordTransport);

var dataProxyTransport = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_lodgingtransports/setDataGrid',
						method: 'POST',
						autoLoad:true
					});

var dataStoreTransport= new Ext.data.Store({
						proxy: dataProxyTransport,
						reader: dataReaderTransport,
						autoLoad:true
					});

var dataRecordUsers = new Ext.data.Record.create([
						{name:'person_id'},
						{name:'person_fullname'}
					]);	

var dataReaderUsers = new Ext.data.JsonReader({root:'data'},dataRecordUsers);					

var dataProxyUsers = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/user/user_users/setData',
        method: 'POST'
    });

var dataStoreUsers = new Ext.data.Store({
						proxy: dataProxyUsers,
						reader: dataReaderUsers,
						autoLoad:true
						});
    
var dataRecordPersons= new Ext.data.Record.create([
						{name:'person_id'},
						{name:'person_fullname'}
					]);

var dataReaderPersons= new Ext.data.JsonReader({root:'data'},dataRecordPersons);

var dataProxyPersons = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/person/person_persons/setDataGrid/',
						method: 'POST'
					});

var dataStorePersons= new Ext.data.Store({
						proxy: dataProxyPersons,
						reader: dataReaderPersons,
						autoLoad:true
						});

var dataStoreLodgingEdit, sm2;


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

    Ext.form.Field.prototype.msgTarget = 'side';
	
	/*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Lodgings');
   
   	var xg = Ext.grid; 
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Lodgings.editGrid.removeButton.enable();
                    Lodgings.editGrid.editButton.enable();
                    Lodgings.editGrid.voucherButton.enable();
                } else {
                    Lodgings.editGrid.removeButton.disable();
                    Lodgings.editGrid.editButton.disable();
                    Lodgings.editGrid.voucherButton.disable();
                }
            }
        }
    });
    
    var p = new Ext.Panel({
        title: 'Hospedaje -> Gestionar hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    function state(val){
        if(val == 'OK'){
            return '<span style="color:green;">' + val + '</span>';
        }else {
            return '<span style="color:red;">' + val + '</span>';
        }
        return val;
    }
    
    function letter(val){
        if(val == '---'){
            return '<span style="color:red;">' + val + '</span>';
        }else {
            return '<span style="color:green;">' + val + '</span>';
        }
        return val;
    }
    
    function voucher(val){
        if(val == 'NO'){
            return '<span style="color:red;">' + val + '</span>';
        }else {
            return '<span style="color:green;">' + val + '</span>';
        }
        return val;
    }
    
   	var miboton= new Ext.Button({
								 
		text : 'Generar carta',
		disabled : false,
        handler : function() {
		    array = sm2.getSelections();
						var chain = '';
						var len = array.length;
						var letter = Ext.getCmp('edit_letter_id').getValue();
						if (len == 0 && letter == ''){
							Ext.MessageBox.alert('Error', 'Debe seleccionar un hospedaje o introducir un n&uacute;mero de carta.');
						} else if (len > 0 && letter == ''){
							//var tmp = true;
							for (var i = 0; i < len; i++) {
								if (array[i].get('hotel_name') == '---'){
									Ext.MessageBox.alert('Error', 'Asegurese que los hospedajes seleccionados ya tienen asignado un hotel.');
									return false;
								}
							}
							for (var i = 0; i < len; i++) {
								if (i > 0){
									chain = chain + '-' + array[i].get('request_id');
								} else {
									chain = array[i].get('request_id');
								}
							}
							
					        Lodgings.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_edit/hotel_letter/'+chain;
	                        Lodgings.filterForm.getForm().getEl().dom.method = 'POST';
                            Lodgings.filterForm.getForm().submit();
			
							sm2.clearSelections();
							dataStoreLodgingEdit.load({params: {start:0,limit:50}});
						} else if (letter != '' && len == 0) {
							Lodgings.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_edit/getByLetter/'+letter;
	                        Lodgings.filterForm.getForm().getEl().dom.method = 'POST';
                            Lodgings.filterForm.getForm().submit();
							Ext.getCmp('edit_letter_id').setValue('');						
						} else if ( letter != '' && len > 0){
							Ext.MessageBox.alert('Error', 'Debe seleccionar un grupo de hospedajes o introducir un n&uacute;mero para generar una carta, pero las 2 cosas a la vez no est&aacute;n permitidas.');
						}
		}						 
						 
	});

   	var voucherButton = new Ext.Button({
								 
		text : 'Generar Voucher',
		disabled : true,
		ref: '../voucherButton',
        handler : function() {
		    array = sm2.getSelections();
						var len = array.length;
						if (len > 1){
							Ext.MessageBox.alert('Error', 'Debe seleccionar un solo hospedaje para generar el voucher');
							return false;
						} else if (len = 1){
							var lodging = new Date(array[0].get('lodging_entrancedate'));
                					var entrancedate = lodging.format(Date.patterns.ISO8601Short);
                					var limit = new Date("2014-08-01");
                					var limitdate = limit.format(Date.patterns.ISO8601Short);
							if (entrancedate >= limitdate) {
                    						Ext.MessageBox.alert('Error', 'Para los Hospedajes posteriores al 1ro de Agosto no se permite generar voucher.');
                    						return false;
                					}
							if (array[0].get('hotel_name') == '---'){
									Ext.MessageBox.alert('Error', 'Asegurese que el hospedaje seleccionado tenga asignado un hotel.');
									return false;
							} else {
						        Lodgings.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_edit/voucher/'+array[0].get('request_id');
		                        Lodgings.filterForm.getForm().getEl().dom.method = 'POST';
								Lodgings.filterForm.getForm().submit();
							}
						}
						sm2.clearSelections();
						dataStoreLodgingEdit.load({params: {start:0,limit:50}});
		}						 
						 
	});

    Lodgings.dataRecordLodgingEdit = new Ext.data.Record.create([
        {name: 'request_id'},
        {name: 'request_date'},
        {name: 'lodging_entrancedate'},
        {name: 'lodging_exitdate'},
        {name: 'center_name'},
		{name: 'person_worker'},
		{name: 'state'},
		{name: 'hotel_name'},
		{name: 'request_details'},
		{name: 'letter'},
		{name: 'voucher'}
    ]);
	/*
     * Creamos el reader para el Grid 
     */
    Lodgings.dataReaderLodgingEdit = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'request_id'},
        Lodgings.dataRecordLodgingEdit
    );


    Lodgings.dataProxyLodgingEdit = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/lodging/lodging_edit/setDataGrid',
        method: 'POST'
    });  
   
	dataStoreLodgingEdit= new Ext.data.GroupingStore({
		id: 'editDS',
		proxy: Lodgings.dataProxyLodgingEdit,
		reader: Lodgings.dataReaderLodgingEdit,
		sortInfo:{field: 'request_details', direction: "ASC"},
		groupField:'request_details'
	});

	/*
     * Creamos el columnModel para el grid
     */
	Lodgings.lodgingEditColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },	{
            id: 'request_details',
            name : 'request_details',
            header: "Detalle",
            dataIndex: 'request_details',
            hidden: true
        },	{
	   		id: 'state',
            name: 'state',
			header: "Estado",
			renderer: state,
			width: 90,
			dataIndex: 'state',
			sortable: false
		},	{
	   		id: 'lodging_entrancedate',
            name: 'lodging_entrancedate',
            header: 'Entrada',
            width: 80,
            dataIndex: 'lodging_entrancedate',
            sortable: true
        },	{
            id: 'lodging_exitdate',
            name : 'lodging_exitdate',
            header: 'Salida',
			width: 80,
            dataIndex: 'lodging_exitdate',
            sortable: false
        },	{
	   		id: 'letter',
            name: 'letter',
			header: "No. Carta",
			renderer: letter,
			width: 70,
			dataIndex: 'letter',
			sortable: false
		},	{
	   		id: 'voucher',
            name: 'voucher',
			header: "Dieta",
			renderer: voucher,
			width: 50,
			dataIndex: 'voucher',
			sortable: false
		},	{
	   		id: 'hotel_name',
            name: 'hotel_name',
			header: "Hotel",
			width: 130,
			dataIndex: 'hotel_name',
			sortable: true
		},	{
	   		id: 'person_worker',
            name: 'person_worker',
			header: "Trabajador",
			width: 170,
			dataIndex: 'person_worker',
			sortable: true
		},	{
            id: 'center_name',
            name : 'center_name',
            header: "Centro de Costo",
            width: 120,
            dataIndex: 'center_name',
            sortable: true
        }/*{
	   		id: 'request_date',
            name: 'request_date',
            header: 'Fecha de Solicitud',
			format: 'dd-mm-YYYY',
            width: 95,
            dataIndex: 'request_date',
            sortable: true
        },*/]
    );

    /*
     * Creamos el grid 
     */
    Lodgings.editGrid = new xg.GridPanel({
        id : 'ctr-edits-grid',
        store : dataStoreLodgingEdit,
        cm : Lodgings.lodgingEditColumnMode,
		view: new Ext.grid.GroupingView({
	          forceFit:true,
	          groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Personas" : "Persona"]})'
	    }),
        stripeRows: true,
        frame:true,
        collapsible: true,
        width : 750,
        height : 500,
        tbar:[{
            text:'Cancelar',
            tooltip:'Cancelar la(s) Solicitud(es) de Hospedaje Seleccionada(s)',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea cancelar este(os) Hospedaje(s)?', delRecords);
				    }
            	}
        	},'-',{
        	text:'Editar',
	            tooltip:'Editar hospedaje(s) seleccionado(s)',
	            iconCls:'add',
	            ref: '../editButton',
	            disabled: true,
	            handler: function(){
		            	    array = sm2.getSelections();
							for (var i = 0, len = array.length; i < len; i++) {
								var exitdate = array[i].get('lodging_exitdate');
						   		if (today > exitdate){
									Ext.MessageBox.alert('Error', 'No se puede editar este hospedaje porque la fecha de entrada ya expir&oacute;.');
									return false;
					       		}
						        Ext.Ajax.request({
								   url: baseUrl+'index.php/lodging/lodging_edit/insertMulti/'+array[i].get('request_id')+'/'+Ext.getCmp('edit_hotel_id').getValue(),
								   method: 'GET',
								   disableCaching: false,
								   success: function(){
								   },
								   failure: function(){
								   		Ext.MessageBox.alert('Error', 'No se pudo editar el hospedaje.');
								   }
								});
						    }
						    sm2.clearSelections();
				           	var startDate = Ext.getCmp('startdt').getValue();
				           	var endDate = Ext.getCmp('enddt').getValue();
							var center = Ext.getCmp('filter_center_id').getValue();
							var province = Ext.getCmp('filter_province_id').getValue();
							var motive = Ext.getCmp('filter_motive_id').getValue();
							var hotel = Ext.getCmp('filter_hotel_id').getValue();
				           	dataStoreLodgingEdit.baseParams = {
								dateStart: startDate.dateFormat('Y-m-d'),
								dateEnd: endDate.dateFormat('Y-m-d'),
								center: center,
								province: province,
								motive: motive,
								hotel: hotel
				           	};
				           	Ext.getCmp('edit_hotel_id').setValue('');
				           	dataStoreLodgingEdit.load({params: {start:0,limit:50}});
				           	Lodgings.editGrid.getStore().reload();
				           	
			            }
				}, new Ext.form.ComboBox({
	           			store: dataStoreHotel,
	           			fieldLabel: 'Hotel',
	           			displayField: 'hotel_name',
	           			valueField: 'hotel_id',
	           			hiddenName: 'hotel_id',
	           			allowBlank: true,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all', 					
	           			emptyText: 'Seleccione un Hotel...',
	           			selectOnFocus: true,
	           			width: 200,
					    id: 'edit_hotel_id',
			            name : 'edit_hotel_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreHotel.findExact( 'hotel_id', Ext.getCmp('edit_hotel_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('edit_hotel_id').reset();
					    			return false;
					    		}
							}
				 		}
			}),'-',miboton,{
	            id: 'edit_letter_id',
	            name : 'letter_id',
	            allowBlank: true,
	            width: 100,
	            xtype: 'numberfield'
	            },'-',voucherButton
	            ,'-',voucherButton
	    ],
        bbar: new Ext.PagingToolbar({
            pageSize: 50,
            store: dataStoreLodgingEdit,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    }); 
    
	Lodgings.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 140,
        height: 140,
        width: 750,
        items: [{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items: [	new Ext.form.ComboBox({
		        			store: dataStoreProv,
		          			fieldLabel: 'Provincia del Hospedaje',
		          			displayField: 'province_name',  
		          			valueField: 'province_id',
		          			hiddenName: 'province_id',
		          			allowBlank: false,
		          			formBind: true,
		          			typeAhead: true,
		          			mode: 'local',
		          			triggerAction: 'all',
		          			emptyText: 'Seleccione una Provincia...',
		          			selectOnFocus: true,
		          			width: 200,
						    id: 'filter_province_id',
				            name : 'filter_province_id',
				            listeners: {
								'select': function(){
											dataStoreHotel.baseParams = {province_id: Ext.getCmp('filter_province_id').getValue()};
											dataStoreHotel.load();
											dataStoreCafeteria.baseParams = {province_id: Ext.getCmp('filter_province_id').getValue()};
											dataStoreCafeteria.load();
								},
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
						    			Ext.getCmp('filter_province_id').reset();
						    			return false;
						    		}
								}
					 		}
		    				
						}),	{
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
		         			store: dataStoreHotel,
		           			fieldLabel: 'Hotel',
		           			displayField: 'hotel_name',
		           			valueField: 'hotel_id',
		           			hiddenName: 'hotel_id',
		           			allowBlank: true,
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all', 					
		           			emptyText: 'Seleccione un Hotel...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_hotel_id',
				            name : 'filter_hotel_id',
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
	            		}),	new Ext.form.ComboBox({
				   			store: dataStoreMotive,
				   			fieldLabel: 'Motivo del hospedaje',
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
	            		}),	new Ext.form.ComboBox({
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
							name : 'filter_center_id',
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
	            		})]
            }]
        }]
	});

	Lodgings.filterForm.addButton({
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Lodgings.filterForm.getForm().reset();
           dataStoreLodgingEdit.baseParams = {
				dateStart: '1900-01-01',
				dateEnd: '1900-01-01',
				center: 0,
				province: 0,
				motive: 0,
				hotel: 0
           };
           dataStoreLodgingEdit.load({params: {start:0,limit:50}});
       }
   });

   /*
    * A�adimos el bot�n para filtrar
    */
   	Lodgings.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Ext.getCmp('startdt').getValue();
           	var endDate = Ext.getCmp('enddt').getValue();
			var center = Ext.getCmp('filter_center_id').getValue();
			var province = Ext.getCmp('filter_province_id').getValue();
			var motive = Ext.getCmp('filter_motive_id').getValue();
			var hotel = Ext.getCmp('filter_hotel_id').getValue();
           	dataStoreLodgingEdit.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d'),
				center: center,
				province: province,
				motive: motive,
				hotel: hotel
           	};
           	dataStoreLodgingEdit.load({params: {start:0,limit:50}});
       	}
   	});    
    

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
	
    Lodgings.editGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = dataStoreLodgingEdit.getAt(row).data.request_id;
        update_ventana(selectedId);
    });

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Lodgings.filterForm.render(Ext.get('edit_grid'));
	Lodgings.editGrid.render(Ext.get('edit_grid'));
	
	function update_ventana(id){
	
		lodgingRecordUpdate = new Ext.data.Record.create([

	       	{name: 'request_id', type: 'int'},
	        {name: 'request_date'},
	        {name: 'request_details'},
			{name: 'lodging_entrancedate'},
	        {name: 'lodging_exitdate'},
	        {name: 'person_idrequestedby', type: 'int'},
	        {name: 'center_id', type: 'int'},
	        {name: 'transport_idlodging', type: 'int'},
	        {name: 'transport_idreturnlodging', type: 'int'},
			{name: 'person_idworker', type: 'int'},
			{name: 'province_idlodging', type: 'int'},
			{name: 'province_id', type: 'int'},
			{name: 'person_idlicensedby', type: 'int'},
			{name: 'motive_id', type: 'int'},
			{name: 'lodging_state'},
	        {name: 'lodging_requestreinforceddiet'},
	        {name: 'lodging_requestelongationdiet'},
	        {name: 'lodging_reinforceddiet'},
			{name: 'lodging_elongationdiet'},
			{name: 'lodging_noshow'},
			{name: 'lodging_prorogation'},
			{name: 'hotel_id', type: 'int'},
			{name: 'linearity_id', type: 'int'},
			{name: 'cafeteria_id', type: 'int'},
			{name: 'person_ideditedby', type: 'int'}

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
     * Creamos el formulario de alta/modificacion de request
     */
   var updateForm = new Ext.FormPanel({
        id: 'form-requests',
        region: 'west',
        split: false,
        collapsible: true,
        frame: true,
        labelWidth: 150,
        width: 735,
        minWidth: 730,
        height: 350,
        waitMsgTarget: true,
        monitorValid: true,
        reader: requestsFormReader,
		items: [{
            layout:'column',
            items:[{
                columnWidth:.5,
                layout: 'form',
                items: [
                        new Ext.form.ComboBox({
	           			store: dataStorePersons,
	           			fieldLabel: 'Persona que autoriza',
	           			displayField: 'person_fullname',  //coge esto dataStoreCenter 
	           			valueField: 'person_id',  //coge esto del Formreader que depende de requestRecordUpdate, auqnue el store es del propio de centro de costo para leer el id y mostrar el nombre
	           			hiddenName: 'person_idlicensedby',
	           			allowBlank: false,
	           			disabled: true,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione una persona que autorice...',
	           			selectOnFocus: true,
	           			width: 180,
					    id: 'frm_person_idlicensedby',
			            name : 'person_idlicensedby',
			            listeners: {
							'blur': function(){
								var flag = dataStorePersons.findExact( 'person_id', Ext.getCmp('frm_person_idlicensedby').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_person_idlicensedby').reset();
					    			return false;
					    		}
							},
							'beforeshow': function(){
								dataStorePersons.load();
							}
                
				 		}
                }),	new Ext.form.ComboBox({
	           			store: dataStoreCenter,
	           			fieldLabel: 'Centro de Costo',
	           			displayField: 'center_name',  //coge esto dataStoreCenter 
	           			valueField: 'center_id',  //coge esto del Formreader que depende de requestRecordUpdate, auqnue el store es del propio de centro de costo para leer el id y mostrar el nombre
	           			hiddenName: 'center_id',
	           			allowBlank: false,
	           			disabled: true,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all', 					
	           			emptyText: 'Seleccione un centro de costo...',
	           			selectOnFocus: true,
	           			width: 180,
					    id: 'frm_center_id',
			            name : 'center_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreCenter.findExact( 'center_id', Ext.getCmp('frm_center_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_center_id').reset();
					    			return false;
					    		}
							}
				 		}
                }), {
		            fieldLabel : 'Fecha Entrada',
		            id: 'frm_lodging_entrancedate',
		            name : 'lodging_entrancedate',
					hiddenName: 'lodging_entrancedate',
				   	allowBlank: false,
				   	disabled: true,
				   	invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
		            format: 'Y-m-d',
		            xtype: 'datefield',
		            listeners: {
                        	'beforerender': function(){
								if (session_rollId == 6) {
									Ext.getCmp('frm_lodging_entrancedate').enable();
								}
						}
                    }
		        }, 	{
		            fieldLabel : 'Fecha Salida',
		            id: 'frm_lodging_exitdate',
		            name : 'lodging_exitdate',  //debe coincidir con los campos del requestsRecordUpdate 
					hiddenName: 'lodging_exitdate',
		            allowBlank: false,
		            disabled: true,
		            invalidText: "El formato correcto de la fecha es aaaa/mm/dd. Ejemplo: 2010-01-01",
		            format: 'Y-m-d',
		            xtype: 'datefield',
		            listeners: {
                    	'beforerender': function(){
							if (session_rollId == 6) {
								Ext.getCmp('frm_lodging_exitdate').enable();
								Ext.getCmp('buttonModify').enable();
							}
						}
                	}
		        }, new Ext.form.ComboBox({
	           			store: dataStoreTransport,
	           			fieldLabel: 'Transporte de ida',
	           			displayField: 'transport_name',
	           			valueField: 'transport_id',
	           			hiddenName: 'transport_idlodging',//como se ve igual no tiene que coincidir los nombres, el value field es el name definido en el datarecord arriba y el hidden  el nombre que se ercibe en el input del model a la hora de coger el valor
	           			allowBlank: false,
	           			typeAhead: true,
	           			disabled:true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione un transporte...',
	           			selectOnFocus: true,
	           			width: 180,
					    id: 'frm_transport_idlodging',
			            name : 'transport_idlodging',
			            listeners: {
							'blur': function(){
								var flag = dataStoreTransport.findExact( 'transport_id', Ext.getCmp('frm_transport_idlodging').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_transport_idlodging').reset();
					    			return false;
					    		}
							}
				 		}
				}), new Ext.form.ComboBox({
	           			store: dataStoreTransport,
	           			fieldLabel: 'Transporte de regreso',
	           			displayField: 'transport_name',
	           			valueField: 'transport_id',
	           			hiddenName: 'transport_idreturnlodging',//como se ve igual no tiene que coincidir los nombres, el value field es el name definido en el datarecord arriba y el hidden  el nombre que se ercibe en el input del model a la hora de coger el valor
	           			allowBlank: false,
	           			typeAhead: true,
	           			disabled: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione un transporte...',
	           			selectOnFocus: true,
	           			width: 180,
					    id: 'frm_transport_idreturnlodging',
			            name : 'transport_idreturnlodging',
			            listeners: {
							'blur': function(){
								var flag = dataStoreTransport.findExact( 'transport_id', Ext.getCmp('frm_transport_idreturnlodging').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_transport_idreturnlodging').reset();
					    			return false;
					    		}
							}
				 		}
				}), new Ext.form.ComboBox({
	          			store: dataStoreProv,
	          			fieldLabel: 'Provincia de Hospedaje',
	          			displayField: 'province_name',
	          			valueField: 'province_id',
	          			allowBlank: false,
	          			readOnly: true,
	          			typeAhead: true,
	          			disabled:true,
	          			mode: 'local',
	          			triggerAction: 'all',
	          			emptyText: 'Seleccione una Provincia...',
	          			selectOnFocus: true,
	          			width: 180,
	          			id: 'frm_province_idlodging',
			            name : 'province_idlodging',
			           	hiddenName: 'province_idlodging',
			           	listeners: {
							'select': function(){
										dataStoreHotel.baseParams = {province_id: Ext.getCmp('frm_province_idlodging').getValue()};
										dataStoreHotel.load();
							},
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_idlodging').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_province_idlodging').reset();
					    			return false;
					    		}
							}
				 		}
                }), new Ext.form.ComboBox({
	          			store: dataStoreMotive,
	          			fieldLabel: 'Motivo de solicitud',
	          			displayField: 'motive_name',
	          			valueField: 'motive_id',
	          			allowBlank: false,
	          			disabled:true,
	          			typeAhead: true,
	          			mode: 'local',
	          			triggerAction: 'all',
	          			emptyText: 'Seleccione motivo ...',
	          			selectOnFocus: true,
	          			width: 180,
					    id: 'frm_motive_id',
						hiddenName: 'motive_id',
			            name : 'motive_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreMotive.findExact( 'motive_id', Ext.getCmp('frm_motive_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_motive_id').reset();
					    			return false;
					    		}
							}
				 		}
				}), new Ext.form.ComboBox({
	          			store: dataStoreProv,
	          			fieldLabel: 'Provincia del trabajador',
	          			displayField: 'province_name',  
	          			valueField: 'province_id',
	          			hiddenName: 'province_id',
	          			allowBlank: true,
	          			disabled:true,
	          			typeAhead: true,
	          			mode: 'local',
	          			triggerAction: 'all',
	          			emptyText: 'Seleccione una Provincia...',
	          			selectOnFocus: true,
	          			width: 180,
					    id: 'frm_province_id',
			            name : 'province_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_province_id').reset();
					    			return false;
					    		}
							}
				 		}
				}), new Ext.form.ComboBox({
	          			store: dataStorePersons,
	          			fieldLabel: 'Trabajador',
	          			displayField: 'person_fullname',
	          			valueField: 'person_id',
	          			hiddenName: 'person_idworker',
	          			allowBlank: false,
	          			disabled:true,
	          			typeAhead: true,
	          			mode: 'local',
	          			triggerAction: 'all',
	          			emptyText: 'Seleccione un trabajador...',
	          			selectOnFocus: true,
	          			width: 180,
					    id: 'frm_person_idworker',
			            name : 'person_idworker',
			            listeners: {
							'blur': function(){
								var flag = dataStorePersons.findExact( 'person_id', Ext.getCmp('frm_person_idworker').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_person_idworker').reset();
					    			return false;
					    		}
							}
				 		}
                }), new Ext.form.ComboBox({
	           			store: dataStoreUsers,
	           			fieldLabel: 'Solicitado por',
	           			displayField: 'person_fullname',
	           			valueField: 'person_id',
	           			hiddenName: 'person_idrequestedby',
	           			allowBlank: false,
	           			disabled: true,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',				
	           			emptyText: 'Seleccione la persona ...',
	           			selectOnFocus: true,
	           			width: 180,
					    id: 'frm_person_idrequestedby',
			            name : 'person_idrequestedby',
			            listeners: {
							'blur': function(){
								var flag = dataStoreUsers.findExact( 'person_id', Ext.getCmp('frm_person_idrequestedby').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_person_idrequestedby').reset();
					    			return false;
					    		}
							}
				 		}
                }),	{
		            id: 'frm_request_id',
		            name : 'request_id',
					hiddenName: 'request_id',
		            xtype: 'hidden'
		        },	{
		            id: 'frm_request_details',
		            name : 'request_details',
					hiddenName: 'request_details',
		            xtype: 'hidden'
		        }]
            },{
                columnWidth:.5,
                layout: 'form',
                items: [{
                	xtype: 'checkbox',
	                id: 'frm_lodging_requestreinforceddiet',
	                name: 'lodging_requestreinforceddiet',
					hiddenName: 'lodging_requestreinforceddiet',
					fieldLabel: 'Solicita Dieta reforzada',
					checked:'lodging_requestreinforceddiet'
				}, 	{
                	xtype: 'checkbox',
	                id: 'frm_lodging_reinforceddiet',
	                name: 'lodging_reinforceddiet',
					hiddenName: 'lodging_reinforceddiet',
					fieldLabel: 'Se autoriza Dieta reforzada',
					checked:'lodging_reinforceddiet'
				}, 	{
                	xtype: 'checkbox',
	                id: 'frm_lodging_requestelongationdiet',
	                name: 'lodging_requestelongationdiet',
					hiddenName: 'lodging_requestelongationdiet',
					fieldLabel: 'Solicita alargamiento de dieta',
					checked:'lodging_requestelongationdiet'
				}, 	{
                	xtype: 'checkbox',
	                id: 'frm_lodging_elongationdiet',
	                name: 'lodging_elongationdiet',
					hiddenName: 'lodging_elongationdiet',
					fieldLabel: 'Se autoriza Alargamiento de Dieta',
					checked:'lodging_elongationdiet'
				},	{
                	xtype: 'checkbox',
	                id: 'frm_lodging_noshow',
	                name: 'lodging_noshow',
					hiddenName: 'lodging_noshow',
					fieldLabel: 'No Show',
					checked:'lodging_noshow'
				}, 	{
                	xtype: 'checkbox',
	                id: 'frm_lodging_prorogation',
	                name: 'lodging_prorogation',
					hiddenName: 'lodging_prorogation',
					fieldLabel: 'Pr&oacute;rroga',
					checked:'lodging_prorogation',
					listeners: {
						'check' : function() {
							var check = Ext.getCmp('frm_lodging_prorogation').getValue();
					       	if (check == true){
						      	Ext.getCmp('frm_lodging_prorogationdate').enable();
						      	Ext.getCmp('frm_center_id').enable();
							}else{
						      	Ext.getCmp('frm_lodging_prorogationdate').disable();
						      	Ext.getCmp('frm_center_id').disable();
							}
					  	}
					}
				}, {
		            fieldLabel : 'Fecha de Pr&oacute;rroga',
		            id: 'frm_lodging_prorogationdate',
		            name : 'lodging_prorogationdate', 
					hiddenName: 'lodging_prorogationdate',
		            allowBlank:true,
		            disabled:true,
					vtype: 'daterange',
					invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
		            format: 'Y-m-d',
					startDateField:'frm_lodging_entrancedate',
		            xtype: 'datefield'
		        }, new Ext.form.ComboBox({
	           			store: dataStoreHotel,
	           			fieldLabel: 'Hotel',
	           			displayField: 'hotel_name',
	           			valueField: 'hotel_id',
	           			hiddenName: 'hotel_id',
	           			allowBlank: false,
	           			formBind: true,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all', 					
	           			emptyText: 'Seleccione un Hotel...',
	           			selectOnFocus: true,
	           			width: 180,
	           			id: 'frm_hotel_id',
			            name : 'hotel_id',
			           	listeners: {
							'beforequery': function(){
										dataStoreHotel.baseParams = {province_id: Ext.getCmp('frm_province_idlodging').getValue()};
										dataStoreHotel.load();
							},
							'select': function(){
										dataStoreLinearity.baseParams = {hotel_id: Ext.getCmp('frm_hotel_id').getValue()};
										dataStoreLinearity.load();
							},
							'blur': function(){
								var flag = dataStoreHotel.findExact( 'hotel_id', Ext.getCmp('frm_hotel_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_hotel_id').reset();
					    			return false;
					    		}
							}
							
				 		}
			            
                }), new Ext.form.ComboBox({
	           			store: dataStoreLinearity,
	           			fieldLabel: 'Linealidad',
	           			displayField: 'linearity_name',
	           			valueField: 'linearity_id',
	           			hiddenName: 'linearity_id',
	           			allowBlank: true,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all', 					
	           			emptyText: 'Seleccione Linealidad...',
	           			selectOnFocus: true,
	           			width: 180,
					    id: 'frm_linearity_id',
			            name : 'frm_linearity_id',
			            listeners: {
							'blur': function(){
								var flag = dataStoreLinearity.findExact( 'linearity_id', Ext.getCmp('frm_linearity_id').getValue());
					    		if (flag == -1){
					    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
					    			Ext.getCmp('frm_linearity_id').reset();
					    			return false;
					    		}
							}
				 		}
                }), new Ext.form.ComboBox({
           			store: dataStoreCafeteria,
           			fieldLabel: 'Cafeter&iacute;a',
           			displayField: 'cafeteria_name',
           			valueField: 'cafeteria_id',
           			hiddenName: 'cafeteria_id',
           			allowBlank: true,
           			typeAhead: true,
           			mode: 'local',
           			triggerAction: 'all', 					
           			emptyText: 'Seleccione una Cafeteria...',
           			selectOnFocus: true,
           			width: 180,
           			id: 'frm_cafeteria_id',
		            name : 'cafeteria_id',
		           	listeners: {
						'beforequery': function(){
									dataStoreCafeteria.baseParams = {province_id: Ext.getCmp('frm_province_idlodging').getValue()};
									dataStoreCafeteria.load();
						},
						'blur': function(){
							var flag = dataStoreCafeteria.findExact( 'cafeteria_id', Ext.getCmp('frm_cafeteria_id').getValue());
				    		if (flag == -1){
				    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
				    			Ext.getCmp('frm_cafeteria_id').reset();
				    			return false;
				    		}
						}
						
			 		}
		            
            })]
            }]
        }]
    });
   
   	updateForm.addButton({
       text : 'Modificar Fecha',
       disabled : true,
       id : 'buttonModify',
       handler : function() {
	   		updateForm.getForm().submit({
	   		url : baseUrl+'index.php/lodging/lodging_edit/updateDate',
	   		waitMsg : 'Salvando datos...',
		   		failure: function (form, action) {
		   			if(action.failureType == 'server'){ 
	                    obj = Ext.util.JSON.decode(action.response.responseText); 
	                    Ext.Msg.alert('Fall&oacute; el registro!', obj.errors.reason); 
		   			}
		   			dataStoreLodgingEdit.load({params: {start:0,limit:50}});
		   			sm2.clearSelections();
		   		},
	           	success: function (form, request) {
	               Ext.MessageBox.show({
	                   title: 'Datos salvados correctamente',
	                   msg: 'Datos salvados correctamente',
	                   width: 300,
	                   buttons: Ext.MessageBox.OK,
	                   icon: Ext.MessageBox.INFO
	               });
	               updateForm.getForm().reset();
	               updateWindow.destroy();
	               dataStoreLodgingEdit.load({params: {start:0,limit:50}});
	               sm2.clearSelections();
					
	           	}
			
	   		});
       }
   	});
 	
	    /*
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    updateForm.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
				var lodging = Ext.getCmp('frm_lodging_exitdate').getValue();
				var exitdate = lodging.format(Date.patterns.ISO8601Short);
				var noShow = Ext.getCmp('frm_lodging_noshow').getValue();
		   		if (today > exitdate && session_rollId < 6){
					if(!noShow){
                        Ext.MessageBox.alert('Error', 'No se puede editar este hospedaje porque la fecha de salida ya expir&oacute;.');
                        return false;
                    }
	       		}
			    enableCmp();
	            updateForm.getForm().submit({
	                url : baseUrl+'index.php/lodging/lodging_edit/insert',
	                waitMsg : 'Salvando datos...',
	                failure: function (form, action) {
	                	disableCmp();
	                	if(action.failureType == 'server'){ 
                             obj = Ext.util.JSON.decode(action.response.responseText); 
                             Ext.Msg.alert('Fall&oacute; el registro!', obj.errors.reason); 
                        }
	                	dataStoreLodgingEdit.load({params: {start:0,limit:50}});
						sm2.clearSelections();
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
	                    dataStoreLodgingEdit.load({params: {start:0,limit:50}});
						sm2.clearSelections();
						
	                }
					
	            });
				
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
	    
	    /*updateForm.addButton({
	        text : 'Voucher Especial',
	        disabled : false,
	        handler : function() {
	    		voucher_ventana();
	        }
	    });*/	
   
    	var updateWindow;
		if (id > 0){
			updateForm.load({url:baseUrl+'index.php/lodging/lodging_edit/getById/'+id});
		}
		
		if(! updateWindow){

				updateWindow = new Ext.Window({
				title: 'Editar Solicitud de Hospedaje',
				layout:'form',
				top: 200,
				width:760,
				height:390,
				resizable : false,
				modal: true,
				bodyStyle:'padding:5px;',
				items: updateForm
				
				});
			}
		updateWindow.show(this);

	}
	
	function actualizar(fecha) {
		var milisegundos = parseInt(1 * 24 * 60 * 60 * 1000);
		var tmp = Date.parse(fecha);
		var date = new Date(tmp);
		date.setDate(date.getDate() + 1);
		return date;
	}
	
	/*function voucher_ventana(){
	    var lodging_entrance = Ext.getCmp('frm_lodging_entrancedate').getValue();
   		var lodging_exitdate = Ext.getCmp('frm_lodging_exitdate').getValue();
	    var length = (Math.round((lodging_exitdate - lodging_entrance)/(24*60*60*1000))*1) + 1;
	    var entrance = lodging_entrance.dateFormat('Y-m-d');
	    var counter = 0;
	    var simple = new Ext.FormPanel({
	        autoScroll: true,
	        width: 170,
	        labelWidth: 10,
			height:350,
	        layout: 'form',
	        items: []
	    });
	    entrance = actualizar(entrance);
		for ( var i = 0; i < length; i++) {
			counter = i + 1;
		    simple.add(new Ext.form.Checkbox({
            		boxLabel: entrance.dateFormat('Y-m-d'),
            		name: counter
        		})
		    );
		    entrance = actualizar(entrance);
		}
	    
   		simple.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
				/*var lodging = Ext.getCmp('frm_lodging_exitdate').getValue();
				var exitdate = lodging.format(Date.patterns.ISO8601Short);
		   		if (today > exitdate){
					Ext.MessageBox.alert('Error', 'No se puede editar este hospedaje porque la fecha de salida ya expir&oacute;.');
					return false;
	       		}
			    enableCmp();*/
				/*
			    simple.getForm().submit({
	                url : baseUrl+'index.php/lodging/lodging_edit/insert',
	                waitMsg : 'Salvando datos...',
	                failure: function (form, action) {
	                },
	                success: function (form, request) {
	                    Ext.MessageBox.show({
	                        title: 'Datos salvados correctamente',
	                        msg: 'Datos salvados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
	                }
					
	            });
				
	        }
	    });
	    

	    simple.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        handler : function() {
	        }
	    });	

    	var voucherWindow;
	
		if(! voucherWindow){

				voucherWindow = new Ext.Window({
				title: 'Voucher para Hospedaje',
				layout:'form',
				top: 200,
				width: 200,
				height:390,
				resizable : false,
				modal: true,
				bodyStyle:'padding:5px;',
				items: simple  //adicionamos la forma dentro de la ventana
				
				});
			}
		voucherWindow.show(this);

	}*/
	 
});

    function enableCmp() {
    	Ext.getCmp('frm_person_idlicensedby').enable();
    	Ext.getCmp('frm_center_id').enable();
    	Ext.getCmp('frm_lodging_entrancedate').enable();
    	Ext.getCmp('frm_lodging_exitdate').enable();
    	Ext.getCmp('frm_transport_idlodging').enable();
    	Ext.getCmp('frm_transport_idreturnlodging').enable();
    	Ext.getCmp('frm_motive_id').enable();
    	Ext.getCmp('frm_province_id').enable();
    	Ext.getCmp('frm_person_idworker').enable();
    	Ext.getCmp('frm_person_idrequestedby').enable();
    	Ext.getCmp('frm_province_idlodging').enable();
    }

    function disableCmp() {
    	Ext.getCmp('frm_person_idlicensedby').disable();
    	Ext.getCmp('frm_center_id').disable();
    	Ext.getCmp('frm_lodging_entrancedate').disable();
    	Ext.getCmp('frm_lodging_exitdate').disable();
    	Ext.getCmp('frm_transport_idlodging').disable();
    	Ext.getCmp('frm_transport_idreturnlodging').disable();
    	Ext.getCmp('frm_motive_id').disable();
    	Ext.getCmp('frm_province_id').disable();
    	Ext.getCmp('frm_person_idworker').disable();
    	Ext.getCmp('frm_person_idrequestedby').disable();
    	Ext.getCmp('frm_province_idlodging').disable();

    }
///////fin del onReady
    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/lodging/lodging_edit/canceled/'+array[i].get('request_id'),
				   method: 'POST',
				   disableCaching: false,
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo cancelar la solicitud.');
				   }
				});//cierro Ext.Ajax.request
		    }//cierro el for
			dataStoreLodgingEdit.load({params: {start:0,limit:50}});
			sm2.clearSelections();
	    }//cierro el if
    }//cierro la funcion
    
    function getRequestId(arr) {
		//var arrayId = new Array();
		var chain = '';
		for (var i = 0, len = arr.length; i < len; i++) {
			if (i > 0){
				chain = chain + '-' + arr[i].get('request_id');
			} else {
				chain = arr[i].get('request_id');
			}
		}
        Ext.Ajax.request({
		   url: baseUrl+'index.php/lodging/lodging_edit/hotel_letter/'+chain,
		   method: 'POST',
		   disableCaching: false,
		   failure: function(){
		   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la solicitud.');
		   }
		});
		sm2.clearSelections();
    }
 
