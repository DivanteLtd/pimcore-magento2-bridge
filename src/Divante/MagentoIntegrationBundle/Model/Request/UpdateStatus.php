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
 * Class UpdateStatus
 * @package Divante\MagentoIntegrationBundle\Model\Request
 */
class UpdateStatus extends AbstractObjectRequest
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
        $this->id =  $request->get('id') !== null ? (int) $request->get('id') : null;
        $this->status = $request->get('status');
        $this->message = $request->get('message');
    }
}
