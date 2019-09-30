/**
 * @category    pimcore5-module-magento2-integration
 * @date        03/04/2018
 * @author      Michal Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
pimcore.registerNS("pimcore.plugin.MagentoIntegrationBundle");

pimcore.plugin.MagentoIntegrationBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.MagentoIntegrationBundle";
},

initialize: function () {
    pimcore.plugin.broker.registerPlugin(this);
},

    pimcoreReady: function (params, broker) {
    },

    postOpenObject: function (object, type) {
        if (this.isConfiguration(object)) {
            var key = 'mapper_product_' + object.id;
            var value = new pimcore.plugin.MagentoIntegrationBundle.ProductMapper(object);
            pimcore.globalmanager.add(key, value);
            var tabs = Ext.getCmp('object_' + object.id);
            tabs.object.tab.items.items[1].add(value.getLayout());
            key = 'mapper_category_' + object.id;
            value = new pimcore.plugin.MagentoIntegrationBundle.CategoryMapper(object);
            pimcore.globalmanager.add(key, value);
            tabs = Ext.getCmp('object_' + object.id);
            tabs.object.tab.items.items[1].add(value.getLayout());
        }
        if (this.isSynchronized(object)) {
            var key = 'magentostatus_' + object.id;
            var value = new pimcore.plugin.MagentoIntegrationBundle.UploadStatus(object);
            pimcore.globalmanager.add(key, value);
        }
    },

    postOpenAsset: function (asset, type) {
        if (this.isSynchronized(asset)) {
            var key = 'magentostatus_' + asset.id;
            var value = new pimcore.plugin.MagentoIntegrationBundle.UploadStatus(asset);
            pimcore.globalmanager.add(key, value);
        }
    },

    preSaveObject: function (object) {
        if (this.isConfiguration(object)) {
            object.edit.dataFields.productMapping.getValue();
            var columnsConfig = pimcore.globalmanager.get('mapping_config_' + object.id).toColumns;
            columnsConfig.filter(function (element) {
                return element.config.required
            });
        }

    },
    postSaveObject: function (object) {
        if (this.isConfiguration(object)) {
            var key = 'mapper_product_' + object.id;
            var value = pimcore.globalmanager.get(key);
            value.reloadMapper(object);
            var key = 'mapper_category_' + object.id;
            var value = pimcore.globalmanager.get(key);
            value.reloadMapper(object);
        }
    },
    isConfiguration: function (object) {
        return 'IntegrationConfiguration' === object.data.general.o_className;
    },

    isSynchronized: function (object) {
        return ("synchronize-status" in  object.data.properties);
    }
});

var MagentoIntegrationBundle = new pimcore.plugin.MagentoIntegrationBundle();
