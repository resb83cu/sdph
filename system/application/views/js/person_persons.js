var personsDataStore;
var array, sm2;

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
						url:baseUrl+'index.php/person/person_persons/setDataByProvinceId',
						method: 'POST'
					});
var dataStorePers = new Ext.data.Store({
						proxy: dataProxyPers,
						reader: dataReaderPers,
						autoLoad: false
						});
						
/*var dataRecordWork = new Ext.data.Record.create([
						{name:'person_id'},
						{name:'person_fullname'}
					]);
var dataReaderWork = new Ext.data.JsonReader({root:'data'},dataRecordWork);
var dataProxyWork = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/person/person_workers/setDataByProvince',
						method: 'POST'
					});
var dataStoreWork = new Ext.data.Store({
						proxy: dataProxyWork,
						reader: dataReaderWork,
						autoLoad: false
						});*/					

var dataRecordPos = new Ext.data.Record.create([
						{name:'position_id'},
						{name:'position_name'}
					]);
var dataReaderPos = new Ext.data.JsonReader({root:'data'},dataRecordPos);
var dataProxyPos = new Ext.data.HttpProxy({
						url:baseUrl+'index.php/conf/conf_positions/setDataGrid',
						method: 'POST'
					});
var dataStorePos = new Ext.data.Store({
						proxy: dataProxyPos,
						reader: dataReaderPos,
						autoLoad: true
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
						
Ext.onReady(function() {

	Ext.BLANK_IMAGE_URL = baseAppUrl+'views/images/s.gif';
	Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

	
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Persons');
    
	var bd = Ext.getBody();

   	var xg = Ext.grid;
   	
    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function(sm) {
                if (sm.getCount()) {
                    Persons.personsGrid.removeButton.enable();
                } else {
                    Persons.personsGrid.removeButton.disable();
                }
            }
        }
    });
    
	var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Personas',
        collapsible:false,
        renderTo: 'panel-basic',
        width:750,
        bodyCfg: {
	    }
    });
    
    /*
     * Definimos el registro
     */
     
    Persons.personsRecord = new Ext.data.Record.create([
        {name: 'person_id', type: 'int'},
        {name: 'person_identity', type: 'string'},
        {name: 'person_fullname', type: 'string'},
        {name: 'province_name', type: 'string'}
    ]);

    
    /*
     * Creamos el reader para el Grid de cadenas personeras
     */
    Persons.personsGridReader = new Ext.data.JsonReader({
		root: 'data',
        totalProperty: 'count',
        id: 'person_id'},
        Persons.personsRecord
    );
    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Persons.personsDataProxy = new Ext.data.HttpProxy({
        url: baseUrl+'index.php/person/person_persons/setData',
        method: 'POST'
    });

    personsDataStore = new Ext.data.GroupingStore({
        id: 'personsDS',
        proxy: Persons.personsDataProxy,
        reader: Persons.personsGridReader,
        sortInfo:{field: 'province_name', direction: "ASC"},
		groupField:'province_name'
    });

    Persons.personsColumnMode = new xg.ColumnModel(
       [new xg.RowNumberer(),
       	sm2,
       {
            id: 'person_id',
            name : 'person_id',
            dataIndex: 'person_id',
            hidden: true
        },{
	   		id: 'person_identity',
            name: 'person_identity',
            header: 'CI',
            width: 70,
            dataIndex: 'person_identity',
            sortable: true
        },{
            id: 'person_fullname',
            name : 'person_fullname',
            header: 'NOMBRE Y APELLIDOS',
            width: 140,
            dataIndex: 'person_fullname',
            sortable: true
        },{
            id: 'province_name',
            name : 'province_name',
            header: "PROVINCIA",
            width: 90,
            dataIndex: 'province_name',
            sortable: true
        }]
    );

    /*
     * Creamos el grid
     */
    Persons.personsGrid = new xg.GridPanel({
        id : 'ctr-persons-grid',
        store : personsDataStore,
        cm : Persons.personsColumnMode,
        stripeRows: true,
		view: new Ext.grid.GroupingView({
          forceFit:true,
          groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Personas" : "Persona"]})'
        }),
        //enableColLock : false,
        frame:true,
        collapsible: true,
        width : 750,
        height : 380,
        tbar:[{
	            text:'Agregar',
	            tooltip:'Agregar nueva Persona',
	            iconCls:'add',
	            handler: function(){
	                update_ventana();
	            }
	        },'-',{
	            text:'Eliminar',
	            tooltip:'Eliminar Persona seleccionada',
	            iconCls:'del',
	            ref: '../removeButton',
	            disabled: true,
	            handler: function(){
	            	    array = sm2.getSelections();
					    if (array.length > 0) {
					        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) Persona(s)?', delRecords);
					    }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 100,
            store: personsDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel : sm2
    });

	
    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Persons.personsGrid.on('rowdblclick',function( grid, row, evt) {
        var selectedId = personsDataStore.getAt(row).data.person_id;
        update_ventana(selectedId);
    });
    
    Persons.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit:true,
        monitorValid: true,
        labelWidth: 100,
        height: 130,
        width: 750,
        items:[{
            layout:'column',
            border:false,
            items:[{
		      columnWidth:.5,
		      layout: 'form',
		      border:false,
		      items: [	new Ext.form.ComboBox({
		         			store: dataStoreProv,
		           			fieldLabel: 'Provincia',
		           			displayField: 'province_name',
		           			valueField: 'province_id',
		           			hiddenName: 'province_id',
		           			typeAhead: true,
		           			mode: 'local',
		           			triggerAction: 'all',
		           			emptyText: 'Seleccione una Provincia...',
		           			selectOnFocus: true,
		           			width: 200,
						    id: 'filter_province_id',
				            name : 'province_id',
				            listeners: {
		
									'blur': function(){
										var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('filter_province_id').getValue());
							    		if (flag == -1){
							    			Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
							    			Ext.getCmp('filter_province_id').reset();
							    			return false;
							    		}
									}
						 	}
		                }),	{
				            fieldLabel : 'CI',
				            id: 'filter_person_identity',
				            name : 'person_identity',
				            maxLength: 11,
				            width: 180,
							invalidText: "Carnet de identidad no valido",
				            xtype: 'numberfield'
				        }
		             ]
		    },{
		      columnWidth:.5,
		      layout: 'form',
		      border: false,
		      items: [	{
				            fieldLabel : 'Nombre',
				            id: 'filter_person_name',
				            name : 'person_name',
				            width: 180,
				            xtype: 'textfield'
				        },	{
				            fieldLabel : '1er Apellido',
				            id: 'filter_person_lastname',
				            name : 'person_lastname',
				            width: 180,
				            xtype: 'textfield'
				        },	{
				            fieldLabel : '2do Apellido',
				            id: 'filter_person_secondlastname',
				            name : 'person_secondlastname',
				            width: 180,
				            xtype: 'textfield'
				        }	
		              ]
            }]
        }]
	});

    Persons.filterForm.addButton({
       text : 'Borrar filtro',
       disabled : false,
       formBind: true,
       handler : function() {
    		Persons.filterForm.getForm().reset();
    		personsDataStore.baseParams = {
				ci: '',
				name: '',
				lastname: '',
				secondlastname: '',
				province: 0
    		};
    		personsDataStore.load({params: {start:0,limit:100}});
       }
    });
    
    Persons.filterForm.addButton({
       	text : 'Filtrar',
       	disabled : false,
       	formBind: true,
       	handler : function() {
           	var ci = Ext.getCmp('filter_person_identity').getValue();
           	var name = Ext.getCmp('filter_person_name').getValue();
			var lastname = Ext.getCmp('filter_person_lastname').getValue();
			var secondlastname = Ext.getCmp('filter_person_secondlastname').getValue();
			var province = Ext.getCmp('filter_province_id').getValue();
			personsDataStore.baseParams = {
				ci: ci,
				name: name,
				lastname: lastname,
				secondlastname: secondlastname,
				province: province
           	};
			personsDataStore.load({params: {start:0,limit:100}});
       	}
   	});    
    
	/*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Persons.filterForm.render(Ext.get('persons_grid'));
	Persons.personsGrid.render(Ext.get('persons_grid'));
    personsDataStore.load({params: {start:0,limit:100}});
    
    function update_ventana(id){
	
		Persons.personsRecordUpdate = new Ext.data.Record.create([
	        {name: 'person_id', type: 'int'},
	        {name: 'person_identity', type: 'string'},
	        {name: 'person_name', type: 'string'},
	        {name: 'person_lastname', type: 'string'},
	        {name: 'person_secondlastname', type: 'string'},
	        {name: 'person_address', type: 'string'},
	        {name: 'person_phone', type: 'string'},
	        {name: 'province_id', type: 'int'},
	        {name: 'person_isworker'},
	        {name: 'worker_email', type: 'string'},
	        {name: 'worker_phone', type: 'string'},
	        {name: 'position_id', type: 'int'}
	    ]);
	    
		/*
	     * Creamos el reader para el formulario de alta/modificaci�n
	     */
	    Persons.personsFormReader = new Ext.data.JsonReader({
	        root : 'data',
	        successProperty : 'success',
	        totalProperty: 'count',
	        id: 'person_id'
	        },Persons.personsRecordUpdate
	    );
	    
	    var updateWindow;
	    
		 /*
	     * Creamos el formulario de alta/modificaci�n de motivos
	     */
		 
		var ciExpr1 = /[0-9]/;
	    Persons.Form = new Ext.FormPanel({
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
	        reader: Persons.personsFormReader,
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
			        },{
			            fieldLabel : 'Nombre',
			            id: 'frm_person_name',
			            name : 'person_name',
			            allowBlank:false,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            fieldLabel : '1er Apellido',
			            id: 'frm_person_lastname',
			            name : 'person_lastname',
			            allowBlank:false,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            fieldLabel : '2do Apellido',
			            id: 'frm_person_secondlastname',
			            name : 'person_secondlastname',
			            allowBlank:false,
			            width: 180,
			            xtype: 'textfield'
			        }, new Ext.form.ComboBox({
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
								/*'select': function(){
											dataStoreWork.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
											dataStoreWork.load(); 
								},*/
								'blur': function(){
									var flag = dataStoreProv.findExact( 'province_id', Ext.getCmp('frm_province_id').getValue());
						    		if (flag == -1){
						    			Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
						    			Ext.getCmp('frm_province_id').reset();
						    			return false;
						    		}
								}
					 	}
	                }),{
			            fieldLabel : 'Direcci&oacute;n particular',
			            id: 'frm_person_address',
			            name : 'person_address',
			            allowBlank: true,
			            width: 200,
			            xtype: 'textarea'
			        },{
			            fieldLabel : 'Tel&eacute;fono',
			            id: 'frm_person_phone',
			            name : 'person_phone',
			            allowBlank:true,
			            width: 180,
			            xtype: 'textfield'
			        },{
			            id: 'frm_person_id',
			            name : 'person_id',
			            xtype: 'hidden'
			        },{
	                	xtype: 'checkbox',
		                id: 'frm_person_isworker',
		                name: 'person_isworker',
		                fieldLabel: 'Es Trabajador',
		                checked: 'person_isworker',
		                listeners: {
							'check' : function(chk) {
								if (chk.checked){
									Persons.Form.findById('frm_worker_email').enable();
									Persons.Form.findById('frm_worker_phone').enable();
								}else{
									Persons.Form.findById('frm_worker_email').disable();
									Persons.Form.findById('frm_worker_phone').disable();
								}
							}
						}
					}, {
			            fieldLabel : 'Correo',
			            id: 'frm_worker_email',
			            name : 'worker_email',
			            allowBlank:true,
			            xtype: 'textfield',
			            width: 180,
			            vtype: 'email'
			        },{
			            fieldLabel : 'Tel&eacute;fono',
			            id: 'frm_worker_phone',
			            name : 'worker_phone',
			            allowBlank:true,
			            xtype: 'textfield',
			            width: 180
			        } /*new Ext.form.ComboBox({
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
	     * A�adimos el bot�n para guardar los datos del formulario
	     */
	    Persons.Form.addButton({
	        text : 'Guardar',
	        disabled : false,
	        formBind: true,
	        handler : function() {
	            Persons.Form.getForm().submit({
	                url : baseUrl+'index.php/person/person_persons/insert',
	                waitMsg : 'Salvando datos...',
					failure: function (form, action) {
                            obj = Ext.util.JSON.decode(action.response.responseText); 
                            Ext.Msg.alert('Fall&oacute; la operaci&oacute;n!', obj.errors.reason);
                            sm2.clearSelections();
     	                    personsDataStore.load({params: {start:0,limit:100}});
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
	                    Persons.Form.getForm().reset();
						updateWindow.destroy();
	                    sm2.clearSelections();
	                    personsDataStore.load({params: {start:0,limit:100}});
	                }
	            });
	        }
	    });
	    
	    Persons.Form.addButton({
	        text : 'Cancelar',
	        disabled : false,
	        //formBind: true,
	        handler : function() {
	            Persons.Form.getForm().reset();
	            updateWindow.destroy();
	            sm2.clearSelections();
	        }
	    });
	
	  	var title = 'Agregar';
		if (id > 0){
			title = 'Editar';
			Persons.Form.load({url:baseUrl+'index.php/person/person_persons/getById/'+id});
		}
		
		if(!updateWindow){
	
				updateWindow = new Ext.Window({
				title: title + ' Persona',
				layout:'form',
				top: 200,
				width: 400,
				height:423,
				resizable : false,
				modal: true,
				bodyStyle:'padding:5px;',
				items: Persons.Form
				
				});
			}
		updateWindow.show(this);
	
	}
    
});

    function delRecords(btn) {
	    if (btn == 'yes') {
			for (var i = 0, len = array.length; i < len; i++) {
		        Ext.Ajax.request({
				   url: baseUrl+'index.php/person/person_persons/delete/'+array[i].get('person_id'),
				   method: 'GET',
				   disableCaching: false,
				   failure: function(){
				   		Ext.MessageBox.alert('Error', 'No se pudo eliminar la Persona.');
				   }
				});
		    }
            sm2.clearSelections();
		    personsDataStore.load({params: {start:0,limit:100}});
    	}
    }
