<?php
/**
 * @category    pimcore
 * @date        25/06/2019
 * @author      Michał Bolka <michal.bolka@gmail.com>
 */
namespace Divante\MagentoIntegrationBundle\Security;

use Pimcore\Model\Asset;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AssetVoter extends Voter
{
    const SAVE = 'save';
    const VIEW = 'view';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::VIEW, self::SAVE])) {
            return false;
        }
        return $subject instanceof Asset;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var Asset $subject */
        return $subject->isAllowed($attribute);
    }
}
