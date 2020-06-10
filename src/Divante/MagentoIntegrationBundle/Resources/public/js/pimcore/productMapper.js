pimcore.registerNS("pimcore.plugin.MagentoIntegrationBundle.ProductMapper");
pimcore.plugin.MagentoIntegrationBundle.ProductMapper = Class.create(pimcore.plugin.MagentoIntegrationBundle.item, {
    initialize: function (object) {
        this.object = object;
    },
    reloadMapper: function (object) {
        this.object = object;
        this.reloadColumnMapping();
    },

    getLayout: function () {
        if (!this.mappingSettings) {
            this.mappingSettings = Ext.create({
                xtype: 'panel',
                layout: 'border',
                title: t('Product Mapping'),
                iconCls: 'pimcore_icon_fieldset',
                disabled: false
            });
        }
        const data = this.object.data.data;
        if (data.productClass) {
            this.reloadColumnMapping();
        }

        return this.mappingSettings;
    },
    reloadColumnMapping: function () {
        if (this.mappingSettings) {
            this.mappingSettings.removeAll();
            this.mappingSettings.enable();
            Ext.Ajax.request({
                url: '/admin/object-mapper/get-columns-product',
                params: {
                    configurationId: this.object.id
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

                                        return null;
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
                                            change: function (combo, newValue, oldValue, eOpts) {
                                                var gridRecord = combo.up('grid').getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];
                                                    gridRecord.fromColumnStore = fromColumnStore;

                                                    var fromColumn = fromColumnStore.findRecord('identifier', newValue, 0, false, false, true);
                                                    var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                    if (toColumn) {
                                                        gridRecord.data['fromColumn'] = fromColumn;
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        var value = '';
                                                        if (fromColumn) {
                                                            value = fromColumn.data.identifier;
                                                        }
                                                        array.find(function (value) {
                                                            return value[1] === toColumn.data.identifier})[0] = value;
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
                                                    var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                    if (toColumn) {
                                                        gridRecord.data['strategies'] = strategy;
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        var value = '';
                                                        if (strategy) {
                                                            value = strategy.data.identifier;
                                                        }
                                                        array.find(function (value) {
                                                            return value[1] === toColumn.data.identifier})[2] = value;
                                                        if (value == "") {
                                                            array.find(function (value) {
                                                                return value[1] === toColumn.data.identifier})[3] = "";
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
                                                    var toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                    if (toColumn) {
                                                        var array = this.object.edit.dataFields.productMapping.getValue();
                                                        array.find(function (newValue) {
                                                            return newValue[1] === toColumn.data.identifier
                                                        })[3] = newValue;
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
                                                    array[rowIndex][4] = checked;
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
                                                    array[rowIndex][5] = checked;
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
                                                    array[rowIndex][6] = checked;
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
                                                    array[rowIndex][7] = checked;
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
                                                    array[rowIndex][8] = checked;
                                                    this.object.edit.dataFields.productMapping.store.loadData(array, false);
                                                    this.object.edit.dataFields.productMapping.dirty = true;
                                                }
                                            }
                                        }
                                    }
                                }
                            ]
                        }
                    });
                    this.mappingSettings.add(grid);

                }.bind(this)
            });
        }
    },

});
