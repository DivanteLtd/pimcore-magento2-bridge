<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Application\Mapper;

/**
 * Class AttributesChecksumCalculator
 * @package Divante\MagentoIntegrationBundle\Application\Mapper
 */
class AttributesChecksumCalculator
{
    const EXCLUDED_ATTRIBUTES = ['related_products'];

    /**
     * @param \stdClass $object
     * @return array
     */
    public function getAttributesChecksum(\stdClass $object): array
    {
        $attributes = [];
        foreach ($object->elements as $key => $element) {
            if (in_array($key, static::EXCLUDED_ATTRIBUTES)) {
                continue;
            }
            $attributes[$key] = $element['type'];
        }
        ksort($attributes);
        return ['algo' => 'md5', 'value' => md5(json_encode($attributes))];
    }
}
