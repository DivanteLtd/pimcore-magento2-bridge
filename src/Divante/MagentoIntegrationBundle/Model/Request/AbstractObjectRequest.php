<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Model\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class AbstractObjectRequest
 * @package Divante\MagentoIntegrationBundle\Model\Request
 */
abstract class AbstractObjectRequest implements RequestDTOInterface
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
        $this->instaceUrl = $request->get('instanceUrl');
        $this->storeViewId = $request->get('storeViewId') !== null ? (int) $request->get('storeViewId') : null;
    }
}
