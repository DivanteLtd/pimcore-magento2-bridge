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
 * Class GetObject
 * @package Divante\MagentoIntegrationBundle\Model\Request
 */
class GetObject extends AbstractObjectRequest
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
