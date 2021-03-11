<?php

namespace Divante\MagentoIntegrationBundle\Action\Admin\Type;

use Divante\MagentoIntegrationBundle\Action\Common\Type\IdRequest;

/**
 * Class SendProductsType
 * @package Divante\MagentoIntegrationBundle\Action\Admin\Type
 */
class SendProductsType extends IdRequest
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Send Products to Magento Request";
    }
}
