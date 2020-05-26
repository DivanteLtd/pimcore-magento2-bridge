/**
 * @category    pimcore5-module-magento2-integration
 * @date        03/04/2018
 * @author      Michal Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

pimcore.registerNS("pimcore.plugin.MagentoIntegrationBundle.UploadStatus");

pimcore.plugin.MagentoIntegrationBundle.UploadStatus = Class.create({
    initialize: function (object) {
        this.object = object;
        this.getStatusBar();
        this.refreshStatus();
    },

    getSyncStatus: function () {
        try {
            return JSON.parse(this.object.data.properties['synchronize_status'].data);
        } catch (e) {
            return {"Magento: ":this.object.data.properties['synchronize_status'].data};
        }
    },

    refreshStatus: function (completed, total) {
        this.getStatusBar().html = this.getSyncStatus();
    },

    getStatusBar: function () {
        if (!this.uploadStatus) {
            var syncStatus = this.getSyncStatus();
            var statusText = syncStatus[Object.keys(syncStatus)[0]];
            var menu = [];
            Object.keys(syncStatus).forEach(function (key) {
                statusText = syncStatus[key] == statusText ? statusText : 'WARNING';
                var statusIcon = "";

                switch(syncStatus[key]) {
                    case 'SUCCESS':
                        statusIcon = 'pimcore_icon_save';
                        break;
                    case 'DELETED':
                        statusIcon = 'pimcore_icon_delete';
                        break;
                    case 'ERROR':
                        statusIcon = 'pimcore_icon_minus';
                        break;
                    case 'SENT':
                        statusIcon = 'pimcore_icon_overlay_upload';
                        break;
                }
                console.log(statusIcon);
                menu.push({
                    text: key + ": " + t('magento-integration.status.' + syncStatus[key]),
                    iconCls: statusIcon
                });
            });
            this.uploadStatus = new Ext.SplitButton({
                text: t('magento-integration.status.' + statusText),
                iconCls: "pimcore_material_icon_upload",
                cls: "integration_status",
                scale: "medium",
                menu: menu
            });
            this.object.toolbar.add('-');
            this.object.toolbar.add(this.uploadStatus);
        }
        return this.uploadStatus;
    }
});