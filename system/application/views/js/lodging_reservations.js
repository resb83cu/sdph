var dataRecordHotel = new Ext.data.Record.create([
						{name:'hotel_id'},
						{name:'hotel_name'}
					]);

var dataReaderHotel = new Ext.data.JsonReader({root:'data'},dataRecordHotel);

var dataProxyHotel = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_hotels/setDataGrid',
						method: 'POST'
					});

var dataStoreHotel = new Ext.data.Store({
						proxy: dataProxyHotel,
						reader: dataReaderHotel,
						autoLoad:true
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
						url:baseUrl+'index.php/person/person_persons/setDataGrid/',   //ver que este metodo devuelve el name con la concatenacion name+lastname+secondlastname y ademas se le esta pasando por el post parametros por algun baseparams
						method: 'POST'
					});

var dataStorePersons= new Ext.data.Store({
						proxy: dataProxyPersons,
						reader: dataReaderPersons,
						autoLoad:true
						});

var dataStoreLodgingReservation, sm2; //auqnue sus propiedades van dentro del onReady



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
    Ext.namespace('Lodgings');
   
   	var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({//es independiente del namespace
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Lodgings.lodgingGrid.removeButton.enable();
                } else {
                    Lodgings.lodgingGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Hospedaje -> Reserva Hospedaje',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
     /*datos que recibira el grid del getdata del model que es llamado por el setdatagrid del controlador, tienen que coicidir estos name
	 con el nombre de los campos que se usan en el arreglo  devuelto pro el getdata, ojo no tienen porque coincidir todos con el nombre de los campos en la base de datos, ejemplo un id de tipo int que lo que paso en el arreglo es el nombre full tipo string,
	 cuando se ponga type debajo es una sugerencia al grid, ejemplo los datos date no se pone el tipo
 */
 //campos como estan devueltos en el arreglo de getdata del model
    Lodgings.dataRecordLodgingReservation= new Ext.data.Record.create([
        {name: 'reservation_id'},
        {name: 'reservation_number'},
        {name: 'reservation_rooms'},
        {name: 'reservation_persons'},
        {name: 'reservation_requestdate'},
        {name: 'reservation_begindate'},/*aqui un ejemplo de no coincidencia con el campode  la based e datos*/
        {name: 'reservation_enddate'},/*otro ejemplo, en vez de poner el id del center pomngo el nom,bre*/
        {name: 'person_fullname'},
		{name: 'hotel_name'} 
    ]);
/*
     * Creamos el reader para el Grid de 
     */
    Lodgings.dataReaderLodgingReservation = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',   //tiene que ser con el setdatagrid el json que manda ademas de los datos el count
        id: 'reservation_id'},/*creo que para los checkbox*/
        Lodgings.dataRecordLodgingReservation 
    );


    Lodgings.dataProxyLodgingReservation = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/lodging/lodging_reservations/setDataGrid',
        method: 'POST'
    });
   
	dataStoreLodgingReservation= new Ext.data.Store({
						id: 'requestDS',
						proxy: Lodgings.dataProxyLodgingReservation,
						reader: Lodgings.dataReaderLodgingReservation
						});

	/*
     * Creamos el modelo de columnas para el grid (conciden campos de dataindex con los del metodo reservationsRecord para ponerlos en las columnas correspondientes en las filas)
     */
	Lodgings.lodgingReservationColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'reservation_id',
            name : 'reservation_id',
            dataIndex: 'reservation_id',
            hidden: true
        },{
            id: 'reservation_rooms',
            name : 'reservation_rooms',
            header: "Rooms",
            width: 50,
            dataIndex: 'reservation_rooms',
            sortable: true
        },{
	   		id: 'reservation_persons',
            name: 'reservation_persons',
			header: "Personas",
			width: 60,
			dataIndex: 'reservation_persons',
			sortable: true
		},{
	   		id: 'reservation_requestdate',
            name: 'reservation_requestdate',
            header: 'Fecha de Solicitud',
			format: 'dd-mm-YYYY',
            width: 100,
            dataIndex: 'reservation_requestdate',
            sortable: true
        },{
	   		id: 'reservation_begindate',
            name: 'reservation_begindate',
            header: 'Entrada',
            width: 70,
            dataIndex: 'reservation_begindate',
            sortable: true
        },{
            id: 'reservation_enddate',
            name : 'reservation_enddate',
            header: 'Salida',
			width: 70,
            dataIndex: 'reservation_enddate',
            sortable: false
        },{
	   		id: 'reservation_number',
            name: 'reservation_number',
			header: "N&uacute;mero",
			width: 70,
			dataIndex: 'reservation_number',
			sortable: true
		},{
	   		id: 'hotel_name',
            name: 'hotel_name',
			header: "Hotel",
			width: 130,
			dataIndex: 'hotel_name',
			sortable: true
		},{
	   		id: 'person_fullname',
            name: 'person_fullname',
			header: "Persona",
			width: 140,
			dataIndex: 'person_fullname',
			sortable: true
		}
		
		
		]
    );

