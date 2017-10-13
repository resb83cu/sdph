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
        title: 'Hospedaje -> Conciliar -> Hospedaje',
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
        {name: 'request_date'},
        {name: 'lodging_entrancedate'},
        {name: 'lodging_exitdate'},
        {name: 'bill_number'},
        {name: 'center_name'},
		{name: 'person_worker'},
		{name: 'person_nameeditedby'},
		{name: 'hotel_name'} 
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

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Conciliations.conciliationsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/lodging/lodging_lodgingconciliations/setDataGrid',
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
       {
            id: 'request_id',
            name : 'request_id',
            dataIndex: 'request_id',
            hidden: true
        },{
            id: 'bill_number',
            name : 'bill_number',
            header: 'No. Factura',
            dataIndex: 'bill_number',
            width: 70
            //hidden: true
        },{
	   		id: 'person_worker',
            name: 'person_worker',
			header: "Trabajador",
			width: 130,
			dataIndex: 'person_worker',
			sortable: true
		},{
            id: 'center_name',
            name : 'center_name',
            header: "Centro de Costo",
            width: 130,
            dataIndex: 'center_name',
            sortable: true
        },{
	   		id: 'person_nameeditedby',
            name: 'person_nameeditedby',
			header: "Editado por",
			width: 130,
			dataIndex: 'person_nameeditedby',
			sortable: true
		},{
	   		id: 'hotel_name',
            name: 'hotel_name',
			header: "Hotel",
			width: 130,
			dataIndex: 'hotel_name',
			sortable: true
		},{
	   		id: 'lodging_entrancedate',
            name: 'lodging_entrancedate',
            header: 'Entrada',
            width: 80,
            dataIndex: 'lodging_entrancedate',
            sortable: true
        },{
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
        tbar:[{text:'Conciliar',
	            tooltip:'Conciliar pasaje(s) seleccionado(s)',
	            iconCls:'add',
	            ref: '../conciliationButton',
	            disabled: true,
	            handler: function(){
		            	    array = sm2.getSelections();
							for (var i = 0, len = array.length; i < len; i++) {
						        Ext.Ajax.request({
								   url: baseUrl+'index.php/lodging/lodging_lodgingconciliations/insert/'+array[i].get('request_id')+'/'+Ext.getCmp('bill_number').getValue(),
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
						    /*var startDate = Conciliations.filterForm.findById('startdt').getValue();
				           	var endDate = Conciliations.filterForm.findById('enddt').getValue();
							var province = Conciliations.filterForm.findById('filter_province_id').getValue();
							var hotel = Conciliations.filterForm.findById('filter_hotel_id').getValue();
				           	conciliationsDataStore.baseParams = {
								dateStart: startDate.dateFormat('Y-m-d'),
								dateEnd: endDate.dateFormat('Y-m-d'),
								hotel: hotel,
								province: province
								//motive: motive
				           	};*/
				           	conciliationsDataStore.load({params: {start:0,limit:15}});
			            }
				}, {
				    xtype: 'textfield',
				    name: 'bill_number',
				    id: 'bill_number',
				    emptyText: 'No. de factura'
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
               //motive: 0
           };
           sm2.clearSelections();
           conciliationsDataStore.load({params: {start:0,limit:15}});
       }
   });

   /*
    * A�adimos el bot�n para filtrar
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
				//motive: motive
           	};
           	conciliationsDataStore.load({params: {start:0,limit:15}});
       	}
   	});    

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	Conciliations.filterForm.render(Ext.get('conciliation_grid'));
	Conciliations.conciliationsGrid.render(Ext.get('conciliation_grid'));
    conciliationsDataStore.load({params: {start:0,limit:15}});
});