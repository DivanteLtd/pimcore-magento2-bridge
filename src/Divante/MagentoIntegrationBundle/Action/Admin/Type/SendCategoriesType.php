<?php

namespace Divante\MagentoIntegrationBundle\Action\Admin\Type;

use Divante\MagentoIntegrationBundle\Action\Common\Type\IdRequest;

/**
 * Class SendCategoriesType
 * @package Divante\MagentoIntegrationBundle\Action\Admin\Type
 */
class SendCategoriesType extends IdRequest
{
    /**
     * @return string
     */
    public function getDescription()
    {
        return "Send Categories to Magento Request";
    }
}

