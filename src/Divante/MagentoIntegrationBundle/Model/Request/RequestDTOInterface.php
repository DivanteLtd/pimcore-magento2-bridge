<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Model\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface RequestDTOInterface
 * @package Divante\MagentoIntegrationBundle\Model\Webservice
 */
interface RequestDTOInterface
{
    /**
     * RequestDTOInterface constructor.
     * @param Request $request
     */
    public function __construct(Request $request);
}
