pimcore.registerNS("pimcore.plugin.MagentoIntegrationBundle.ProductMapper");
pimcore.plugin.MagentoIntegrationBundle.ProductMapper = Class.create(pimcore.plugin.MagentoIntegrationBundle.item, {
    initialize: function (object) {
        this.object = object;
        this.requiredFields = ["name", "sku", "visibility", "is_active_in_pim", "url_key", "category_ids"];
    },
    reloadMapper: function (object, idObject) {
        this.object = object;
        this.reloadColumnMapping(idObject);
    },

    getEmptyHiddenLayout: function (){
        return Ext.create({
            xtype: 'panel',
            layout: 'border',
            id: 'product-mapping-tab' + this.object.id,
            title: t('Product Mapping'),
            iconCls: 'pimcore_icon_fieldset',
            disabled: true,
            hidden: true
        });
    },

    getLayout: function (idObject) {
        if (!this.mappingSettings) {
            this.mappingSettings = Ext.create({
                xtype: 'panel',
                layout: 'border',
                id: 'product-mapping-tab' + this.object.id,
                title: t('Product Mapping'),
                iconCls: 'pimcore_icon_fieldset',
                disabled: false
            });
        }
        console.log(this.mappingSettings);
        const data = this.object.data.data;
        if (data.productClass) {
            this.reloadColumnMapping(idObject);
        }

        return this.mappingSettings;
    },

    reloadColumnMapping: function (idObject) {
        if (this.mappingSettings) {
            this.mappingSettings.removeAll();
            this.mappingSettings.enable();
            Ext.Ajax.request({
                url: '/admin/object-mapper/get-columns-product',
                params: {
                    configurationId: this.object.id,
                    objectId: idObject
                },
                method: 'GET',
                success: function (result) {
                    var config = Ext.decode(result.responseText);
                    var gridStoreData = [];

                    var fromColumnStore = new Ext.data.Store({
                        fields: [
                            'identifier',
                            'label'
                        ],
                        collapsible: false,
                        data: config.fromColumns
                    });

                    var strategiesColumnStore = new Ext.data.Store({
                        fields: [
                            'identifier',
                            'label'
                        ],
                        collapsible: false,
                        data: config.strategies
                    });

                    var thumbnailsStore = new Ext.data.Store({
                        fields: [
                            'name',
                        ],
                        collapsible: false,
                        data: config.thumbnails,
                        listeners: {
                            load: function(store, records) {
                                store.insert(0, [{
                                    name: '(Empty)',
                                }]);
                            }
                        }
                    });

                    if (typeof config.toColumns == 'undefined') {
                        config.toColumns = [];
                    }
                    var toColumnStore = new Ext.data.Store({
                        data: config.toColumns
                    });
                    var gridStore = new Ext.data.Store({
                        grouper: {

                            groupFn: function (item) {
                                var rec = toColumnStore.findRecord('identifier', item.data.toColumn);
                                if (rec) {
                                    return rec.data.group;
                                }
                            }
                        },
                        fields: [
                            'fromColumn',
                            'toColumn',
                            'strategies',
                            'attributes',
                            'primaryIdentifier'
                        ]
                    });

                    config.toColumns.forEach(function (col) {
                        gridStoreData.push({
                            toColumn: col.id
                        });
                    });
                    pimcore.globalmanager.add('mapping_config_' + this.object.id, config);
                    gridStore.loadRawData(config.mapping);

                    var cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing');

                    var grid = Ext.create({
                        xtype: 'grid',
                        region: 'center',
                        store: gridStore,
                        plugins: [cellEditingPlugin],
                        features: [{
                            ftype: 'grouping',
                            groupHeaderTpl: '{name}',
                            collapsible: false
                        }],
                        object: this.object,
                        tbar: this.getToolbar(),
                        columns: {
                            defaults: {},
                            items: [
                                {
                                    text: t('Pimcore fields'),
                                    dataIndex: 'fromColumn',
                                    flex: 1,
                                    renderer: function (val) {
                                        if (val) {
                                            var rec = fromColumnStore.findRecord('identifier', val, 0, false, false, true);

                                            if (rec) {
                                                return rec.get('label');
                                            }
                                        }

                                        return val;
                                    },
                                    editor: {
                                        xtype: 'combo',
                                        store: fromColumnStore,
                                        mode: 'local',
                                        displayField: 'label',
                                        valueField: 'identifier',
                                        object: this.object,
                                        editable: true,
                                        listeners: {
                                            focus: function (comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "(Empty)") {
                                                    comp.setValue(null);
                                                }
                                            },
                                            select: function (comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "(Empty)") {
                                                    comp.setValue(null);
                                                }
                                            },
                                            change: function (combo, newValue, oldValue, eOpts) {
                                                var gridRecord = combo.up('grid').getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];

                                                    var fromColumn = fromColumnStore.findRecord('identifier', newValue, 0, false, false, true);
                                                    var row = grid.store.indexOf(gridRecord);
                                                    if (typeof(row) !== 'undefined' && row != null) {
                                                        if (fromColumn) {
                                                            newValue = fromColumn.data.identifier;
                                                        }
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        array[row][0] = newValue;
                                                        this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                        this.object.edit.dataFields.productMapping.dirty = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Magento fields'),
                                    dataIndex: 'toColumn',
                                    flex: 1,
                                    renderer: function (val, metadata) {
                                        var rec = toColumnStore.findRecord('identifier', val, 0, false, false, true);
                                        if (rec) {
                                            if (rec.data.config['required']) {
                                                metadata.tdCls = 'pimcore_icon_inputQuantityValue td-icon mapper-required-field';
                                            } else {
                                                metadata.tdCls = 'pimcore_icon_' + rec.data.fieldtype + ' td-icon';
                                            }

                                            return rec.data.label;
                                        }
                                        return val;
                                    },
                                    editor: {
                                        xtype: 'textfield',
                                        mode: 'local',
                                        object: this.object,
                                        listeners: {
                                            change: function (combo, newValue, oldValue, eOpts) {
                                                var gridRecord = grid.getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];
                                                    var row = grid.store.indexOf(gridRecord);
                                                    if (row && !this.requiredFields.includes(row[1])) {
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        array[row][1] = newValue;
                                                        this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                        this.object.edit.dataFields.productMapping.dirty = true;
                                                    }
                                                }
                                            }.bind(this)
                                        }
                                    }
                                },
                                {
                                    text: t('Strategies'),
                                    dataIndex: 'strategy',
                                    flex: 1,
                                    renderer: function (val) {
                                        if (val) {
                                            var rec = strategiesColumnStore.findRecord('identifier', val, 0, false, false, true);

                                            if (rec) {
                                                return rec.get('label');
                                            }
                                        }

                                        return null;
                                    },
                                    editor: {
                                        xtype: 'combo',
                                        store: strategiesColumnStore,
                                        mode: 'local',
                                        displayField: 'label',
                                        valueField: 'identifier',
                                        object: this.object,
                                        editable: true,
                                        listeners: {
                                            change: function (combo, newValue, oldValue, eOpts) {
                                                var gridRecord = combo.up('grid').getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];
                                                    gridRecord.strategiesColumnStore = strategiesColumnStore;

                                                    var strategy = strategiesColumnStore.findRecord('identifier', newValue, 0, false, false, true);
                                                    var row = grid.store.indexOf(gridRecord);
                                                    if (typeof(row) !== 'undefined' && row != null) {
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        if (strategy) {
                                                            newValue = strategy.data.identifier;
                                                        } else {
                                                            newValue = '';
                                                        }
                                                        array[row][2] = newValue;
                                                        if (newValue === '') {
                                                            array[row][3] = "";
                                                        }
                                                        this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                        this.object.edit.dataFields.productMapping.dirty = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Relation attributes'),
                                    dataIndex: 'attributes',
                                    flex: 1,
                                    editable: false,
                                    editor: {
                                        xtype: 'textfield',
                                        mode: 'local',
                                        object: this.object,
                                        listeners: {
                                            change: function (combo, newValue, oldValue, eOpts) {
                                                var gridRecord = grid.getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];
                                                    var row = grid.store.indexOf(gridRecord);
                                                    if (typeof(row) !== 'undefined' && row != null) {
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        array[row][3] = newValue;
                                                        this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                        this.object.edit.dataFields.productMapping.dirty = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Thumbnail'),
                                    dataIndex: 'thumbnail',
                                    flex: 1,
                                    renderer: function (val) {
                                        if (val) {
                                            var rec = thumbnailsStore.findRecord('name', val, 0, false, false, true);

                                            if (rec) {
                                                return rec.get('name');
                                            }
                                        }

                                        return null;
                                    },
                                    editor: {
                                        xtype: 'combo',
                                        store: thumbnailsStore,
                                        mode: 'local',
                                        displayField: 'name',
                                        valueField: 'name',
                                        object: this.object,
                                        editable: true,
                                        listeners: {
                                            focus: function (comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "(Empty)") {
                                                    comp.setValue(null);
                                                }
                                            },
                                            select: function (comp, record, index) {
                                                if (comp.getValue() === "" || comp.getValue() === "(Empty)") {
                                                    comp.setValue(null);
                                                }
                                            },
                                            change: function (combo, newValue, oldValue, eOpts) {
                                                var gridRecord = combo.up('grid').getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];
                                                    gridRecord.thumbnailsStore = thumbnailsStore;

                                                    var thumbnail = thumbnailsStore.findRecord('name', newValue, 0, false, false, true);
                                                    var row = grid.store.indexOf(gridRecord);
                                                    if (typeof(row) !== 'undefined' && row != null) {
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        if (thumbnail) {
                                                            newValue = thumbnail.data.name;
                                                        } else {
                                                            newValue = '';
                                                        }
                                                        array[row][4] = newValue;
                                                        this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                        this.object.edit.dataFields.productMapping.dirty = true;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Searchable'),
                                    dataIndex: 'searchable',
                                    xtype: 'checkcolumn',
                                    editable: true,
                                    object: this.object,
                                    listeners: {
                                        checkChange: function (column, rowIndex, checked, record, eOpts) {
                                            gridRecord = grid.getStore().getAt(rowIndex);
                                            if (gridRecord) {
                                                var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                if (toColumn) {
                                                    var array = this.object.edit.dataFields.productMapping.getValue();
                                                    array[rowIndex][5] = checked;
                                                    this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                    this.object.edit.dataFields.productMapping.dirty = true;
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Filterable'),
                                    dataIndex: 'filterable',
                                    xtype: 'checkcolumn',
                                    editable: true,
                                    object: this.object,
                                    listeners: {
                                        checkChange: function (column, rowIndex, checked, record, eOpts) {
                                            gridRecord = grid.getStore().getAt(rowIndex);
                                            if (gridRecord) {
                                                var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                if (toColumn) {
                                                    var array = this.object.edit.dataFields.productMapping.getValue();
                                                    array[rowIndex][6] = checked;
                                                    this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                    this.object.edit.dataFields.productMapping.dirty = true;
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Comparable'),
                                    dataIndex: 'comparable',
                                    xtype: 'checkcolumn',
                                    editable: true,
                                    object: this.object,
                                    listeners: {
                                        checkChange: function (column, rowIndex, checked, record, eOpts) {
                                            gridRecord = grid.getStore().getAt(rowIndex);
                                            if (gridRecord) {
                                                var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                if (toColumn) {
                                                    var array = this.object.edit.dataFields.productMapping.getValue();
                                                    array[rowIndex][7] = checked;
                                                    this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                    this.object.edit.dataFields.productMapping.dirty = true;
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Visible on front'),
                                    dataIndex: 'visible_on_front',
                                    xtype: 'checkcolumn',
                                    editable: true,
                                    object: this.object,
                                    listeners: {
                                        checkChange: function (column, rowIndex, checked, record, eOpts) {
                                            gridRecord = grid.getStore().getAt(rowIndex);
                                            if (gridRecord) {
                                                var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                if (toColumn) {
                                                    var array = this.object.edit.dataFields.productMapping.getValue();
                                                    array[rowIndex][8] = checked;
                                                    this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                    this.object.edit.dataFields.productMapping.dirty = true;
                                                }
                                            }
                                        }
                                    }
                                },
                                {
                                    text: t('Used in product listing'),
                                    dataIndex: 'used_in_product_listing',
                                    xtype: 'checkcolumn',
                                    editable: true,
                                    object: this.object,
                                    listeners: {
                                        checkChange: function (column, rowIndex, checked, record, eOpts) {
                                            gridRecord = grid.getStore().getAt(rowIndex);
                                            if (gridRecord) {
                                                var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                if (toColumn) {
                                                    var array = this.object.edit.dataFields.productMapping.getValue();
                                                    array[rowIndex][9] = checked;
                                                    this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                    this.object.edit.dataFields.productMapping.dirty = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            ]
                        },
                        listeners: {
                            beforeedit: function(editor, e, eOpts) {
                                if (e.field === "toColumn"){
                                    if (this.requiredFields.includes(e.value)) {
                                        return false;
                                    }
                                }
                                return true;
                            }.bind(this),
                            rowcontextmenu: function (grid, record, tr, rowIndex, e, eOpts) {
                                var menu = new Ext.menu.Menu();
                                var data = grid.getStore().getAt(rowIndex);
                                var selectedRows = grid.getSelectionModel().getSelection();

                                if (!this.requiredFields.includes(selectedRows[0].data.toColumn) && selectedRows.length <= 1) {
                                    menu.add(new Ext.menu.Item({
                                        text: t('delete'),
                                        iconCls: "pimcore_icon_delete",
                                        handler: function (data) {
                                            var selectedRows = grid.getSelectionModel().getSelection();
                                            var selectedColumn = (selectedRows[0].data.toColumn);
                                            Ext.Ajax.request({
                                                url: '/admin/mappings/remove-row/product',
                                                method: 'post',
                                                params: {
                                                    id: this.object.id,
                                                    toColumn: selectedColumn
                                                },
                                                success: function (response) {
                                                    response = JSON.parse(response.responseText)
                                                    if (response.success) {
                                                        this.object.reload();
                                                    } else {
                                                        Ext.MessageBox.alert(t("An error occurred"), t(response.message));
                                                    }
                                                }.bind(this),
                                            });
                                    }.bind(this, grid, data)
                                    }));
                                }

                                pimcore.plugin.broker.fireEvent("prepareOnRowContextmenu", menu, this, selectedRows);

                                e.stopEvent();
                                menu.showAt(e.pageX, e.pageY);
                            }.bind(this)
                        },
                    });
                    this.mappingSettings.add(grid);

                }.bind(this)
            });
        }
    },
    getToolbar: function () {
        this.addRow = new Ext.Button({
            iconCls: 'pimcore_icon_add',
            text: '<b>' + t("Add row") + '</b>',
            tooltip: t("Add new row"),
            handler: function () {
                Ext.Ajax.request({
                    url: '/admin/mappings/add-row/product',
                    method: 'post',
                    params: {
                        id: this.object.id
                    },
                    success: function(){
                        this.object.reload();
                    }.bind(this),
                    failure: function (e) {
                        Ext.MessageBox.alert(t("An error occured"));
                    }.bind(this)
                });
            }.bind(this)
        });

        this.deleteRow = new Ext.Button({
            iconCls: 'pimcore_icon_delete',
            text: '<b>' + t("Remove row") + '<b>',
            tooltip: t("Remove selected row"),
            handler: function () {
                Ext.Ajax.request({
                    url: '/admin/mappings/remove-row/product',
                    method: 'post',
                    params: {
                        id: this.object.id
                    },
                    success: function(){
                        this.object.reload();
                    }.bind(this),
                    failure: function (e) {
                        Ext.MessageBox.alert(t("An error occured"));
                    }.bind(this)
                });
            }.bind(this)
        });

        return new Ext.Toolbar({
            scrollable: "x",
            items: [
                this.addRow, "-",
                this.deleteRow,
            ]
        });
    },
});
