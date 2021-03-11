<?php


namespace Divante\MagentoIntegrationBundle\Domain\Mapper;

/**
 * Class MagentoRequiredFields
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper
 */
class MagentoRequiredFields
{
    const REQUIRED_FIELDS = [
        self::NAME,
        self::SKU,
        self::VISIBILITY,
        self::IS_ACTIVE_PIM,
        self::URL_KEY,
        self::CATEGORY_IDS,
    ];
    const NAME = "name";
    const SKU = "sku";
    const VISIBILITY = "visibility";
    const IS_ACTIVE_PIM = "is_active_in_pim";
    const URL_KEY = "url_key";
    const CATEGORY_IDS = "category_ids";
}
