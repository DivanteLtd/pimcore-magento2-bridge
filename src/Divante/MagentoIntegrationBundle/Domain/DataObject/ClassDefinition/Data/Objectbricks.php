<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain\DataObject\ClassDefinition\Data;

use Pimcore\Model\DataObject;
use Pimcore\Model\DataObject\ClassDefinition\Data\Objectbricks as ObjectbricksParent;
use Pimcore\Model\Webservice;

/**
 * Class Objectbricks
 * @package Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data
 */
class Objectbricks extends ObjectbricksParent
{
    /**
     * Objectbricks constructor.
     * @param ObjectbricksParent $field
     */
    public function __construct(ObjectbricksParent $field)
    {
        foreach ($field as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * This method is copied from Picmore core
     * @inheritdoc
     * @throws \Exception
     */
    public function getForWebserviceExport($object, $params = [])
    {
        $data   = $this->getDataFromObjectParam($object, $params);
        $wsData = [];

        if ($data instanceof DataObject\Objectbrick) {
            foreach ($data as $item) {
                if (!$item instanceof DataObject\Objectbrick\Data\AbstractData) {
                    continue;
                }

                $wsDataItem        = new Webservice\Data\DataObject\Element();
                $wsDataItem->value = [];
                $wsDataItem->type  = $item->getType();

                try {
                    $collectionDef = DataObject\Objectbrick\Definition::getByKey($item->getType());
                } catch (\Exception $e) {
                    continue;
                }

                foreach ($collectionDef->getFieldDefinitions() as $fd) {
                    $el        = new Webservice\Data\DataObject\Element();
                    $el->name  = $fd->getName();
                    $el->type  = $fd->getFieldType();
                    $el->label = $fd->getTitle();
                    $el->value = $fd->getForWebserviceExport($item, $params);
                    if ($el->value == null && self::$dropNullValues) {
                        continue;
                    }

                    $wsDataItem->value[] = $el;
                }

                $wsData[] = $wsDataItem;
            }
        }

        return $wsData;
    }
}
