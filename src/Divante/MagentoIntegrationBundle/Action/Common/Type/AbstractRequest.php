<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractObjectRequest
 * @package Divante\MagentoIntegrationBundle\Domain\Common\Request
 */
abstract class AbstractRequest
{
    /**
     * @Assert\NotBlank(message = "Instance param is mandatory")
     */
    public $instaceUrl;

    /**
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\NotBlank(message = "StoreViewId param is mandatory")
     */
    public $storeViewId;

    /**
     * AbstractObjectRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->instaceUrl  = $request->get('instanceUrl');
        $this->storeViewId = $request->get('storeViewId') !== null ? (int)$request->get('storeViewId') : null;
    }
}
