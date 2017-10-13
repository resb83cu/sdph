var conciliationsDataStore;
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


Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';
	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Conciliations');
    

   	var xg = Ext.grid;
   	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Conciliations.conciliationsGrid.conciliationButton.enable();
                } else {
                    Conciliations.conciliationsGrid.conciliationButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Hospedaje -> Conciliar Hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    
    /*
     * Definimos el registro
     */
     
    Conciliations.conciliationsRecord = new Ext.data.Record.create([
        {name: 'request_id'},
        {name: 'conciliation_id'},
        {name: 'request_date'},
        {name: 'person_licensedby'},
        {name: 'lodging_entrancedate'},
        {name: 'lodging_exitdate'},
        {name: 'bill_number'},
        {name: 'center_name'},
		{name: 'person_worker'},
		{name: 'person_identity'},
		{name: 'request_details', type: 'string'},
		{name: 'province_lodging'},
		{name: 'hotel_name'},
		{name: 'lodging_amount'},
		{name: 'diet_amount'},
		{name: 'letter_id'}
		
    ]);


    /*
     * Creamos el reader para el Grid
     */
    Conciliations.conciliationsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'request_id'},
        Conciliations.conciliationsRecord
    );
    
    var expander = new Ext.ux.grid.RowExpander({
        tpl : new Ext.Template(
            '<p><b>Detalle:</b> {request_details}</p>'
        )
    });

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Conciliations.conciliationsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/lodging/lodging_conciliations/setDataGrid',
        method: 'POST'
    });

    conciliationsDataStore = new Ext.data.Store({
        id: 'conciliationsDS',
        proxy: Conciliations.conciliationsDataProxy,
        reader: Conciliations.conciliationsGridReader
    });

    /*
     * Creamos el modelo de columnas para el grid
     */
    Conciliations.conciliationsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       	expander,
       {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },	{
            id: 'conciliation_id',
            name : 'conciliation_id',
            dataIndex: 'conciliation_id',
            hidden: true
        },	{
            id: 'bill_number',
            name : 'bill_number',
            header: 'No. Factura',
            dataIndex: 'bill_number',
            width: 70
        },	{
            id: 'letter_id',
            name : 'letter_id',
            header: 'No. Carta',
            dataIndex: 'letter_id',
            width: 70
        },	{
	   		id: 'person_identity',
            name: 'person_identity',
			header: "CI",
			width: 90,
			dataIndex: 'person_identity',
			sortable: true
		},	{
	   		id: 'person_worker',
            name: 'person_worker',
			header: "Trabajador",
			width: 180,
			dataIndex: 'person_worker',
			sortable: true
		},	{
            id: 'center_name',
            name : 'center_name',
            header: "Centro de Costo",
            width: 130,
            dataIndex: 'center_name',
            sortable: true
        },	{
            id: 'person_licensedby',
            name : 'person_licensedby',
            header: "Autoriza",
            width: 130,
            dataIndex: 'person_licensedby',
            sortable: true
        },	{
	   		id: 'diet_amount',
            name: 'diet_amount',
			header: "Dieta",
			width: 80,
			renderer: 'usMoney',
			dataIndex: 'diet_amount',
			sortable: true
		},	{
	   		id: 'lodging_amount',
            name: 'lodging_amount',
			header: "Hospedaje",
			width: 80,
			renderer: 'usMoney',
			dataIndex: 'lodging_amount',
			sortable: true
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
        }]
    );

    /*
     * Creamos el grid
     */
    Conciliations.conciliationsGrid = new xg.GridPanel({
        id : 'ctr-conciliations-grid',
        store : conciliationsDataStore,
        cm : Conciliations.conciliationsColumnMode,
        viewConfig: {
            forceFit:false
        },
        frame:true,
        stripeRows: true,
        collapsible: true,
        width : 750,
        height : 380,
        plugins: expander,
        tbar:[{text:'Conciliar',
	            tooltip:'Conciliar pasaje(s) seleccionado(s)',
	            iconCls:'add',
	            ref: '../conciliationButton',
	            disabled: true,
	            handler: function(){
		            	    array = sm2.getSelections();
							for (var i = 0, len = array.length; i < len; i++) {
						        Ext.Ajax.request({
								   url: baseUrl+'index.php/lodging/lodging_conciliations/insertBill/'+array[i].get('request_id')+'/'+Ext.getCmp('bill_number').getValue(),
								   method: 'GET',
								   disableCaching: false,
								   success: function(){
								   },
								   failure: function(){
								   		Ext.MessageBox.alert('Error', 'No se pudo conciliar el pasaje.');
								   }
								});
						    }
						    Ext.getCmp('bill_number').setValue('');
						    sm2.clearSelections();
				           	conciliationsDataStore.load({params: {start:0,limit:15}});
			            }
				},'-',{
				    xtype: 'textfield',
				    name: 'bill_number',
				    id: 'bill_number',
				    width: 80,
				    emptyText: 'No. de factura'
				},'-',{
		            text:'Exportar a pdf',
		            tooltip:'Exportar a pdf',
		            iconCls:'pdf',
		            ref: '../pdfButton',
		            //disabled: true,
		            handler: function(){
								var bill = Ext.getCmp('bill_number_pdf').getValue();
                                                                var htl = Ext.getCmp('filter_hotel_id').getValue();
								if (bill == ""){
									Ext.MessageBox.alert('Error', 'Debe introducir un n&uacute;mero de factura para exportar.');
									sm2.clearSelections();
									return false;
								} else {
									Conciliations.filterForm.getForm().getEl().dom.action = baseUrl+'index.php/lodging/lodging_conciliations/billPdf/'+bill+'/'+htl;
									Conciliations.filterForm.getForm().getEl().dom.method = 'POST';
									Conciliations.filterForm.getForm().submit();							
								}
		            }
		        },'-',{
				    xtype: 'textfield',
				    name: 'bill_numberPdf',
				    id: 'bill_number_pdf',
				    name: 'bill_number_pdf',
				    width: 120,
				    emptyText: 'Factura a exportar'
				}],
			bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: conciliationsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

	Conciliations.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 140,
        height: 110,
        width: 750,
        items: [{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items:[	new Ext.form.ComboBox({
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
		          			width: 180,
						    id: 'filter_province_id',
				            name : 'filter_province_id',
				            listeners: {
								'select': function(){
											dataStoreHotel.baseParams = {province_id: Ext.getCmp('filter_province_id').getValue()};
											dataStoreHotel.load();
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
		      			}), new Ext.form.ComboBox({
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
		      			})
		             ]
		    },{
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
				            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
				            format: 'Y-m-d',
				            endDateField: 'enddt'
				        },{
				            xtype: 'datefield',
				            width: 180,
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
            }]
        }]
    });

	Conciliations.filterForm.addButton({
       text : 'Limpiar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
           Conciliations.filterForm.getForm().reset();
           conciliationsDataStore.baseParams = {
               dateStart: '',
               dateEnd: '',
               hotel: 0,
               province: 0
           };
           sm2.clearSelections();
           conciliationsDataStore.load({params: {start:0,limit:15}});
       }
   });

   /*
    * Anadimos el boton para filtrar
    */
   	Conciliations.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var startDate = Conciliations.filterForm.findById('startdt').getValue();
           	var endDate = Conciliations.filterForm.findById('enddt').getValue();
			var province = Conciliations.filterForm.findById('filter_province_id').getValue();
			var hotel = Conciliations.filterForm.findById('filter_hotel_id').getValue();
           	conciliationsDataStore.baseParams = {
				dateStart: startDate.dateFormat('Y-m-d'),
				dateEnd: endDate.dateFormat('Y-m-d'),
				hotel: hotel,
				province: province
           	};
           	conciliationsDataStore.load({params: {start:0,limit:15}});
       	}
   	});    
   	
   	Conciliations.conciliationsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = conciliationsDataStore.getAt(row).data.request_id;
        update_ventana(selectedId);
    });

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Conciliations.filterForm.render(Ext.get('conciliation_grid'));
	Conciliations.conciliationsGrid.render(Ext.get('conciliation_grid'));
	
	function update_ventana(id){
		
	    conciliationRecordUpdate = new Ext.data.Record.create([
			{name: 'request_id'},
			{name: 'conciliation_id'},
			{name: 'person_licensedby'},
			{name: 'lodging_entrancedate'},
			{name: 'lodging_exitdate'},
			{name: 'center_name'},
			{name: 'person_worker'},
			{name: 'person_identity'},
			{name: 'lodging_amount'},
			{name: 'diet_amount'}
	    ]);
		    
			/*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
	   requestsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'request_id'     //para el modificar pasarlo 
	        },conciliationRecordUpdate /*ojo ver bien campos y nombres del requestrecordUpdate, este es para el formulario, no para el grid*/
	    );
			
	 	/*
	     * Creamos el formulario de alta/modificaci�n de request
	     */
	   var updateForm = new Ext.FormPanel({
	        id: 'form-requests',
	        region: 'west',
	        split: false,
	        collapsible: true,
	        frame: true,
	        labelWidth: 150,
	        width: 400,
	        minWidth: 400,
	        height: 270,
	        waitMsgTarget: true,
	        monitorValid: true,
	        reader: requestsFormReader,   //ver referencia de nombres en el requestFormReader y a su vez este depende de requestRecordUpdate
			items: [{
			            fieldLabel : 'CI',
			            id: 'upd_person_identity',
			            name : 'person_identity',
			            readOnly: true,
			            disabled: true,
			            width: 100,
			            xtype: 'textfield'
			        },	{
			            fieldLabel : 'Nombre y Apellidos',
			            id: 'upd_person_worker',
			            name : 'person_worker',
			            readOnly: true,
			            disabled: true,
			            width: 220,
			            xtype: 'textfield'
			        },	{
			            fieldLabel : 'Autorizado por',
			            id: 'upd_person_licensedby',
			            name : 'person_licensedby',
			            readOnly: true,
			            disabled: true,
			            width: 220,
			            xtype: 'textfield'
			        },	{
			            fieldLabel : 'Centro de Costo',
			            id: 'upd_center_name',
			            name : 'center_name',
			            readOnly: true,
			            disabled: true,
			            width: 220,
			            xtype: 'textfield'
			        },	{
			            fieldLabel : 'Fecha Entrada',
			            id: 'upd_lodging_entrancedate',
			            name : 'lodging_entrancedate', 
						hiddenName: 'lodging_entrancedate',
					   	allowBlank: false,
					   	width: 100,
					   	invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
			            format: 'Y-m-d',
			            xtype: 'datefield'
			        }, {
			            fieldLabel : 'Fecha Salida',
			            id: 'upd_lodging_exitdate',
			            name : 'lodging_exitdate',  //debe coincidir con los campos del requestsRecordUpdate 
						hiddenName: 'lodging_exitdate',
			            allowBlank: false,
			            width: 100,
			            invalidText: "El formato correcto de la fecha es aaaa/mm/dd. Ejemplo: 2010-01-01",
			            format: 'Y-m-d',
			            xtype: 'datefield'
			        }, 	{
			            fieldLabel : 'Importe de Dieta',
			            id: 'upd_diet_amount',
			            name : 'diet_amount',
			            allowNegative: false,
			            width: 100,
			            xtype: 'numberfield'
			        },	{
			            fieldLabel : 'Importe de Hospedaje',
			            id: 'upd_lodging_amount',
			            name : 'lodging_amount',
			            allowNegative: false,
			            width: 100,
			            xtype: 'numberfield'
			        },	{
			            id: 'upd_request_id',
			            name : 'request_id',
						hiddenName: 'request_id',
			            xtype: 'hidden'
			        },	{
			            id: 'upd_conciliation_id',
			            name : 'conciliation_id',
						hiddenName: 'conciliation_id',
			            xtype: 'hidden'
			        }
			]
	    });
	 	
		    /*
		     * A�adimos el bot�n para guardar los datos del formulario
		     */
		    updateForm.addButton({
		        text : 'Guardar',
		        disabled : false,
		        formBind: true,
		        handler : function() {
					/*var lodging = Ext.getCmp('upd_lodging_exitdate').getValue();
					var exitdate = lodging.format(Date.patterns.ISO8601Short);
			   		if (today > exitdate){
						Ext.MessageBox.alert('Error', 'No se puede editar este hospedaje porque la fecha de salida ya expir&oacute;.');
						return false;
		       		}*/
				    //enableCmp();
		            updateForm.getForm().submit({
		                url : baseUrl+'index.php/lodging/lodging_conciliations/insert',
		                waitMsg : 'Salvando datos...',
		                failure: function (form, action) {
		                	if(action.failureType == 'server'){ 
	                             obj = Ext.util.JSON.decode(action.response.responseText); 
	                             Ext.Msg.alert('Fall&oacute; el registro!', obj.errors.reason); 
	                        }
		                	conciliationsDataStore.load();
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
		                    conciliationsDataStore.load();
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
		
	   
	    	var updateWindow;
			if (id > 0){
				updateForm.load({url:baseUrl+'index.php/lodging/lodging_conciliations/getById/'+id});
			}
			
			if(! updateWindow){

					updateWindow = new Ext.Window({
					title: 'Editar Solicitud de Hospedaje',
					layout:'form',
					top: 200,
					width: 425,
					height:310,
					resizable : false,
					modal: true,
					bodyStyle:'padding:5px;',
					items: updateForm  //adicionamos la forma dentro de la ventana
					
					});
				}
			updateWindow.show(this);

		}
		 
});