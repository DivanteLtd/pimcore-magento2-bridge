<?php

namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class AssetRequest
 * @package Divante\MagentoIntegrationBundle\Action\Common\Type
 */
class AssetRequest extends IdRequest
{
    /**
     * @var string
     */
    public $thumbnail;

    /**
     * AssetRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->thumbnail = $request->get("thumbnail");
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Id: %s\nThumbnail: %s", $this->id, $this->thumbnail);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Get asset by id request";
    }
}
