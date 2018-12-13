<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        15/06/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Model\Webservice;

use Pimcore\Logger;
use Pimcore\Model\DataObject;

/**
 * Class Service
 * @package Divante\MagentoIntegrationBundle\Model\Webservice
 */
class Service extends \Pimcore\Model\Webservice\Service
{
    /**
     * @param $id
     *
     * @return array|string
     * @throws \Exception
     */
    public function getObjectConcreteById($id)
    {
        try {
            $object = DataObject::getById($id);

            if ($object instanceof DataObject\Concrete) {
                DataObject\Service::loadAllObjectFields($object);

                $apiObject = \Pimcore\Model\Webservice\Data\Mapper::map(
                    $object,
                    '\\Divante\\MagentoIntegrationBundle\\Model\\Webservice\\Data\\DataObject\\Concrete\\Out',
                    'out'
                );

                return $apiObject;
            }
            throw new \Exception('Object with given ID (' . $id . ') does not exist.');
        } catch (\Exception $e) {
            Logger::error($e);
            throw $e;
        }
    }
}
