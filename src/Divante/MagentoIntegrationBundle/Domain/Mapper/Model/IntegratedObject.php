<?php


namespace Divante\MagentoIntegrationBundle\Domain\Mapper\Model;

/**
 * Class IntegratedObject
 * @package Divante\MagentoIntegrationBundle\Domain\Mapper\Model
 */
class IntegratedObject
{
    /** @var string */
    protected $className;

    /** @var int */
    protected $objectId;

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return int
     */
    public function getObjectId(): int
    {
        return $this->objectId;
    }

    /**
     * @param int $objectId
     */
    public function setObjectId(int $objectId): void
    {
        $this->objectId = $objectId;
    }
}
