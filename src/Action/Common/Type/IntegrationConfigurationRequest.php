<?php

namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class IntegrationConfigurationRequest
 * @package PimcoreConnectorBundle\Domain\IntegrationConfiguration\Request
 */
class IntegrationConfigurationRequest extends IdRequest
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
     * IntegrationConfigurationRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->instaceUrl  = $request->get('instanceUrl');
        $this->storeViewId = $request->get('storeViewId') !== null ? (int)$request->get('storeViewId') : null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
       return sprintf(
           "%s\nInstanceUrl: %s\nStoreView Id: %s",
           parent::__toString(),
           $this->instaceUrl,
           $this->storeViewId
       );
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Get data from Pimcore Request";
    }
}
