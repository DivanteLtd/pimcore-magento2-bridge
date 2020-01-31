<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        22/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Helper;

/**
 * Class MapperHelper
 * @package Divante\MagentoIntegrationBundle\Helper
 */
class MapperHelper
{
    const OBJECT_TYPE_PRODUCT = 'product';
    const OBJECT_TYPE_CATEGORY = 'category';
    const TEXT_TYPES = ['input', 'numeric', 'country', 'language', 'calculatedValue'];
    const TEXT_AREA_TYPES = ['textarea'];
    const WYSIWYG_TYPES   = ['wysiwyg'];
    const DATE_TYPES = ['date', 'datetime', 'time'];
    const BOOL_TYPES = ['booleanSelect', 'checkbox'];
    const SELECT_TYPES = ['select'];
    const QUANTITY_VALUE_TYPES = ['inputQuantityValue', 'quantityValue'];
    const OBJECT_TYPES = ['href', 'image', 'manyToOneRelation', 'manyToOneObjectRelation'];
    const MULTI_OBJECT_TYPES = [
        'multihref',
        'imageGallery',
        'objects',
        'manyToManyRelation',
        'manyToManyObjectRelation'
    ];
    const STRUCTURED_TYPES = [self::LOCALIZED_FIELD_TYPE];
    const USER_TYPES = ['user'];
    const IMAGE_TYPES = ['image', 'imageGallery'];
    const MULTI_SELECT_TYPES = [
        'multiselect',
        'countrymultiselect',
        'languagemultiselect',
        'objectsMetadata',
        'multihrefMetadata'
    ];
    const OBJECT_BRICKS_TYPE         = 'objectbricks';
    const CLASSIFICATION_STORE_TYPE  = 'classificationstore';
    const LOCALIZED_FIELD_TYPE = 'localizedfields';
    const BLOCK_TYPES                = ['block'];
    const OBJECT_BRICKS_TYPES        = [self::OBJECT_BRICKS_TYPE];
    const CLASSIFICATION_STORE_TYPES = [self::CLASSIFICATION_STORE_TYPE];
}
