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
                    Users.usersGrid.sessionButton.enable();
                } else {
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
        url: baseUrl + 'index.php/user/user_users/setDataContable',
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
            }
        ],
        bbar: new Ext.PagingToolbar({
            pageSize: 50,
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
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Users.filterForm.render(Ext.get('users_grid'));
    Users.usersGrid.render(Ext.get('users_grid'));
    usersDataStore.load({params: {start: 0, limit: 100}});

});