<?php
/**
 * @category    magento2-pimcore5-bridge
 * @date        20/08/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Resolver;

use Divante\MagentoIntegrationBundle\Model\Request\AbstractObjectRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class RequestDTOResolver
 * @package Divante\MagentoIntegrationBundle\Resolver
 */
class RequestDTOResolver implements ArgumentValueResolverInterface
{
    /** @var ValidatorInterface  */
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
            && $reflection->getParentClass()->getName() == AbstractObjectRequest::class;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $class = $argument->getType();
        $dto = new $class($request);
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
