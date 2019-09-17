pimcore.registerNS("pimcore.plugin.MagentoIntegrationBundle.CategoryMapper");

pimcore.plugin.MagentoIntegrationBundle.CategoryMapper = Class.create(pimcore.plugin.MagentoIntegrationBundle.item, {
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
                title: t('Category Mapping'),
                iconCls: 'pimcore_icon_fieldset',
                disabled: false
            });
        }
        const data = this.object.data.data;
        if (data.categoryClass) {
            this.reloadColumnMapping();
        }

        return this.mappingSettings;
    },

    reloadColumnMapping: function () {
        if (this.mappingSettings) {
            this.mappingSettings.removeAll();
            this.mappingSettings.enable();
            Ext.Ajax.request({
                url: '/admin/object-mapper/get-columns-category',
                params: {
                    configurationId: this.object.id
                },
                method: 'GET',
                success: function (result) {
                    const config = Ext.decode(result.responseText);
                    let gridStoreData = [];

                    const fromColumnStore = new Ext.data.Store({
                        fields: [
                            'identifier',
                            'label'
                        ],
                        data: config.fromColumns
                    });

                    if (typeof config.toColumns === 'undefined') {
                        config.toColumns = [];
                    }
                    const toColumnStore = new Ext.data.Store({
                        data: config.toColumns
                    });

                    let gridStore = new Ext.data.Store({
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

                    gridStore.loadRawData(config.mapping);

                    let cellEditingPlugin = Ext.create('Ext.grid.plugin.CellEditing');

                    let grid = Ext.create({
                        xtype: 'grid',
                        region: 'center',
                        store: gridStore,
                        plugins: [cellEditingPlugin],
                        features: [{
                            ftype: 'grouping',
                            collapsible: false,
                            groupHeaderTpl: '{name}'
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
                                            const rec = fromColumnStore.findRecord('identifier', val, 0, false, false, true);

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
                                                let gridRecord = combo.up('grid').getSelectionModel().getSelection();
                                                if (gridRecord.length > 0) {
                                                    gridRecord = gridRecord[0];
                                                    gridRecord.fromColumnStore = fromColumnStore;

                                                    const fromColumn = fromColumnStore.findRecord('identifier', newValue, 0, false, false, true);
                                                    const toColumn = toColumnStore.findRecord('identifier', gridRecord.get('toColumn'), 0, false, false, true);
                                                    if (toColumn) {
                                                        gridRecord.data['fromColumn'] = fromColumn;
                                                        const array = this.object.edit.dataFields.categoryMapping.getValue();
                                                        let value = '';
                                                        if (fromColumn) {
                                                            value = fromColumn.data.identifier;
                                                        }
                                                        array.find(function (value) {
                                                            return value[1] === toColumn.data.identifier})[0] = value;
                                                        this.object.edit.dataFields.categoryMapping.store.loadData(array, false);
                                                        this.object.edit.dataFields.categoryMapping.dirty = true;
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
                                        const rec = toColumnStore.findRecord('identifier', val, 0, false, false, true);

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
