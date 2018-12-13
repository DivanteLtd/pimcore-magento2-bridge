<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        15/06/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data;

use Pimcore\Model\DataObject;
use Pimcore\Tool;
use Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore as ClassificationstoreParent;

/**
 * Class Classificationstore
 * @package Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data
 */
class Classificationstore extends ClassificationstoreParent
{
    /**
     * Classificationstore copy constructor.
     * @param \Pimcore\Model\DataObject\ClassDefinition\Data\Classificationstore $field
     */
    public function __construct(ClassificationstoreParent $field)
    {
        foreach ($field as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * This method is copied from Pimcore core code
     * @param DataObject\AbstractObject $object
     * @param mixed                     $params
     *
     * @return array
     * @throws \Exception
     */
    public function getForWebserviceExport($object, $params = [])
    {
        /** @var $data DataObject\Classificationstore */
        $data = $this->getDataFromObjectParam($object, $params);

        if ($data) {
            if ($this->isLocalized()) {
                $validLanguages = Tool::getValidLanguages();
            } else {
                $validLanguages = [];
            }
            array_unshift($validLanguages, 'default');

            $result = [];
            $activeGroups = [];
            $items = $data->getActiveGroups();
            if (is_array($items)) {
                foreach ($items as $groupId => $groupData) {
                    $groupDef = DataObject\Classificationstore\GroupConfig::getById($groupId);
                    if (!is_null($groupDef)) {
                        $activeGroups[] = [
                            'id' => $groupId,
                            'name' => $groupDef->getName(). ' - ' . $groupDef->getDescription(),
                            'enabled' => $groupData
                        ];
                    }
                }
            }

            $result['activeGroups'] = $activeGroups;
            $items = $data->getItems();

            foreach ($items as $groupId => $groupData) {
                $groupResult = [];

                foreach ($groupData as $keyId => $keyData) {
                    $keyConfig = DataObject\Classificationstore\DefinitionCache::get($keyId);
                    $fd = DataObject\Classificationstore\Service::getFieldDefinitionFromKeyConfig($keyConfig);
                    $context = [
                        'containerType' => 'classificationstore',
                        'fieldname' => $this->getName(),
                        'groupId' => $groupId,
                        'keyId' => $keyId
                    ];

                    foreach ($validLanguages as $language) {
                        $value = $fd->getForWebserviceExport($object, ['context' => $context, 'language' => $language]);
                        $groupResult[$language][] = [
                            'id' => $keyId,
                            'name' => $keyConfig->getName(),
                            'description' => $keyConfig->getDescription(),
                            'value' => $value,
                            'label' => $keyConfig->getTitle()
                        ];
                    }
                }

                $groupDef = DataObject\Classificationstore\GroupConfig::getById($groupId);
                if (!is_null($groupDef) && $groupResult) {
                    $groupResult = [
                        'id' => $groupId,
                        'name' => $groupDef->getName(). ' - ' . $groupDef->getDescription(),
                        'keys' => $groupResult
                    ];
                }

                $result['groups'][] = $groupResult;
            }

            return $result;
        }
        return [];
    }
}