//para el update al dar doble click en una fila del grid



    /*
     * Creamos el grid 
     */
    Lodgings.lodgingGrid = new xg.GridPanel({
        id : 'ctr-reservations-grid',
		
        store : dataStoreLodgingReservation,
		
        cm : Lodgings.lodgingReservationColumnMode,
		
        viewConfig: {
            forceFit:false
        },
        columnLines: true,
        //enableColLock : false,
        frame:true,
        //plugins: expander,
        collapsible: true,
        width : 750,
        height : 500,
        tbar:[{
            text:'Agregar',
            tooltip:'Agregar Reservaci&oacute;n',
            iconCls:'add',
            handler: function(){
                update_ventana();
            }
        },'-',{
            text:'Eliminar',
            tooltip:'Eliminar la(s) Reservacion(es) de Hospedaje Seleccionada(s)',
            iconCls:'del',
            ref: '../removeButton',
            disabled: true,//por defecto false porque no habra nada seleccionado
            handler: function(){
            	    array = sm2.getSelections();
				    if (array.length > 0) {
				        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) Reservacion(es)?', delRecords);
				    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: dataStoreLodgingReservation,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

//





    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
	
    Lodgings.lodgingGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = dataStoreLodgingReservation.getAt(row).data.reservation_id;
		
        update_ventana(selectedId);//pasandole el id  que indica una modificacion
    });

	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
	dataStoreLodgingReservation.load({params: {start:0,limit:15}});  //parametros que se le pasan al controlador y metodo setDatagrid
	
	Lodgings.lodgingGrid.render(Ext.get('requests_grid'));
	function update_ventana(id){
	
    lodgingReservationRecordUpdate = new Ext.data.Record.create([
        {name: 'reservation_id'},
        {name: 'reservation_number'},
        {name: 'reservation_rooms'},
        {name: 'reservation_persons'},
        {name: 'reservation_requestdate'},
        {name: 'reservation_begindate'},
        {name: 'reservation_enddate'},
        {name: 'person_id'},
		{name: 'hotel_id'},
		{name: 'province_idlodging'}
    ]);
	    
		/*
     * Creamos el reader para el formulario de alta/modificaci�n
     */
   reservationsFormReader = new Ext.data.JsonReader({
        root : 'data',
        successProperty : 'success',
        totalProperty: 'count',
        id: 'reservation_id'     //para el modificar pasarlo 
        },lodgingReservationRecordUpdate /*ojo ver bien campos y nombres del requestrecordUpdate, este es para el formulario, no para el grid*/
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
        labelWidth: 120,
        width: 655,
        minWidth:650,
        height: 160,
        waitMsgTarget: true,
        monitorValid: true,
        reader: reservationsFormReader,
        items: [{
            layout:'column',
            items:[{
                columnWidth:.6,
                layout: 'form',
                items: [/*{
		            fieldLabel : 'Fecha Solicitud',
		            id: 'frm_request_date',
		            name : 'request_date',  //debe coincidir con los campos del reservationsRecordUpdate 
		            allowBlank:false,
		            //width: 180,
		            xtype: 'datefield'
		        },*/  /* no lo pongo porque este campo se coge la fecha del sistema getdate*/
				{
		            fieldLabel : 'Fecha Entrada',
		            id: 'frm_reservation_begindate',
		            name : 'reservation_begindate',
					hiddenName: 'reservation_begindate',
				    vtype: 'daterange',
					endDateField:'frm_reservation_enddate',
					invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
		            format: 'Y-m-d',
		            allowBlank:false,
		            //width: 180,
		            xtype: 'datefield'
		        },	{
		            fieldLabel : 'Fecha Salida',
		            id: 'frm_reservation_enddate',
		            name : 'reservation_enddate',  //debe coincidir con los campos del reservationsRecordUpdate 
					hiddenName: 'reservation_enddate',
		            allowBlank:true,
					vtype: 'daterange',
					startDateField:'frm_reservation_begindate',
		            invalidText: "El formato correcto de la fecha es aaaa-mm-dd. Ejemplo: 2010-01-01",
		            format: 'Y-m-d',
					//width: 180,
		            xtype: 'datefield'
		        },	/*new Ext.form.ComboBox({
           			store: dataStorePersons,
           			fieldLabel: 'Persona',
           			displayField: 'person_fullname',
           			valueField: 'person_id',
           			hiddenName: 'person_id',
           			allowBlank: false,
           			typeAhead: true,
           			mode: 'local',
           			triggerAction: 'all',
           			emptyText: 'Seleccione una persona...',
           			selectOnFocus: true,
           			width: '100%',
				    id: 'frm_person_id',
		            name : 'person_id',
		            listeners: {
						'blur': function(){
							var flag = dataStorePersons.findExact( 'person_id', Ext.getCmp('frm_person_id').getValue());
				    		if (flag == -1){
				    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
				    			Ext.getCmp('frm_person_id').reset();
				    			return false;
				    		}
						}
			 		}
		      }),*/	new Ext.form.ComboBox({
	           			store: dataStoreProv,
	           			fieldLabel: 'Provincia hospedaje',
	           			displayField: 'province_name',
	           			valueField: 'province_id',
	           			hiddenName: 'province_idlodging',
	           			allowBlank: false,
	           			typeAhead: true,
	           			mode: 'local',
	           			triggerAction: 'all',
	           			emptyText: 'Seleccione una Provincia...',
	           			selectOnFocus: true,
	           			width: '100%',
					    id: 'frm_province_idlodging',
			            name : 'province_idlodging',
						 listeners: {
							'select': function(){
										dataStoreHotel.baseParams = {province_id: Ext.getCmp('frm_province_idlodging').getValue()};
										dataStoreHotel.load();
										Ext.getCmp('frm_hotel_id').reset();
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
		      }),	new Ext.form.ComboBox({
          			store: dataStoreHotel,
          			fieldLabel: 'Hotel',
          			displayField: 'hotel_name',
          			valueField: 'hotel_id',
          			allowBlank: false,
          			typeAhead: true,
          			mode: 'local',
          			triggerAction: 'all',
          			emptyText: 'Seleccione un Hotel...',
          			selectOnFocus: true,
          			width: '100%',
				    id: 'frm_hotel_id',
		            name : 'hotel_id',
		            hiddenName: 'hotel_id',
		            listeners: {
						'blur': function(){
							var flag = dataStoreHotel.findExact( 'hotel_id', Ext.getCmp('frm_hotel_id').getValue());
				    		if (flag == -1){
				    			Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
				    			Ext.getCmp('frm_hotel_id').reset();
				    			return false;
				    		}
						}
			 		}
		      }),	{
		            id: 'frm_reservation_id',
		            name : 'reservation_id',
					hiddenName: 'reservation_id',
		            xtype: 'hidden'
		    }]
            },{
                columnWidth:.4,
                layout: 'form',
                items: [
                 
				{
		            fieldLabel : 'Numero',
		            id: 'frm_reservation_number',
					hiddenName: 'reservation_number',
		            name : 'reservation_number',
		            allowBlank:false,
		            width: '80%',
		            xtype: 'textfield'
		        },
				{
		            fieldLabel : 'Habitaciones',
		            id: 'frm_reservation_rooms',
					hiddenName: 'reservation_rooms',
		            name : 'reservation_rooms',
		            allowBlank:false,
					width: '80%',
		             xtype: 'numberfield'
		        },
				{
		            fieldLabel : 'Cantidad personas',
		            id: 'frm_reservation_persons',
					hiddenName: 'reservation_persons',
		            name : 'reservation_persons',  //debe coincidir con los campos del reservationsRecordUpdate 
		            allowBlank:false,
					width: '80%',
		          
		            xtype: 'numberfield'
		        }	
				
				
				
				]
            }]
        }] //fin de los items del formulario
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
				var startDate = Ext.getCmp('frm_reservation_begindate').getValue();
                var reservation_date = startDate.format(Date.patterns.ISO8601Short);
                if (today > reservation_date){
                	Ext.MessageBox.alert('Error', 'La fecha de entrada debe ser mayor o igual a la fecha actual.');
                	return false;
                }
                updateForm.getForm().submit({
                   
                    url : baseUrl+'index.php/lodging/lodging_reservations/insert',
                    waitMsg : 'Salvando datos...',
                    failure: function (form, action) {
                        Ext.MessageBox.show({
                            title: 'Error al salvar los datos',
                            msg: 'Error al salvar los datos.',
                            width: 300,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        dataStoreLodgingReservation.load({params: {start:0,limit:15}});
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
                        dataStoreLodgingReservation.load({params: {start:0,limit:15}});
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
		        //formBind: true,
		        handler : function() {
		            updateForm.getForm().reset();
		            updateWindow.destroy();
		            selectedId = 0;
					sm2.clearSelections();//para quitar si estaba modificando la marca del checkox
		        }
		    });
		
		
		
		var updateWindow;
		var title = 'Agregar ';
		if (id > 0){//se va a actualizar cargamos los valores
			updateForm.load({url:baseUrl+'index.php/lodging/lodging_reservations/getById/'+id});
			title = 'Editar ';
		}
		
		if( ! updateWindow){

				updateWindow = new Ext.Window({
				title: title + 'Reservaci&oacute;n de Hospedaje',
				layout:'form',
				top: 200,
				width: 683,
				height:203,
				resizable : false,
				bodyStyle:'padding:5px;',
				items: updateForm  //adicionamos la forma dentro de la ventana
				
				});
			}
		updateWindow.show(this);

	}
	
	//  
	 
});
///////fin del onReady
    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/lodging/lodging_reservations/delete/'+array[i].get('reservation_id'),
				   method: 'POST',
				   disableCaching: false,
				   success: function(){
				   		dataStoreLodgingReservation.load({params: {start:0,limit:15}});
				   		Ext.MessageBox.show({
	                        title: 'Datos eliminados correctamente',
	                        msg: 'Datos eliminados correctamente',
	                        width: 300,
	                        buttons: Ext.MessageBox.OK,
	                        icon: Ext.MessageBox.INFO
	                    });
				   },//cierro success
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la reservacion.');
				   }
				});//cierro Ext.Ajax.request
		    }
			sm2.clearSelections();
			dataStoreLodgingReservation.load({params: {start:0,limit:15}});//cierro el for
    	}//cierro el if
    }//cierro la funcion
 //   