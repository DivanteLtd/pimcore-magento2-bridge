<?php


namespace Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration;

/**
 * Class ObjectMappingsDefaults
 * @package Divante\MagentoIntegrationBundle\Domain\IntegrationConfiguration
 */
class ObjectMappingsDefaults
{
    const DEFAULT_PRODUCT_MAPPINGS = [
        ["","name","","",false,false,false,false,false],
        ["","sku","","",false,false,false,false,false],
        ["","visibility","","",false,false,false,false,false],
        ["","is_active_in_pim","","",false,false,false,false,false],
        ["","url_key","","",false,false,false,false,false],
        ["","category_ids","","",false,false,false,false,false],
        ["","qty","","",false,false,false,false,false],
        ["","description","","",false,false,false,false,false],
        ["","short_description","","",false,false,false,false,false],
        ["","image","","",false,false,false,false,false],
        ["","small_image","","",false,false,false,false,false],
        ["","thumbnail","","",false,false,false,false,false],
        ["","media_gallery","","",false,false,false,false,false],
        ["","manufacturer","","",false,false,false,false,false],
        ["","news_from_date","","",false,false,false,false,false],
        ["","news_to_date","","",false,false,false,false,false],
        ["","related_products","","",false,false,false,false,false],
        ["","up_sell_products","","",false,false,false,false,false],
        ["","cross_sell_products","","",false,false,false,false,false],
    ];

    const DEFAULT_CATEGORY_MAPPINGS = [
        ["","name","",""],
        ["","url_key","",""],
        ["","description","",""],
        ["","is_active","",""],
        ["","image","",""],
    ];
}
