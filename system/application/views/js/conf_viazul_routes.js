var routesDataStore;
var array, sm2;

var dataRecordPlace = new Ext.data.Record.create([
    {name: 'viazul_place_id'},
    {name: 'viazul_place_name'}
]);
var dataReaderPlace = new Ext.data.JsonReader({root: 'data'}, dataRecordPlace);
var dataProxyPlace = new Ext.data.HttpProxy({
    url: baseUrl + 'index.php/conf/conf_viazul_places/getAllData',
    method: 'POST'
});
var dataStorePlace = new Ext.data.Store({
    proxy: dataProxyPlace,
    reader: dataReaderPlace,
    autoLoad: true
});

Ext.onReady(function () {

    Ext.BLANK_IMAGE_URL = baseAppUrl + 'views/images/s.gif';
    //Ext.QuickTips.init();
    /*
     * Creamos un espacio de nombres
     */
    Ext.namespace('Routes');

    function state(val) {
        if (val == 'No') {
            return '<span style="color:green;"><b>' + 'No' + '</b></span>';
        } else {
            return '<span style="color:red;"><b>' + 'Si' + '</b></span>';
        }
        return val;
    }

    var xg = Ext.grid;

    sm2 = new xg.CheckboxSelectionModel({
        listeners: {
            selectionchange: function (sm) {
                if (sm.getCount()) {
                    Routes.routesGrid.removeButton.enable();
                } else {
                    Routes.routesGrid.removeButton.disable();
                }
            }
        }
    });

    var p = new Ext.Panel({
        title: 'Configuraci&oacute;n -> Rutas Viazul',
        collapsible: false,
        renderTo: 'panel-basic',
        width: 750,
        bodyCfg: {}
    });


    /*
     * Definimos el registro
     */

    Routes.routesRecord = new Ext.data.Record.create([
        {name: 'viazul_route_id', type: 'int'},
        {name: 'origen', type: 'string'},
        {name: 'destino', type: 'string'},
        {name: 'province_name', type: 'string'},
        {name: 'viazul_route_price', type: 'string'},
        {name: 'viazul_route_deleted', type: 'string'},
        {name: 'viazul_place_id_form', type: 'int'},
        {name: 'viazul_place_id_to', type: 'int'}
    ]);

    /*
     * Creamos el reader para el Grid de cadenas hoteleras
     */
    Routes.routesGridReader = new Ext.data.JsonReader({
            root: 'data',
            totalProperty: 'count',
            id: 'viazul_route_id'
        },
        Routes.routesRecord
    );

    /*
     * Creamos el DataProxy para carga remota de los datos
     */
    Routes.routesDataProxy = new Ext.data.HttpProxy({
        url: baseUrl + 'index.php/conf/conf_viazul_routes/setData',
        method: 'POST'
    });

    routesDataStore = new Ext.data.GroupingStore({
        id: 'routesDS',
        proxy: Routes.routesDataProxy,
        reader: Routes.routesGridReader,
        sortInfo: {field: 'province_name', direction: "ASC"},
        groupField: 'province_name'

    });

    /*
     * Creamos el modelo de columnas para el grid de movimientos de la cuenta
     */
    Routes.routesColumnMode = new xg.ColumnModel(
        [new xg.RowNumberer(),
            sm2,
            {
                id: 'viazul_routes_id',
                name: 'viazul_routes_id',
                dataIndex: 'viazul_routes_id',
                hidden: true
            }, {
            id: 'province_name',
            name: 'province_name',
            header: "Provincia",
            width: 100,
            dataIndex: 'province_name',
            sortable: true
        }, {
            id: 'origen',
            name: 'origen',
            header: 'Origen',
            width: 200,
            dataIndex: 'origen',
            sortable: true
        }, {
            id: 'destino',
            name: 'destino',
            header: 'Destino',
            width: 200,
            dataIndex: 'destino',
            sortable: true
        }, {
            id: 'viazul_route_price',
            name: 'viazul_route_price',
            header: 'Precio',
            width: 80,
            dataIndex: 'viazul_route_price',
            sortable: true
        }, {
            header: "Eliminado",
            width: 80,
            dataIndex: 'viazul_route_deleted',
            renderer: state,
            sortable: true
        }]
    );


    /*
     * Creamos el grid
     */
    Routes.routesGrid = new xg.GridPanel({
        id: 'ctr-routes-grid',
        store: routesDataStore,
        cm: Routes.routesColumnMode,
        view: new Ext.grid.GroupingView({
            forceFit: true,
            groupTextTpl: '{text} ({[values.rs.length]} {[values.rs.length > 1 ? "Rutas" : "Ruta"]})'
        }),
        stripeRows: true,
        frame: true,
        width: 750,
        height: 380,
        tbar: [{
            text: 'Agregar',
            tooltip: 'Agregar nueva Ruta',
            iconCls: 'add',
            handler: function () {
                update_ventana();
            }
        }, '-', {
            text: 'Eliminar',
            tooltip: 'Eliminar la Ruta seleccionada',
            iconCls: 'del',
            ref: '../removeButton',
            disabled: true,
            handler: function () {
                array = sm2.getSelections();
                if (array.length > 0) {
                    Ext.MessageBox.confirm('Mensaje', 'Usted realmente desea eliminar esta(s) ruta(s)?', delRecords);
                }
            }
        }, '-', {
            text: 'Exportar a excel',
            tooltip: 'Exportar a excel',
            iconCls: 'xls',
            disabled: false,//por defecto true, siemrpe debe estar en true 
            handler: function () {
                //if (dataStorereportInternalLodging.getCount()>0 ){
                Routes.filterForm.getForm().getEl().dom.action = baseUrl + 'index.php/conf/conf_viazul_routes/exportExcel';
                Routes.filterForm.getForm().getEl().dom.method = 'POST';
                Routes.filterForm.getForm().submit();
                /*} else{
                 Ext.Msg.alert('Mensaje','No hay datos que exportar!');
                 }*/
                // }
            }
        }],
        bbar: new Ext.PagingToolbar({
            pageSize: 50,
            store: routesDataStore,
            displayInfo: true,
            displayMsg: 'Datos del  {0} - {1} de {2}',
            emptyMsg: "No hay datos"
        }),
        selModel: sm2
    });

    /*
     * A�adimos el evento doble click en una fila para editar el registro correspondiente
     */
    Routes.routesGrid.on('rowdblclick', function (grid, row, evt) {
        var selectedId = routesDataStore.getAt(row).data.viazul_route_id;
        update_ventana(selectedId);
    });

    Routes.filterForm = new Ext.FormPanel({
        id: 'form-filtro',
        region: 'north',
        split: false,
        frame: true,
        standardSubmit: true,
        monitorValid: true,
        labelWidth: 160,
        height: 150,
        width: 750,
        items: [
            new Ext.form.ComboBox({
                store: dataStorePlace,
                fieldLabel: 'Origen',
                displayField: 'viazul_place_name',
                valueField: 'viazul_place_id',
                hiddenName: 'viazul_place_id',
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText: 'Seleccione un Origen...',
                selectOnFocus: true,
                width: '100%',
                id: 'filter_viazul_place_id_from',
                name: 'viazul_place_id',
                listeners: {

                    'blur': function () {
                        var flag = dataStorePlace.findExact('viazul_place_id', Ext.getCmp('filter_viazul_place_id_from').getValue());
                        if (flag == -1 && Ext.getCmp('filter_viazul_place_id_from').getValue() != "") {
                            Ext.Msg.alert('Error', 'Debe seleccionar un origen de la lista y no introducir un valor por su cuenta');
                            Ext.getCmp('filter_viazul_place_id_from').reset();
                            return false;
                        }
                    }
                }
            }), new Ext.form.ComboBox({
                store: dataStorePlace,
                fieldLabel: 'Destino',
                displayField: 'viazul_place_name',
                valueField: 'viazul_place_id',
                hiddenName: 'viazul_place_id',
                typeAhead: true,
                mode: 'local',
                triggerAction: 'all',
                emptyText: 'Seleccione un Destino...',
                selectOnFocus: true,
                width: '100%',
                id: 'filter_viazul_place_id_to',
                name: 'viazul_place_id',
                listeners: {

                    'blur': function () {
                        var flag = dataStorePlace.findExact('viazul_place_id', Ext.getCmp('filter_viazul_place_id_to').getValue());
                        if (flag == -1 && Ext.getCmp('filter_viazul_place_id_to').getValue() != "") {
                            Ext.Msg.alert('Error', 'Debe seleccionar un destino de la lista y no introducir un valor por su cuenta');
                            Ext.getCmp('filter_viazul_place_id_to').reset();
                            return false;
                        }
                    }
                }
            })
        ]
    });

    Routes.filterForm.addButton({
        text: 'Borrar filtro',
        disabled: false,
        formBind: true,
        handler: function () {
            Routes.filterForm.getForm().reset();
            routesDataStore.baseParams = {
                from: 0,
                to: 0
            };
            routesDataStore.load({params: {start: 0, limit: 50}});
        }
    });

    Routes.filterForm.addButton({
        text: 'Filtrar',
        disabled: false,
        formBind: true,
        handler: function () {
            var from = Ext.getCmp('filter_viazul_place_id_from').getValue();
            var to = Ext.getCmp('filter_viazul_place_id_to').getValue();
            routesDataStore.baseParams = {
                from: from,
                to: to
            };
            routesDataStore.load({params: {start: 0, limit: 50}});
        }
    });

    function update_ventana(id) {

        Routes.routesRecordUpdate = new Ext.data.Record.create([
            {name: 'viazul_route_id', type: 'int'},
            {name: 'viazul_place_id_form', type: 'int'},
            {name: 'viazul_place_id_to', type: 'int'},
            {name: 'viazul_route_price', type: 'string'},
            {name: 'viazul_place_name', type: 'string'},
            {name: 'destino', type: 'string'},
            {name: 'viazul_route_deleted', type: 'string'}
        ]);

        /*
         * Creamos el reader para el formulario de alta/modificaci�n de movimientos
         */
        Routes.routesFormReader = new Ext.data.JsonReader({
                root: 'data',
                successProperty: 'success',
                totalProperty: 'count',
                id: 'viazul_route_id'
            }, Routes.routesRecordUpdate
        );

        var updateWindow;

        /*
         * Creamos el formulario de alta/modificaci�n
         */

        Routes.Form = new Ext.FormPanel({
            id: 'form-routes',
            region: 'west',
            split: false,
            collapsible: false,
            frame: true,
            width: 340,
            minWidth: 340,
            height: 160,
            waitMsgTarget: true,
            monitorValid: true,
            reader: Routes.routesFormReader,
            items: [
                new Ext.form.ComboBox({
                    store: dataStorePlace,
                    fieldLabel: 'Origen',
                    displayField: 'viazul_place_name',
                    valueField: 'viazul_place_id',
                    hiddenName: 'viazul_place_id_form',
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Origen...',
                    selectOnFocus: true,
                    width: '100%',
                    id: 'frm_viazul_place_id_from',
                    name: 'viazul_place_id_form',
                    listeners: {
                        'blur': function () {
                            var flag = dataStorePlace.findExact('viazul_place_id', Ext.getCmp('frm_viazul_place_id_from').getValue());
                            if (flag == -1 && Ext.getCmp('frm_viazul_place_id_from').getValue() != "") {
                                Ext.Msg.alert('Error', 'Debe seleccionar un origen de la lista y no introducir un valor por su cuenta');
                                Ext.getCmp('frm_viazul_place_id_from').reset();
                                return false;
                            }
                        }
                    }
                }), new Ext.form.ComboBox({
                    store: dataStorePlace,
                    fieldLabel: 'Destino',
                    displayField: 'viazul_place_name',
                    valueField: 'viazul_place_id',
                    hiddenName: 'viazul_place_id_to',
                    typeAhead: true,
                    mode: 'local',
                    triggerAction: 'all',
                    emptyText: 'Seleccione un Destino...',
                    selectOnFocus: true,
                    width: '100%',
                    id: 'frm_viazul_place_id_to',
                    name: 'viazul_place_id_to',
                    listeners: {

                        'blur': function () {
                            var flag = dataStorePlace.findExact('viazul_place_id', Ext.getCmp('frm_viazul_place_id_to').getValue());
                            if (flag == -1 && Ext.getCmp('frm_viazul_place_id_to').getValue() != "") {
                                Ext.Msg.alert('Error', 'Debe seleccionar un destino de la lista y no introducir un valor por su cuenta');
                                Ext.getCmp('frm_viazul_place_id_to').reset();
                                return false;
                            }
                        }
                    }
                }),
                {
                    fieldLabel: 'Precio',
                    id: 'frm_viazul_route_price',
                    name: 'viazul_route_price',
                    allowBlank: false,
                    xtype: 'numberfield'
                }, {
                    id: 'frm_viazul_route_id',
                    name: 'viazul_route_id',
                    xtype: 'hidden'
                }, new Ext.form.ComboBox({
                    store: ['No', 'Si'],
                    fieldLabel: 'Eliminado',
                    displayField: 'viazul_route_deleted',
                    valueField: 'viazul_route_deleted',
                    allowBlank: true,
                    typeAhead: true,
                    readOnly: true,
                    mode: 'local',
                    triggerAction: 'all',
                    selectOnFocus: true,
                    width: 50,
                    id: 'frm_viazul_route_deleted',
                    hiddenName: 'viazul_route_deleted',
                    name: 'frm_viazul_route_deleted'
                })]

        });

        /*
         * A�adimos el bot�n para guardar los datos del formulario
         */
        Routes.Form.addButton({
            text: 'Guardar',
            disabled: false,
            formBind: true,
            handler: function () {
                Routes.Form.getForm().submit({
                    url: baseUrl + 'index.php/conf/conf_viazul_routes/insert',
                    waitMsg: 'Salvando datos...',
                    failure: function (form, action) {
                        obj = Ext.util.JSON.decode(action.response.responseText);
                        Ext.MessageBox.show({
                            title: 'Error al salvar los datos',
                            msg: obj.errors.reason,
                            width: 300,
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });

                        sm2.clearSelections();
                        routesDataStore.load({params: {start: 0, limit: 50}});
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
                        Routes.Form.getForm().reset();
                        //updateWindow.destroy();
                        sm2.clearSelections();
                        routesDataStore.load({params: {start: 0, limit: 15}});
                    }
                });
            }
        });

        /*
         * A�adimos el bot�n para borrar el formulario
         */
        Routes.Form.addButton({
            text: 'Cancelar',
            disabled: false,
            //formBind: true,
            handler: function () {
                Routes.Form.getForm().reset();
                updateWindow.destroy();
                sm2.clearSelections();
            }
        });

        var title = 'Agregar';
        if (id > 0) {
            Routes.Form.load({url: baseUrl + 'index.php/conf/conf_viazul_routes/getById/' + id});
            var title = 'Editar';
        }

        if (!updateWindow) {

            updateWindow = new Ext.Window({
                title: title + ' Ruta',
                layout: 'form',
                top: 200,
                width: 364,
                height: 202,
                modal: true,
                resizable: false,
                bodyStyle: 'padding:5px;',
                items: Routes.Form

            });
        }
        updateWindow.show(this);

    }

    /*
     * Mostramos ventana, la centramos y cargamos los datos iniciales en el grid
     */
    Routes.filterForm.render(Ext.get('routes_grid'));
    Routes.routesGrid.render(Ext.get('routes_grid'));
    routesDataStore.load({params: {start: 0, limit: 15}});
});

function delRecords(btn) {
    if (btn == 'yes') {
        for (var i = 0, len = array.length; i < len; i++) {
            Ext.Ajax.request({
                url: baseUrl + 'index.php/conf/conf_viazul_routes/delete/' + array[i].get('viazul_route_id'),
                method: 'GET',
                disableCaching: false,
                success: function () {
                    routesDataStore.load({params: {start: 0, limit: 50}});
                    Ext.MessageBox.show({
                        title: 'Datos eliminados correctamente',
                        msg: 'Datos eliminados correctamente',
                        width: 300,
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.INFO
                    });
                    sm2.clearSelections();
                },
                failure: function () {
                    Ext.MessageBox.alert('Error', 'No se pudo eliminar el Destino.');
                    sm2.clearSelections();
                    routesDataStore.load({params: {start: 0, limit: 50}});
                }
            });
        }
    }
}
