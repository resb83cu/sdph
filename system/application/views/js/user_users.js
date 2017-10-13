var usersDataStore;
var array, sm2;

var dataRecordProv = new Ext.data.Record.create([
    {name: 'province_id'},
    {name: 'province_name'}
]);
var dataReaderProv = new Ext.data.JsonReader({root: 'data'}, dataRecordProv);
var dataProxyProv = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_provinces/setDataGrid',
    method: 'POST'
});
var dataStoreProv = new Ext.data.Store({
    proxy: dataProxyProv,
    reader: dataReaderProv,
    autoLoad: true
});

var dataRecordDirector = new Ext.data.Record.create([
    {name: 'person_id'},
    {name: 'person_fullname'}
]);
var dataReaderDirector = new Ext.data.JsonReader({root: 'data'}, dataRecordDirector);
var dataProxyDirector = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/person/person_workers/setDirectorByProvince',
    method: 'POST'
});
var dataStoreDirector = new Ext.data.Store({
    proxy: dataProxyDirector,
    reader: dataReaderDirector,
    autoLoad: true
});

var dataRecordWork = new Ext.data.Record.create([
    {name: 'person_id'},
    {name: 'person_fullname'}
]);
var dataReaderWork = new Ext.data.JsonReader({root: 'data'}, dataRecordWork);
var dataProxyWork = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/person/person_workers/setDataByProvince',
    method: 'POST'
});
var dataStoreWork = new Ext.data.Store({
    proxy: dataProxyWork,
    reader: dataReaderWork,
    autoLoad: false
});

var dataRecordCenter = new Ext.data.Record.create([
    {name: 'center_id'},
    {name: 'center_name'}
]);

var dataReaderCenter = new Ext.data.JsonReader({root: 'data'}, dataRecordCenter);

var dataProxyCenter = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_costcenters/getDataByProvince',
    method: 'POST'
});

var dataStoreCenter = new Ext.data.Store({
    proxy: dataProxyCenter,
    reader: dataReaderCenter,
    autoLoad: true
});

var dataRecordRoll = new Ext.data.Record.create([
    {name: 'roll_id'},
    {name: 'roll_description'}
]);
var dataReaderRoll = new Ext.data.JsonReader({root: 'data'}, dataRecordRoll);
var dataProxyRoll = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/user/user_rolls/setDataGrid',
    method: 'POST'
});
var dataStoreRoll = new Ext.data.Store({
    proxy: dataProxyRoll,
    reader: dataReaderRoll,
    autoLoad: true
});

var operation;//para ver si al llamar al update_ventana es agregar o actualizar,recordar que en user_user la llave es person_id y luego al pasar desde el formulario al metodo insert del controlador-modelo no se sabra si es para actualziar o para insertar

