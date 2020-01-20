<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Domain;

use Divante\MagentoIntegrationBundle\Domain\Common\IntegratedElementServiceInterface;
use Divante\MagentoIntegrationBundle\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

/**
 * Class IntegrationService
 * @package Divante\MagentoIntegrationBundle\Domain
 */
class ElementDeleteService extends AbstractElementService
{

    /**
     * @param Concrete                 $object
     * @param IntegrationConfiguration $configuration
     * @throws \Exception
     */
    public function deleteObject(Concrete $object, IntegrationConfiguration $configuration)
    {
        $this->hideChildrenElements($object);
        /** @var IntegratedElementServiceInterface $remoteElementsService */
        foreach ($this->remoteElementsServices as $remoteElementsService) {
            if ($remoteElementsService->supports($object)) {
                $remoteElementsService->delete($object, $configuration);
                break;
            }
        }
    }

    /**
     * @param Concrete $object
     * @throws \Exception
     */
    protected function hideChildrenElements(Concrete $object)
    {
        /** @var Concrete $child */
        foreach ($object->getChildren([
            AbstractObject::OBJECT_TYPE_VARIANT,
            AbstractObject::OBJECT_TYPE_FOLDER,
            AbstractObject::OBJECT_TYPE_OBJECT
        ]) as $child) {
            if ($child->isPublished()) {
                $child->setPublished(false);
                $child->save();
            }
        }
    }
}
