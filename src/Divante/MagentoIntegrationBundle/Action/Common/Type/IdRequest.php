<?php


namespace Divante\MagentoIntegrationBundle\Action\Common\Type;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class IdRequest
 * @package PimcoreConnectorBundle\Domain\Common\Request
 */
class IdRequest
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
        $this->id = $request->get('id');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("Id: %s", $this->id);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return "Get resource by id request";
    }
}
