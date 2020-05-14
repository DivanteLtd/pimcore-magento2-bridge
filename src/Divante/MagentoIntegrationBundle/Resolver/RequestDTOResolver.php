<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Resolver;

use Divante\MagentoIntegrationBundle\Action\Common\Type\GetElement;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestDTOResolver
 * @package Divante\MagentoIntegrationBundle\Resolver
 */
class RequestDTOResolver implements ArgumentValueResolverInterface
{
    /** @var ValidatorInterface */
    private $validator;

    /**
     * RequestDTOResolver constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     * @return bool
     * @throws \ReflectionException
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if (!$argument->getType() || !class_exists($argument->getType())) {
            return false;
        }
        $reflection = new \ReflectionClass($argument->getType());
        return
            $reflection->getParentClass()
            && $reflection->getParentClass()->getName() == GetElement::class;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class  = $argument->getType();
        $dto    = new $class($request);
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $response = [];
            foreach ($errors as $error) {
                $response[] = $error->getMessage();
            }
            throw new BadRequestHttpException(implode(" \n ", $response));
        }
        yield $dto;
    }
}
