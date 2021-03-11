<?php

namespace Divante\MagentoIntegrationBundle\Application\Validator;

use Divante\MagentoIntegrationBundle\Application\Validator\Rules\ObjectValidationRuleInterface;
use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\IntegrationConfiguration;
use Pimcore\Model\Element\ValidationException;

/**
 * Class IntegratedObjectValidator
 */
class IntegratedObjectValidator
{
    /** @var ObjectValidationRuleInterface[] */
    private $validators;

    /**
     * IntegratedObjectValidator constructor.
     * @param iterable $validators
     */
    public function __construct(iterable $validators)
    {
        $this->validators = iterator_to_array($validators);
        ksort($this->validators);
    }

    /**
     * @param AbstractObject $object
     * @param IntegrationConfiguration $configuration
     * @param string $type
     * @param bool $silent
     * @return bool
     * @throws \Exception
     */
    public function validateAbstractObject(
        AbstractObject $object,
        IntegrationConfiguration $configuration,
        string $type,
        bool $silent
    ): bool {
        foreach ($this->validators as $validator) {
            try {
                $validator->isValid($object, $configuration, $type);
            } catch (\Exception $exception) {
                if ($silent || $validator->isSilent()) {
                    return false;
                }
                throw $exception;
            }
        }

        return true;
    }
}
