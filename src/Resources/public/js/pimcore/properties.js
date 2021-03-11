pimcore.element.properties = Class.create(pimcore.element.properties, {
    getLayout: function ($super) {

        var layout = $super();
        var columns = this.propertyGrid.getColumns();
        var actionColumn = columns[columns.length - 2];
        actionColumn.items = [{
            tooltip: t('open'),
            icon: "/bundles/pimcoreadmin/img/flat-color-icons/settings.svg",
            handler: function (grid, rowIndex) {
                var record = grid.getStore().getAt(rowIndex).data;
                if (record.name == 'configurable_attributes') {
                    // noinspection JSPotentiallyInvalidConstructorUsage
                    var window = new pimcore.plugin.MagentoIntegrationBundle.configurableAttributeSelectWindow(this, record);
                    window.show();
                } else {
                    var pData = grid.getStore().getAt(rowIndex).data;
                    if (pData.all && pData.all.data) {
                        if (pData.all.data.id) {
                            pimcore.helpers.openElement(pData.all.data.id, pData.type, pData.all.data.type);
                        } else if (pData.all.data.o_id) {
                            pimcore.helpers.openElement(
                                pData.all.data.o_id,
                                pData.type,
                                pData.all.data.o_type
                            );
                        }
                    }
                }
            }.bind(this),
            getClass: function (v, meta, rec) {
                if (rec.get('name') != 'configurable_attributes') {
                    if (rec.get('type') != "object" && rec.get('type') != "document"
                        && rec.get('type') != "asset") {
                        return "pimcore_hidden";
                    }
                } else {
                    return '';
                }
            }
        }];
        return layout;
    }
});