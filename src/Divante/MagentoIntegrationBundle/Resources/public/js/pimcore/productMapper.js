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
                            ]
                        }
                    });
                    this.mappingSettings.add(grid);

                }.bind(this)
            });
        }
    },

});
