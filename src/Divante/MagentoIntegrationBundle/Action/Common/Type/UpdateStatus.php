<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Divante\MagentoIntegrationBundle\Action\Common\Type\AbstractRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateStatus
 * @package Divante\MagentoIntegrationBundle\Domain\Common\Reqest
 */
class UpdateStatus extends AbstractRequest
{
    /**
     * @Assert\Type(
     *     type="integer",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     * @Assert\NotBlank(message = "Id param is mandatory")
     */
    public $id;

    /**
     * @Assert\Choice(
     *     choices = {"SUCCESS", "ERROR", "DELETED"},
     *     message = "Status param is mandatory. Available values: SUCCESS, ERROR, DELETED"
     * )
     * @Assert\NotBlank(message = "Status param is mandatory. Available values: SUCCESS, ERROR, DELETED")
     */
    public $status;
    public $message;

    /**
     * UpdateProductStatusRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->id      = $request->get('id') !== null ? (int)$request->get('id') : null;
        $this->status  = $request->get('status');
        $this->message = $request->get('message');
    }
}
