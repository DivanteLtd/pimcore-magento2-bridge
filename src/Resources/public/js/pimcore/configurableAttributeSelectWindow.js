pimcore.registerNS("pimcore.plugin.MagentoIntegrationBundle.configurableAttributeSelectWindow");

pimcore.plugin.MagentoIntegrationBundle.configurableAttributeSelectWindow = Class.create({

    initialize: function (parent, record) {
        this.parent = parent;
        this.record = record;
},

getWindow: function () {

    if (!this.window) {
        this.window = new Ext.window.Window({
            width: 800,
            height: 700,
            modal: true,
            resizeable: true,
            layout: 'fit',
            title: t('configurable_attributes'),
            items: [
                {
                    xtype: 'panel',
                    border: false,
                    layout: 'border',
                    items: [
                        this.getClassesPanel(),
                        this.getValidationPanel()
                    ]
            }
            ]
            });
    }

    return this.window;
},

    getClassesPanel: function () {

        if (!this.classesPanel) {
            let children = [];
            if (this.parent.type === 'object') {
                let classId = this.parent.element.data.general.o_classId;
                let record = this.getObjectTypesStore().getById(classId);
                if (record) {
                    children.push({
                        id: record.get('id'),
                        text: record.get('text'),
                        icon: record.get('icon'),
                        leaf: true
                    });
                }

                children.sort(function (a, b) {
                    return a.text > b.text;
                });
            }
            let store = Ext.create('Ext.data.TreeStore', {
                root: {
                    id: 0,
                    expanded: true,
                    children: children
                }
            });

            this.classesPanel = Ext.create('Ext.tree.Panel', {
                store: store,
                rootVisible: false,
                region: 'west',
                autoScroll: true,
                animate: false,
                containerScroll: true,
                width: 200,
                split: true,
                listeners: {
                    itemclick: this.loadValidationRules.bind(this)
                }
            });
        }

        return this.classesPanel;
    },


    getValidationPanel: function () {

        if (!this.validationPanel) {
            this.validationPanel = Ext.create('Ext.panel.Panel', {
                region: 'center',
                layout: 'fit'
            });
        }

        return this.validationPanel;
    },

    loadValidationRules: function (panel, record, item, index, e, eOpts) {
        const classId =  this.parent.element.data.general.o_classId;

        if (!Ext.getCmp('workflow_validation_rules_panel_' + classId)) {
            this.getValidationPanel().removeAll(true);
            this.getValidationPanel().setLoading(true);

            Ext.Ajax.request({
                url: '/admin/class/get',
                params: {
                    id: classId
                },
                success: this.addValidationRulesPanel.bind(this, classId)
            });
        }
    },

    addValidationRulesPanel: function (classId, response) {
        let data = Ext.decode(response.responseText);

        const validationRulesPanel = Ext.create('Ext.tree.Panel', {
            id: 'workflow_validation_rules_panel_' + classId,
            autoScroll: true,
            root: {
                id: "0",
                root: true,
                text: t("base"),
                leaf: true,
                iconCls: "pimcore_icon_class",
                isTarget: true
            },
            listeners: {
                checkchange: function (node, checked, eOpts) {
                    this.save(classId, validationRulesPanel.getChecked());
                }.bind(this)
            }
        });

        this.getValidationPanel().setLoading(false);
        this.getValidationPanel().add(validationRulesPanel);

        if (data.layoutDefinitions) {
            if (data.layoutDefinitions.childs) {
                for (let i = 0; i < data.layoutDefinitions.childs.length; i++) {
                    validationRulesPanel.getRootNode().appendChild(
                        this.recursiveAddNode(
                            data.layoutDefinitions.childs[i],
                            this.getChecked(classId),
                            validationRulesPanel.getRootNode()
                        )
                    );
                }
                validationRulesPanel.getRootNode().expand();
            }
        }
    },

    recursiveAddNode: function (con, checked, scope) {
        let fn = null;

        if (con.datatype === "layout") {
            fn = this.addLayoutChild.bind(scope, con.fieldtype, con);
        } else if (con.datatype === "data") {
            if (con.fieldtype === 'select') {
                fn = this.addDataChild.bind(scope, con.fieldtype, con, checked);
            } else if (con.fieldtype === "classificationstore") {
                fn = this.addLayoutChild.bind(scope, con.fieldtype, con);
            }
        }
        if (fn == null) {
            return null;
        }
        let newNode = fn();
        if (con.fieldtype === 'classificationstore') {
            if (this.parent.element.edit.object.edit.dataFields[con.name]) {
                let classificationStoreConfig = this.parent.element.edit.object.edit.dataFields[con.name];
                let groups = [];
                for (let i = 0; i < classificationStoreConfig.languageElements.default.length; i++) {
                    let elem = classificationStoreConfig.languageElements.default[i];
                    if (!groups[elem.fieldConfig.csGroupId]) {
                        groups[elem.fieldConfig.csGroupId] = [];
                    }
                    groups[elem.fieldConfig.csGroupId].push(elem.fieldConfig);
                }
                for (let groupId in groups) {
                    if (!classificationStoreConfig.groupElements.default[groupId]) {
                        continue;
                    }
                    let groupTitle = classificationStoreConfig.groupElements.default[groupId].config.title;
                    let childs = [];
                    for (let i = 0; i < groups[groupId].length; i++) {
                        let child = groups[groupId][i];
                        childs.push({
                            'childs': null,
                            'collapsed': false,
                            'collapsible': false,
                            'datatype': 'data',
                            'fieldtype': child.fieldtype,
                            'locked': false,
                            'name': con.name + '_' + groupTitle + '_' + child.name
                        });
                    }
                    let groupCon = {
                        'childs': childs,
                        'collapsed': false,
                        'collapsible': true,
                        'datatype': 'layout',
                        'fieldtype': 'panel',
                        'locked': false,
                        'name': groupTitle
                    };
                    this.recursiveAddNode(groupCon, checked, newNode);
                }
            } else {
                const classificationStoreLayout = this.recursiveSearchNode(this.parent.element.edit.object.data.layout, con.name);
                for (let groupCode in classificationStoreLayout.activeGroupDefinitions) {
                    let group = classificationStoreLayout.activeGroupDefinitions[groupCode];
                    let children = [];
                    for (let i = 0; i < group.keys.length; i++) {
                        let child = group.keys[i];
                        children.push({
                            'childs': null,
                            'collapsed': false,
                            'collapsible': false,
                            'datatype': 'data',
                            'fieldtype': child.definition.fieldtype,
                            'locked': false,
                            'name': con.name + '_' + group.name + '_' + child.name
                        });
                    }
                    var groupCon = {
                        'childs': children,
                        'collapsed': false,
                        'collapsible': true,
                        'datatype': 'layout',
                        'fieldtype': 'panel',
                        'locked': false,
                        'name': group.name
                    };
                    this.recursiveAddNode(groupCon, checked, newNode);
                }
            }
        }
        if (con.childs) {
            for (let i = 0; i < con.childs.length; i++) {
                this.recursiveAddNode(con.childs[i], checked, newNode);
            }
        }

        return newNode;
    },
    recursiveSearchNode: function (node, targetName) {
        if (node == null) {
            return null;
        }
        if (node.name === targetName) {
            return node;
        }
        if (node.datatype === 'data') {
            return null;
        }
        let elem = null;
        for (var i = 0; i < node.childs.length; i++) {
            elem = this.recursiveSearchNode(node.childs[i], targetName);
            if (elem !== null) {
                return elem;
            }
        }
        return elem;
    },

    addLayoutChild: function (type, initData) {

        let nodeLabel = t(type);

        if (initData) {
            if (initData.name) {
                nodeLabel = initData.name;
            }
        }

        let newNode = {
            text: nodeLabel,
            value: nodeLabel,
            type: "layout",
            iconCls: "pimcore_icon_" + type,
            leaf: false,
            expandable: false,
            expanded: true
        };

        newNode = this.appendChild(newNode);

        newNode.addListener('remove', function (node, removedNode, isMove) {
            if (!node.hasChildNodes()) {
                node.set('expandable', false);
            }
        });

        newNode.addListener('append', function (node) {
            node.set('expandable', true);
        });

        this.expand();
        return newNode;
    },

    addDataChild: function (type, initData, checked) {

        let nodeLabel = t(type);

        if (initData) {
            if (initData.name) {
                nodeLabel = initData.name;
            }
        }

        let prefix = '';
        if (!this.data.root && this.data.type === 'data') {
            prefix = this.data.value + '.';
        }

        let newNode = {
            text: nodeLabel,
            value: prefix + nodeLabel,
            type: "data",
            leaf: true,
            iconCls: "pimcore_icon_" + type
        };

        if (type === "localizedfields") {
            newNode.leaf = false;
            newNode.expanded = true;
            newNode.expandable = false;
        } else {
            newNode.checked = false;
        }

        if (checked.includes(newNode.value)) {
            newNode.checked = true;
        }

        newNode = this.appendChild(newNode);

        this.expand();

        return newNode;
    },

    getChecked: function (classId) {
        const validation = this.record.data;
        if (validation !== "") {
            return validation.split(",");
        }
        return [];
    },

    save: function (classId, checked) {
        let rules = [];
        for (let i = 0; i < checked.length; i++) {
            rules.push(checked[i].get('value'));
        }
        this.record.data = rules.join(',');
        this.parent.propertyGrid.getView().refresh()
    },

    show: function () {
        this.getWindow().show();
    },

    getObjectTypesStore: function () {
        if (!this.objectTypeStore) {
            this.objectTypeStore = pimcore.globalmanager.get("object_types_store");
        }
        return this.objectTypeStore;
    }
});
