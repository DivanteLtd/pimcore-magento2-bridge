/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @category   Pimcore
 * @package    Object
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */


pimcore.registerNS("pimcore.object.gridcolumn.operator.anypropertygetter");

pimcore.object.gridcolumn.operator.anypropertygetter = Class.create(pimcore.object.gridcolumn.Abstract, {
    type: "operator",
        class: "AnyPropertyGetter",
    iconCls: "pimcore_icon_properties",
    defaultText: "Property Getter",
    group: "getter",


    getConfigTreeNode: function (configAttributes) {
        let node = null;
        if (configAttributes) {
            const nodeLabel = this.getNodeLabel(configAttributes);
            node = {
                draggable: true,
                iconCls: this.iconCls,
                text: nodeLabel,
                configAttributes: configAttributes,
                isTarget: true,
                maxChildCount: 1,
                expanded: false,
                leaf: true,
                expandable: false
            };
        } else {
            //For building up operator list
            const configAttributes = {type: this.type, class: this.class, label: this.getDefaultText()};
            node = {
                draggable: true,
                iconCls: this.iconCls,
                text: this.getDefaultText(),
                configAttributes: configAttributes,
                isTarget: true,
                maxChildCount: 1,
                leaf: true
            };
        }
        node.isOperator = true;
        return node;
    },


    getCopyNode: function (source) {
        return source.createNode({
            iconCls: this.iconCls,
            text: source.data.cssClass,
            isTarget: true,
            leaf: true,
            maxChildCount: 1,
            expanded: false,
            isOperator: true,
            configAttributes: {
                label: source.data.configAttributes.label,
                type: this.type,
                class: this.class
            }
            });
    },


    getConfigDialog: function (node) {
        this.node = node;

        this.label = new Ext.form.TextField({
            fieldLabel: t('label'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.label
            });

        this.propertyNameField = new Ext.form.TextField({
            fieldLabel: t('Property name'),
            length: 255,
            width: 200,
            value: this.node.data.configAttributes.propertyName
            });

        this.configPanel = new Ext.Panel({
            layout: "form",
            bodyStyle: "padding: 10px;",
            items: [this.label, this.propertyNameField],
            buttons: [{
                text: t("apply"),
                iconCls: "pimcore_icon_apply",
                handler: function () {
                    this.commitData();
                }.bind(this)
            }]
            });

        this.window = new Ext.Window({
            width: 400,
            height: 450,
            modal: true,
            title: t('settings'),
            layout: "fit",
            items: [this.configPanel]
            });

        this.window.show();
        return this.window;
    },

    commitData: function () {
        this.node.set('isOperator', true);
        this.node.data.configAttributes.propertyName = this.propertyNameField.getValue();
        this.node.data.configAttributes.label = this.label.getValue();

        const nodeLabel = this.getNodeLabel(this.node.data.configAttributes);
        this.node.set('text', nodeLabel);
        this.window.close();
    },

    getNodeLabel: function (configAttributes) {
        let nodeLabel = configAttributes.label ? configAttributes.label : this.getDefaultText();
        if (configAttributes.attribute) {
            let attr = configAttributes.attribute;
            if (configAttributes.param1) {
                attr += " " + configAttributes.param1;
            }
            nodeLabel += '<span class="pimcore_gridnode_hint"> (' + attr + ')</span>';
        }
        return nodeLabel;
    }
    });