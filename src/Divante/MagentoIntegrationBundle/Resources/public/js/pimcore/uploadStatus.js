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
    return this.object.data.properties['synchronize-status'].data;
},

    refreshStatus: function (completed, total) {
        this.getStatusBar().html = this.getSyncStatus();
    },

    getStatusBar: function () {
        if (!this.uploadStatus) {
            this.uploadStatus = Ext.toolbar.TextItem({html: this.getSyncStatus()});
            this.object.toolbar.add('-');
            this.object.toolbar.add(this.uploadStatus);
        }
        return this.uploadStatus;
    }
});
