<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Divante\MagentoIntegrationBundle\Action\Common\Type\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GetElement
 * @package Divante\MagentoIntegrationBundle\Domain\Common\Reqest
 */
abstract class GetElement extends AbstractRequest
{
    /**
     * @Assert\NotBlank(message = "Id param is mandatory")
     */
    public $id;

    /**
     * GetObject constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->id = $request->get('id');
    }
}
