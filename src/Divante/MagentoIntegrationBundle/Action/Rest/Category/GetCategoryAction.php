<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        20/01/2020
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2020 Divante Ltd. (https://divante.co)
 */
namespace Divante\MagentoIntegrationBundle\Action\Rest\Category;

use Divante\MagentoIntegrationBundle\Domain\Category\Request\GetCategory;
use Divante\MagentoIntegrationBundle\Responder\MappedObjectJsonResponder;
use Divante\MagentoIntegrationBundle\Domain\Category\MappedCategoryService;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetCategoryAction
 * @package Divante\MagentoIntegrationBundle\Action\Rest\Category
 * @Route("/webservice/rest/category", methods={"GET"})
 */
class GetCategoryAction
{
    /** @var MappedCategoryService */
    private $domain;
    /** @var MappedObjectJsonResponder */
    private $responder;

    /**
     * GetCategoryAction constructor.
     * @param MappedCategoryService     $domain
     * @param MappedObjectJsonResponder $responder
     */
    public function __construct(MappedCategoryService $domain, MappedObjectJsonResponder $responder)
    {
        $this->domain = $domain;
        $this->responder = $responder;
    }

    /**
     * @param GetCategory $query
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(GetCategory $query)
    {
        return $this->responder->createResponse($this->domain->getCategories($query));
    }
}
