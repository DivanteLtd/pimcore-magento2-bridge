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
            /**
             * PRODUCTS SELECT LISTENER
             */
            var tabs = Ext.getCmp('object_' + object.id);
            object.edit.dataFields.productClass.component.on("select", function(data) {
                var productKey = 'mapper_product_' + object.id;
                var productValue = new pimcore.plugin.MagentoIntegrationBundle.ProductMapper(object);
                pimcore.globalmanager.add(productKey, productValue);
                var productMappingTab = tabs.object.tab.items.items[1].getComponent("product-mapping-tab");
                tabs.object.tab.items.items[1].remove(productMappingTab);
                if (data.value !== "") {
                    tabs.object.tab.items.items[1].insert(7, productValue.getLayout(data.value));
                } else {
                    tabs.object.tab.items.items[1].insert(7, productValue.getEmptyHiddenLayout());
                }
            });

            /**
             * PRODUCTS ON POST OPEN
             */
            var productClassId = object.edit.dataFields.productClass.component.value;
            var productKey = 'mapper_product_' + object.id;
            var productValue = new pimcore.plugin.MagentoIntegrationBundle.ProductMapper(object);
            if (productClassId !== "") {
                pimcore.globalmanager.add(productKey, productValue);
                tabs.object.tab.items.items[1].insert(7, productValue.getLayout(productClassId));
                this.addSendAllProductsButton(object);
            } else {
                tabs.object.tab.items.items[1].insert(7, productValue.getEmptyHiddenLayout());
            }

            /**
             * CATEGORY SELECT LISTENER
             */
            object.edit.dataFields.categoryClass.component.on("select", function(data) {
                var categoryKey = 'mapper_category_' + object.id;
                var categoryValue = new pimcore.plugin.MagentoIntegrationBundle.CategoryMapper(object);
                pimcore.globalmanager.add(categoryKey, categoryValue);
                var categoryMappingTabs = tabs.object.tab.items.items[1].getComponent("category-mapping-tab");
                tabs.object.tab.items.items[1].remove(categoryMappingTabs);
                if (data.value !== "") {
                    tabs.object.tab.items.items[1].insert(8, categoryValue.getLayout(data.value));
                } else {
                    tabs.object.tab.items.items[1].insert(8, categoryValue.getEmptyHiddenLayout());
                }
            });

            /**
             * CATEGORY ON POST OPEN
             */
            var categoryKey = 'mapper_category_' + object.id;
            var categoryValue = new pimcore.plugin.MagentoIntegrationBundle.CategoryMapper(object);
            var categoryClassId = object.edit.dataFields.categoryClass.component.value;
            if (categoryClassId !== "") {
                pimcore.globalmanager.add(categoryKey, categoryValue);
                tabs.object.tab.items.items[1].insert(8, categoryValue.getLayout(categoryClassId));
                this.addSendAllCategoriesButton(object);
            } else {
                tabs.object.tab.items.items[1].insert(8, categoryValue.getEmptyHiddenLayout());
            }
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
            if (columnsConfig) {
                columnsConfig.filter(function (element) {
                    return element.config.required
                });
            }
        }

    },
    postSaveObject: function (object) {
        if (this.isConfiguration(object)) {
            var productClassId = object.edit.dataFields.productClass.component.value;
            var categoryClassId = object.edit.dataFields.categoryClass.component.value;
            var key = 'mapper_product_' + object.id;
            var value = pimcore.globalmanager.get(key);
            if (productClassId) {
                value.reloadMapper(object, productClassId);
            }
            var key = 'mapper_category_' + object.id;
            var value = pimcore.globalmanager.get(key);
            if (categoryClassId) {
                value.reloadMapper(object, categoryClassId);
            }
        }
    },
    isConfiguration: function (object) {
        return 'IntegrationConfiguration' === object.data.general.o_className;
    },

    isSynchronized: function (object) {
        return ("synchronize_status" in  object.data.properties);
    },

    addSendAllProductsButton: function (object) {
        object.toolbar.add({
            text: t('Send products'),
            iconCls: 'pimcore_icon_right',
            scale: 'small',
            handler: function () {
                Ext.Ajax.request({
                    url: '/admin/integration-configuration/send/products',
                    method: 'post',
                    params: {
                        id: object.id,
                        storeViewId: object.data.data.magentoStore,
                        instanceUrl: object.data.data.instanceUrl
                    },
                    success: function(){
                        pimcore.helpers.showNotification(t("Success!"), t("Mass update of products has been started"),
                            "success");
                    }.bind(this),
                    failure: function () {
                        console.log(arguments)
                    }.bind(this)
                });
            }.bind(this)
        });
        pimcore.layout.refresh();
    },

    addSendAllCategoriesButton: function (object) {
        object.toolbar.add({
            text: t('Send categories'),
            iconCls: 'pimcore_icon_right',
            scale: 'small',
            handler: function () {
                Ext.Ajax.request({
                    url: '/admin/integration-configuration/send/categories',
                    method: 'post',
                    params: {
                        id: object.id,
                        storeViewId: object.data.data.magentoStore,
                        instanceUrl: object.data.data.instanceUrl
                    },
                    success: function(){
                        pimcore.helpers.showNotification(t("Success!"), t("Mass update of categories has been started"),
                            "success");
                    }.bind(this),
                    failure: function () {
                        console.log(arguments)
                    }.bind(this)
                });
            }.bind(this)
        });
        pimcore.layout.refresh();
    }
});

var MagentoIntegrationBundle = new pimcore.plugin.MagentoIntegrationBundle();
