<?php
/**
 * @category    selena
 * @date        14/04/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UpdateStatusRequest
 * @package PimcoreConnectorBundle\Domain\Common\Request
 */
class UpdateStatusRequest extends IntegrationConfigurationRequest
{
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
     * UpdateStatusRequest constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->status = $request->get('status');
        $this->message = $request->get('message');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf(
            "%s \nStatus: %s \nMessage: %s",
            parent::__toString(),
            $this->status,
            $this->message
        );
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Update Status Request";
    }
}