Ext.onReady(function () {
    function state(val) {
        if (val == '0') {
            return '<span style="color:green;"><b>' + 'No' + '</b></span>';
        } else {
            return '<span style="color:red;"><b>' + 'Si' + '</b></span>';
        }
        return val;
    }

    Ext.BLANK_IMAGE_URL = baseAppUrl + 'views/images/s.gif';
    Ext.QuickTips.init();

    // turn on validation errors beside the field globally
    Ext.form.Field.prototype.msgTarget = 'side';

    /*
     * Creamos un espacio de nombres

     */
    Ext.namespace('Users');
    Users.setMode = false;
    var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function (sm) {
                if (sm.getCount()) {
                    Users.usersGrid.removeButton.enable();
                    Users.usersGrid.sessionButton.enable();
                } else {
                    Users.usersGrid.removeButton.disable();
                    Users.usersGrid.sessionButton.disable();
                }
            }
        }
    });

    var p = new Ext.Panel({
        title: 'Administraci&oacute;n -> Usuarios',
        collapsible: false,
        renderTo: 'panel-basic',
        width: 750,
        bodyCfg: {
        }
    });

    /*
     * Definimos el registro
     */

    Users.usersRecord = new Ext.data.Record.create([
        {name: 'person_id', type: 'int'},
        {name: 'user_name', type: 'string'},
        {name: 'person_fullname', type: 'string'},
        {name: 'user_createdate'},
        {name: 'roll_description', type: 'string'},
        {name: 'province_id', type: 'int'},
        {name: 'province_name', type: 'string'},
        {name: 'locked', type: 'string'}
    ]);

    /*
     * Creamos el reader para el Grid
     */
    Users.usersGridReader = new Ext.data.JsonReader({
            root: 'data',
            totalProperty: 'count',
            id: 'person_id'},
        Users.usersRecord
    );
    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Users.usersDataProxy = new Ext.data.HttpProxy({
        url: baseUrl + 'index.php/user/user_users/setData',
        method: 'POST'
    });

    usersDataStore = new Ext.data.GroupingStore({
        id: 'usersDS',
        proxy: Users.usersDataProxy,
        reader: Users.usersGridReader,
        sortInfo: {field: 'province_name', direction: "ASC"},
        groupField: 'province_name'
    });

    Users.usersColumnMode = new xg.ColumnModel(
        [new xg.RowNumberer(),
            sm2,
            {
                id: 'person_id',
                name: 'person_id',
                dataIndex: 'person_id',
                hidden: true
            }, {
            id: 'locked',
            name: 'locked',
            header: 'Bloqueado',
            width: 60,
            renderer: state,
            dataIndex: 'locked',
            sortable: true
        }, {
            id: 'user_name',
            name: 'user_name',
            header: 'Usuario',
            width: 100,
            dataIndex: 'user_name',
            sortable: true
        }, {
            id: 'person_fullname',
            name: 'person_fullname',
            header: 'Trabajador',
            width: 140,
            dataIndex: 'person_fullname',
            sortable: true
        }, {
            id: 'roll_description',
            name: 'roll_description',
            header: 'Tipo usuario',
            width: 100,
            dataIndex: 'roll_description',
            sortable: true
        }, {
            id: 'province_id',
            name: 'province_id',
            dataIndex: 'province_id',
            hidden: true
        }, {
            id: 'province_name',
            name: 'province_name',
            header: "Provincia",
            width: 100,
            dataIndex: 'province_name',
            sortable: true
        }]
    );

    /*
     * Creamos el grid
     */
    Users.usersGrid = new xg.GridPanel({
        id: 'ctr-users-grid',
        store: usersDataStore,
        cm: Users.usersColumnMode,
        stripeRows: true,
        view: new Ext.grid.GroupingView({
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Usuarios" : "Usuario"]})'
        }),
        //enableColLock : false,
        frame: true,
        collapsible: true,
        width: 750,
        height: 380,
        tbar: [
            {
                text: 'Agregar',
                tooltip: 'Agregar nuevo Usuario',
                iconCls: 'add',
                handler: function () {
                    operation = 'INSERT';
                    Users.setMode = false;
                    update_ventana(0);
                }
            },
            '-',
            {
                text: 'Desbloquear Usuario',
                tooltip: 'Desbloquear usuario seleccionado',
                iconCls: 'add',
                ref: '../sessionButton',
                disabled: true,
                handler: function () {
                    array = sm2.getSelections();
                    if (array.length > 1) {
                        Ext.Msg.alert('Mensaje', 'Debe seleccionar un solo usuario');
                        sm2.clearSelections();
                        usersDataStore.load({params: {start: 0, limit: 100}});
                        return false;
                    }
                    Ext.Ajax.request({
                        url: baseUrl + 'index.php/user/user_users/deleteSession/' + array[0].get('person_id'),
                        method: 'GET',
                        disableCaching: false,
                        success: function () {
                            usersDataStore.load({params: {start: 0, limit: 100}});
                            Ext.MessageBox.show({
                                title: 'Desbloqueo de Usuario',
                                msg: 'Usuario desbloqueado correctamente',
                                width: 300,
                                buttons: Ext.MessageBox.OK,
                                icon: Ext.MessageBox.INFO
                            });
                        },
                        failure: function () {
                            Ext.MessageBox.alert('Error', 'No se pudo eliminar la sesion.');
                        }

                    });
                    sm2.clearSelections();
                    usersDataStore.load({params: {start: 0, limit: 100}});
                }
            },
            '-',
            {
                text: 'Eliminar',
                tooltip: 'Eliminar Usuario seleccionado',
                iconCls: 'del',
                ref: '../removeButton',
                disabled: true,
                handler: function () {
                    array = sm2.getSelections();
                    if (array.length > 0) {
                        Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar este(os) Usuario(s)?', delRecords);
                    }
                }
            },
            '-',
            {
                text: 'Exportar a pdf',
                tooltip: 'Exportar a pdf',
                iconCls: 'pdf',
                handler: function () {
                    if (usersDataStore.getCount() > 0) {
                        Users.filterForm.getForm().getEl().dom.action = baseUrl + 'index.php/user/user_users/usersPdf';
                        Users.filterForm.getForm().getEl().dom.method = 'POST';
                        Users.filterForm.getForm().submit();
                    } else {
                        Ext.Msg.alert('Mensaje', 'No hay datos que exportar!');
                    }
                }
            }
        ],
        bbar: new Ext.PagingToolbar({
            pageSize: 100,
            store: usersDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel: sm2
    });

    Users.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit: true,
        monitorValid: true,
        labelWidth: 160,
        height: 100,
        width: 750,
        items: [new Ext.form.ComboBox({
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
            name: 'province_id',
            listeners: {

                'blur': function () {
                    var flag = dataStoreProv.findExact('province_id', Ext.getCmp('filter_province_id').getValue());
                    if (flag == -1) {
                        Ext.Msg.alert('Error', 'Debe seleccionar una provincia de la lista y no introducir un valor por su cuenta');
                        Ext.getCmp('filter_province_id').reset();
                        return false;
                    }
                }
            }
        }), {
            fieldLabel: 'Nombre de usuario',
            id: 'filter_user_name',
            name: 'user_name',
            width: 180,
            xtype: 'textfield'
        }
        ]
    });

    Users.filterForm.addButton({
        text: 'Borrar filtro',
        disabled: false,
        formBind: true,
        handler: function () {
            Users.filterForm.getForm().reset();
            usersDataStore.baseParams = {
                name: '',
                province: 0
            };
            usersDataStore.load({params: {start: 0, limit: 100}});
        }
    });

    Users.filterForm.addButton({
        text: 'Filtrar',
        disabled: false,
        formBind: true,
        handler: function () {
            ;
            var name = Ext.getCmp('filter_user_name').getValue();
            var province = Ext.getCmp('filter_province_id').getValue();
            usersDataStore.baseParams = {
                name: name,
                province: province
            };
            usersDataStore.load({params: {start: 0, limit: 100}});
        }
    });


    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Users.usersGrid.on('rowdblclick', function (grid, row, evt) {
        var selectedId = usersDataStore.getAt(row).data.person_id;
        var provinceSelId = usersDataStore.getAt(row).data.province_id;
        operation = 'UPDATE';
        Users.setMode = true;
        update_ventana(selectedId, provinceSelId);
    });

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Users.filterForm.render(Ext.get('users_grid'));
    Users.usersGrid.render(Ext.get('users_grid'));
    usersDataStore.load({params: {start: 0, limit: 100}});

    function update_ventana(id, province) {

        Users.usersRecordUpdate = new Ext.data.Record.create([
            {name: 'person_id', type: 'int'},
            {name: 'user_name', type: 'string'},
            {name: 'roll_id', type: 'int'},
            {name: 'province_id', type: 'int'},
            {name: 'center_id', type: 'int'},
            {name: 'person_idparent', type: 'int'},
            {name: 'locked', type: 'string'}
        ]);

        /*
         * Creamos el reader para el formulario de alta/modificaci�n
         */
        Users.usersFormReader = new Ext.data.JsonReader({
                root: 'data',
                successProperty: 'success',
                totalProperty: 'count',
                id: 'person_id'
            }, Users.usersRecordUpdate
        );

        var updateWindow;

        /*
         * Creamos el formulario de alta/modificacion de motivos
         */
        Users.Form = new Ext.FormPanel({
            id: 'form-users',
            region: 'west',
            split: false,
            collapsible: true,
            frame: true,
            labelWidth: 130,
            width: 370,
            minWidth: 370,
            height: 250,
            waitMsgTarget: true,
            monitorValid: true,
            reader: Users.usersFormReader,
            items: [new Ext.form.ComboBox({
                disabled: Users.setMode,
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
                name: 'province_id',
                listeners: {
                    'select': function () {
                        dataStoreWork.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
                        dataStoreWork.load();
                        dataStoreCenter.baseParams = {province_id: Ext.getCmp('frm_province_id').getValue()};
                        dataStoreCenter.load();
                    },
                    'blur': function () {
                        var flag = dataStoreProv.findExact('province_id', Ext.getCmp('frm_province_id').getValue());
                        if (flag == -1) {
                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                            Ext.getCmp('frm_province_id').reset();
                            return false;
                        }
                    }
                }
            }), new Ext.form.ComboBox({
                store: dataStoreWork,
                disabled: Users.setMode,
                fieldLabel: 'Trabajador',
                displayField: 'person_fullname',
                valueField: 'person_id',
                hiddenName: 'person_id',
                allowBlank: false,
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText: 'Seleccione un Trabajador...',
                selectOnFocus: true,
                width: 200,
                id: 'frm_person_id',
                name: 'person_id',
                listeners: {
                    'blur': function () {
                        var flag = dataStoreWork.findExact('person_id', Ext.getCmp('frm_person_id').getValue());
                        if (flag == -1) {
                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                            Ext.getCmp('frm_person_id').reset();
                            return false;
                        }
                    }/*,
                     'keypress': function () {
                     var personSearch = Ext.getCmp('frm_person_id').getRawValue();
                     if(personSearch.length > 2){
                     dataStoreWork.baseParams = {
                     province_id: Ext.getCmp('frm_province_id').getValue(),
                     name: Ext.getCmp('frm_person_id').getRawValue()
                     };
                     dataStoreWork.load();
                     }
                     }*/
                }
            }), new Ext.form.ComboBox({
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
                    }
                }
            }), new Ext.form.ComboBox({
                store: dataStoreRoll,
                fieldLabel: 'Tipo de usuario',
                displayField: 'roll_description',
                valueField: 'roll_id',
                hiddenName: 'roll_id',
                allowBlank: false,
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText: 'Seleccione un Rol...',
                selectOnFocus: true,
                width: 200,
                id: 'frm_roll_id',
                name: 'roll_id',
                listeners: {
                    'blur': function () {
                        var flag = dataStoreRoll.findExact('roll_id', Ext.getCmp('frm_roll_id').getValue());
                        if (flag == -1) {
                            Ext.Msg.alert('Valor Inv&aacute;lido', 'Debe seleccionar un valor de la lista y no introducir un dato err&oacute;neo.');
                            Ext.getCmp('frm_roll_id').reset();
                            return false;
                        }
                    }
                }
            }), {
                fieldLabel: 'Nombre de usuario',
                id: 'user_name',
                name: 'user_name',
                allowBlank: false,
                width: 200,
                xtype: 'textfield'
            }, {
                xtype: 'checkbox',
                id: 'frm_user_locked',
                name: 'locked',
                fieldLabel: 'Bloquear',
                checked: 'locked'
            }]

        });


        /*
         * Anyadimos el boton para guardar los datos del formulario
         */
        Users.Form.addButton({
            text: 'Guardar',
            disabled: false,
            formBind: true,
            handler: function () {
                Ext.getCmp('frm_person_id').enable();//de PIng!!!!! habilito antes de ir al submit para tomar el id, que es desahbilitado al input si disable esta en true
                Users.Form.getForm().submit({
                    url: baseUrl + 'index.php/user/user_users/insert/' + operation + '/',//variable declarada arriba arriba para ver que operation se realizara, si update o insertar
                    waitMsg: 'Salvando datos...',
                    failure: function (form, action) {
                        if (action.failureType == 'server') {
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.alert('Fall&oacute; el registro!', obj.errors.reason);
                        } else {
                            Ext.Msg.alert('Advertencia!', 'Authentication server is unreachable : ' + action.response.responseText);
                        }
                        sm2.clearSelections();
                        usersDataStore.load({params: {start: 0, limit: 100}});
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
                        Users.Form.getForm().reset();
                        updateWindow.destroy();
                        sm2.clearSelections();
                        usersDataStore.load({params: {start: 0, limit: 100}});
                    }
                });
            }
        });

        /*
         * A�adimos el botOn para borrar el formulario
         */
        Users.Form.addButton({
            text: 'Cancelar',
            disabled: false,
            handler: function () {
                Users.Form.getForm().reset();
                updateWindow.destroy();
                sm2.clearSelections();
            }
        });

        var title = 'Agregar';
        if (id > 0) {
            dataStoreWork.baseParams = {province_id: province};
            dataStoreWork.load();
            dataStoreCenter.baseParams = {province_id: province};
            dataStoreCenter.load();
            Users.Form.load({url: baseUrl + 'index.php/user/user_users/getById/' + id});
            title = 'Editar';
        }

        if (!updateWindow) {

            updateWindow = new Ext.Window({
                title: title + ' Usuario',
                layout: 'form',
                top: 200,
                width: 395,
                height: 290,
                resizable: false,
                modal: true,
                bodyStyle: 'padding:5px;',
                items: Users.Form

            });
        }
        updateWindow.show(this);

    }

});

function delRecords(btn) {
    if (btn == 'yes') {
        for (var i = 0, len = array.length; i < len; i++) {
            Ext.Ajax.request({
                url: baseUrl + 'index.php/user/user_users/delete/' + array[i].get('person_id'),
                method: 'GET',
                disableCaching: false,
                success: function () {
                    Ext.MessageBox.show({
                        title: 'Datos eliminados correctamente',
                        msg: 'Datos eliminados correctamente',
                        width: 300,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.INFO
                    });
                },
                failure: function () {
                    Ext.MessageBox.alert('Error', 'No se pudo eliminar el Usuario.');
                }
            });
        }
        sm2.clearSelections();
        usersDataStore.load({params: {start: 0, limit: 100}});
    }
}
