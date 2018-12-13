<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        15/06/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Model\Webservice\Data\DataObject\Concrete;

use Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data\Localizedfields;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\Webservice\Data\DataObject\Element;
use Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data\Classificationstore;
use Divante\MagentoIntegrationBundle\Model\DataObject\ClassDefinition\Data\Objectbricks;
/**
 * Class Out
 * @package Divante\MagentoIntegrationBundle\Model\Webservice\Data\DataObject\Concrete
 */
class Out extends \Pimcore\Model\Webservice\Data\DataObject\Concrete\Out
{

    /**
     * @inheritdoc
     */
    public function map($object, $options = null)
    {
        parent::map($object);

        $this->className = $object->getClassName();

        $fd = $object->getClass()->getFieldDefinitions();
        $this->elements = [];
        foreach ($fd as $field) {
            $getter = 'get'.ucfirst($field->getName());

            //only expose fields which have a get method
            if (method_exists($object, $getter)) {
                $el = new Element();
                $el->name  = $field->getName();
                $el->type  = $field->getFieldType();
                $el->label = $field->getTitle();
                $el->value = $this->getFieldValue($field, $object);
                if ($el->value == null && self::$dropNullValues) {
                    continue;
                }
                $this->elements[] = $el;
            }
        }
    }

    /**
     * @param Data $field
     * @param      $object
     * @return array|mixed|null
     */
    public function getFieldValue(Data $field, $object)
    {
        if ($field instanceof Data\Localizedfields) {
            $field = new Localizedfields($field);
        } elseif ($field instanceof Data\Classificationstore) {
            $field = new Classificationstore($field);
        } elseif ($field instanceof Data\Objectbricks) {
            $field = new Objectbricks($field);
        }
        try {
            return $field->getForWebserviceExport($object);
        } catch (\Exception $e) {
            return null;
        }
    }
}
