<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        30/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Controller\Rest;

use Divante\MagentoIntegrationBundle\Model\Request\GetObject;
use Divante\MagentoIntegrationBundle\Model\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Service\Category\CategoryService;
use Divante\MagentoIntegrationBundle\Service\Category\CategoryStatusService;
use Pimcore\Bundle\AdminBundle\Controller\Rest\Element\AbstractElementController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class CategoryController
 * @package Divante\MagentoIntegrationBundle\Controller\Rest
 */
class CategoryController extends AbstractElementController
{
    /** @var CategoryService */
    private $categoryService;

    /** @var CategoryStatusService */
    private $categoryStatusService;

    /**
     * @Method("GET")
     * @Route("/category")
     * @param GetObject $request
     * @return JsonResponse
     */
    public function getAction(GetObject $request): JsonResponse
    {
        return $this->adminJson($this->getCategoryService()->handleRequest($request));
    }


    /**
     * @Method("POST")
     * @Route("/category/update-status")
     *
     * @api {post} /category/update-status Save information about synchronization status
     * @apiName Save category status
     * @apiGroup Object
     * @apiSampleRequest off
     * @apiParam {int} id of elemenet
     * @apiParam {string} status ['synchronized','error']
     * @apiParam {instanceUrl} url to your instance
     * @apiParam {string} apikey your access token
     * @apiParam {storeViewId} id to your store view
     *
     * @apiParamExample {json} Request-Example:
     *     {
     *         "id": 1,
     *         "status"  : "SUCCESS",
     *         "message" : "",
     *         "instanceUrl":"http://magento.ecommerce",
     *         "storeViewId": "1"
     *         "apikey": "21314njdsfn1342134"
     *      }
     * @apiSuccess {json} success parameter of the returned data = true
     * @apiError {json} success parameter of the returned data = false
     * @apiErrorExample {json} Error-Response:
     *                  {"success":false, "msg":"exception 'Exception' with message '....'"}
     * @apiSuccessExample {json} Success-Response:
     *                    HTTP/1.1 200 OK
     *                    {
     *                      "success": true
     *                    }
     *
     * @param UpdateStatus $request
     *
     * @return JsonResponse
     */
    public function updateStatusAction(UpdateStatus $request): JsonResponse
    {
        return $this->adminJson($this->getCategoryStatusService()->handleRequest($request));
    }

    /**
     * @return CategoryService
     */
    protected function getCategoryService(): CategoryService
    {
        if (!$this->categoryService instanceof CategoryService) {
            $this->categoryService = $this->get(CategoryService::class);
        }
        return $this->categoryService;
    }

    /**
     * @return CategoryStatusService
     */
    protected function getCategoryStatusService(): CategoryStatusService
    {
        if (!$this->categoryStatusService instanceof CategoryStatusService) {
            $this->categoryStatusService = $this->get(CategoryStatusService::class);
        }
        return $this->categoryStatusService;
    }
}
