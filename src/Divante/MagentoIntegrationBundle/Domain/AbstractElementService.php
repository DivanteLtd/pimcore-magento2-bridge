<?php
/**
 * @category    bosch-stuttgart
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Domain;

use Divante\MagentoIntegrationBundle\Domain\Common\IntegratedElementServiceInterface;

/**
 * Class AbstractElementService
 * @package Divante\MagentoIntegrationBundle\Domain
 */
abstract class AbstractElementService
{
    /** @var IntegratedElementServiceInterface[] */
    protected $remoteElementsServices;

    /**
     * AbstractElementService constructor.
     * @param iterable $remoteElementsServices
     */
    public function __construct(iterable $remoteElementsServices)
    {
        foreach ($remoteElementsServices as $remoteElementsService) {
            $this->addRemoteElementService($remoteElementsService);
        }
    }

    /**
     * @param IntegratedElementServiceInterface $service
     */
    public function addRemoteElementService(IntegratedElementServiceInterface $service)
    {
        $this->remoteElementsServices[] = $service;
    }
}
