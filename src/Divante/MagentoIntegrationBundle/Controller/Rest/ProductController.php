<?php
/**
 * @category    pimcore5-module-magento2-integration
 * @date        19/03/2018
 * @author      Michał Bolka <mbolka@divante.co>
 * @copyright   Copyright (c) 2018 DIVANTE (https://divante.co)
 */

namespace Divante\MagentoIntegrationBundle\Controller\Rest;

use Divante\MagentoIntegrationBundle\Model\Request\GetObject;
use Divante\MagentoIntegrationBundle\Model\Request\UpdateStatus;
use Divante\MagentoIntegrationBundle\Service\Product\ProductService;
use Divante\MagentoIntegrationBundle\Service\Product\ProductStatusService;
use Pimcore\Bundle\AdminBundle\Controller\Rest\Element\AbstractElementController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class ProductController
 *
 * end point for product data.
 *
 * get product
 * GET http://[YOUR-DOMAIN]/webservice/rest/product?apikey=[API-KEY]
 * returns json-encoded object data.
 */
class ProductController extends AbstractElementController
{
    /** @var ProductStatusService */
    private $productStatusService;

    /** @var ProductService */
    private $productService;

    /**
     * @Method("GET")
     * @Route("/product")
     *
     * @api {get} /product Get product data
     * @apiName Get product by id and storeView
     * @apiGroup Object
     * @apiSampleRequest off
     * @apiParam {int} id an product id
     * @apiParam {instanceUrl} url to your instance
     * @apiParam {string} apikey your access token
     * @apiParam {storeViewId} id to your store view
     *
     * @apiParamExample {json} Request-Example:
     *     {
     *         "id": 1,
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
     *                      "data": {
     *                       "path": "/crm/inquiries/",
     *                       "creationDate": 1368630916,
     *                       "modificationDate": 1388409137,
     *                       "userModification": null,
     *                       "childs": null,
     *                       "elements": [
     *                       {
     *                           "type": "gender",
     *                           "value": "female",
     *                           "name": "gender",
     *                           "language": null
     *                      },
     *                      ]
     *
     *                      ...
     *
     *                    }
     *
     * @param GetObject $request
     *
     * @return JsonResponse
     */
    public function getAction(GetObject $request): JsonResponse
    {
        return $this->adminJson($this->getProductService()->handleRequest($request));
    }

    /**
     * @Method("POST")
     * @Route("/product/update-status")
     *
     * @api {post} /product/update-status Save information about synchronization status
     * @apiName Save product status
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
        return $this->adminJson($this->getProductStatusService()->handleRequest($request));
    }

    /**
     * @return ProductStatusService
     */
    protected function getProductStatusService(): ProductStatusService
    {
        if (!$this->productStatusService instanceof ProductStatusService) {
            $this->productStatusService = $this->get(ProductStatusService::class);
        }
        return $this->productStatusService;
    }

    /**
     * @return ProductService
     */
    protected function getProductService(): ProductService
    {
        if (!$this->productService instanceof ProductService) {
            $this->productService = $this->get(ProductService::class);
        }
        return $this->productService;
    }
}
