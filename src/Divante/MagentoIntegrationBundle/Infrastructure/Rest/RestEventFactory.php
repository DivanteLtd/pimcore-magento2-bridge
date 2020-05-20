<?php

namespace Divante\MagentoIntegrationBundle\Infrastructure\Rest;

use Divante\MagentoIntegrationBundle\Domain\Rest\Event\AbstractSendEvent;

/**
 * Class RestEventFactory
 * @package Divante\MagentoIntegrationBundle\Infrastructure\Rest
 */
class RestEventFactory
{
    /**
     * @param          $data
     * @param string   $type
     * @return mixed
     */
    public function createEvent($data, string $type): AbstractSendEvent
    {
        $className = sprintf(
            '\\Divante\\MagentoIntegrationBundle\\Domain\\Rest\\Event\\%sSendEvent',
            ucfirst($type)
        );
        if (class_exists($className)) {
            return new $className($data);
        }
        throw new \InvalidArgumentException(sprintf("Class %s does not exist.", $className));
    }
}
