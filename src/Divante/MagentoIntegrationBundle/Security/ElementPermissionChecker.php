<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        17/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Security;

use Divante\MagentoIntegrationBundle\Domain\Common\Exception\NotPermittedException;
use Pimcore\Bundle\AdminBundle\Security\User\TokenStorageUserResolver;
use Pimcore\Bundle\AdminBundle\Security\User\User as UserProxy;
use Pimcore\Model\Element\AbstractElement;
use Pimcore\Model\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class AbstractElementPermissionChecker
 * @package Divante\MagentoIntegrationBundle\Security
 */
class ElementPermissionChecker implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param AbstractElement $element
     * @param string          $type
     *
     * @throws NotPermittedException
     */
    public function checkElementPermission(AbstractElement $element, $type)
    {
        $map = [
            'get'    => 'view',
            'delete' => 'delete',
            'update' => 'publish',
            'create' => 'create'
        ];

        if (!isset($map[$type])) {
            throw new \InvalidArgumentException(sprintf('Invalid permission type: %s', $type));
        }

        $permission = $map[$type];
        if (!$element->isAllowed($permission)) {
            $this->container->get('monolog.logger.security')->error(
                'User {user} attempted to access {permission} on {elementType} {elementId}, but has no permission to do so',
                [
                    'user'        => $this->getAdminUser()->getName(),
                    'permission'  => $permission,
                    'elementType' => $element->getType(),
                    'elementId'   => $element->getId(),
                ]
            );

            throw new NotPermittedException(sprintf('Not allowed: permission %s is needed', $permission));
        }
    }

    /**
     * Get user from user proxy object which is registered on security component
     *
     * @param bool $proxyUser Return the proxy user (UserInterface) instead of the pimcore model
     *
     * @return UserProxy|User
     */
    protected function getAdminUser($proxyUser = false)
    {
        $resolver = $this->container->get(TokenStorageUserResolver::class);

        if ($proxyUser) {
            return $resolver->getUserProxy();
        } else {
            return $resolver->getUser();
        }
    }
}
