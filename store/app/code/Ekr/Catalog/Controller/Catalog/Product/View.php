<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ekr\Catalog\Controller\Catalog\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class View extends \Magento\Catalog\Controller\Product
{
    /**
     * @var \Magento\Catalog\Helper\Product\View
     */
    protected $viewHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $_catalogProductTypeConfigurable;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_result;

    /**
     * @var \Ekr\Cart\Logger\Logger
     */
    protected $_logger;

     protected $finish_attribute_id = 145;
    protected $price_attribute_id = 77;


    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Catalog\Helper\Product\View $viewHelper
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Catalog\Helper\Product\View $viewHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ekr\Catalog\Logger\Logger $logger
    ) {
        $this->viewHelper = $viewHelper;
        $this->_logger = $logger;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->_productFactory = $productFactory;
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_result =  $context->getResultFactory();

        $this->_logger->info('construct View');

        parent::__construct($context);
    }
    

    /**
     * Redirect if product failed to load
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\Result\Forward
     */
    protected function noProductRedirect()
    {
        $store = $this->getRequest()->getQuery('store');
        if (isset($store) && !$this->getResponse()->isRedirect()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('');
        } elseif (!$this->getResponse()->isRedirect()) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    /**
     * Product view action
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->_logger->info("execute method");
        // Get initial data from request
        $categoryId = (int) $this->getRequest()->getParam('category', false);
        $productId = (int) $this->getRequest()->getParam('id');
        $specifyOptions = $this->getRequest()->getParam('options');

        if ($this->getRequest()->isPost() && $this->getRequest()->getParam(self::PARAM_NAME_URL_ENCODED)) {
            $product = $this->_initProduct();
            if (!$product) {
                return $this->noProductRedirect();
            }

            if ($specifyOptions) {
                $notice = $product->getTypeInstance()->getSpecifyOptionMessage();
                $this->messageManager->addNotice($notice);
            }
            if ($this->getRequest()->isAjax()) {
                $this->getResponse()->representJson(
                    $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode([
                        'backUrl' => $this->_redirect->getRedirectUrl()
                    ])
                );
                return;
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        // check if product is a simple product.
        $product = $this->loadProduct($productId);
        $type = $product->getTypeId();
        $this->_logger->info("product type {$type}");
        if($type == "simple"){
            $url = $this->getProductUrl($product);
            $this->_logger->info("redirect to {$url}");
            
            $resultRedirect = $this->_result->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($url);
            return $resultRedirect;  
        }


        // Prepare helper and params
        $params = new \Magento\Framework\DataObject();
        $params->setCategoryId($categoryId);
        $params->setSpecifyOptions($specifyOptions);

        // Render page
        try {
            $page = $this->resultPageFactory->create();
            $this->viewHelper->prepareAndRender($page, $productId, $this, $params);
            return $page;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->noProductRedirect();
        } catch (\Exception $e) {
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
    }

    /**
     * Retrieve Product URL using UrlDataObject
     *
     * @param \Magento\Catalog\Model\Product $simple
     * @param array $additional the route params
     * @return string
     */
    public function getProductUrl($simple, $additional = [])
    {
        $this->_logger->info("getting product url");
        $this->_logger->info("get parent id for " . $simple->getEntityId());
        // check if product is has a parent
        $parentByChild = $this->_catalogProductTypeConfigurable->getParentIdsByChild($simple->getEntityId());
        $parent_id = null;
        $this->_logger->info("parentByChild: " . $parentByChild[0]);
        if(isset($parentByChild[0])){
            $parent_id = $parentByChild[0];          
        }
        $this->_logger->info("parent_id: " . $parent_id);
        // if product has a parent
        if($parent_id){
            $configurable = $this->loadProduct($parent_id);
            $this->_logger->info("got configurable: " . $parent_id );
            $url = $this->getDefaultUrl($configurable,$additional); 
            $this->_logger->info("default url" . $url);
            // get attribute values
            $base_metal = $simple->getData('base_metal');
            $inlay = $simple->getData('inlay');
            $finish = $this->getProductAttribute($simple->getEntityId(),$this->finish_attribute_id);

            $this->_logger->info("base_metal" . $base_metal);
            $this->_logger->info("inlay" . $inlay);
            $this->_logger->info("finish" . $finish);

            $url .= (strpos($url,"?") === false)? "?" : "&";
            $url .= "base_metal={$base_metal}";
            if(!empty($inlay)) $url .= "&inlay={$inlay}";
            if(!empty($finish)) $url .= "&finish={$finish}";

            $this->_logger->info("final url" . $url);
        }else{
            $url = $this->getDefaultUrl($simple,$additional);
        }

        $this->product_urls[$simple->getEntityId()] = $url;
        return $url;
    }

    /**
     * create product object
     * @param  int $product_id  Product entity id
     * @return product
     */
    public function loadProduct($product_id){
        return $this->_productFactory->create()->load($product_id);
    }

    /**
     * get product base url
     * @param  object $product \Magento\Catalog\Model\Product
     * @return string
     */
    public function getDefaultUrl($product,$additional){
        $url = "/store/catalog/product/view/id/" . $product->getEntityId() . "/";
        if(@$additional['category_id']){
            $url .= "?category_id=" . $additional['category_id'];
        }
        return $url;
    }

    /**
     * get attribute directly from table.
     * @param  int $entity_id    product id
     * @param  int $attribute_id attribute id
     * @return return value
     */
    function getProductAttribute($entity_id,$attribute_id,$table = "catalog_product_entity_varchar"){

        if(isset($this->product_attribute_values[$attribute_id][$entity_id])) return $this->product_attribute_values[$attribute_id][$entity_id];

        $this->_logger->info("getting " . $attribute_id);
        // get database connection
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        // $this->_logger->info("got objectManager");
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        // $this->_logger->info("got resource");
        $connection = $resource->getConnection();
        // $this->_logger->info("got connection");
        $tableName = $resource->getTableName($table); //gives table name with prefix
        $this->_logger->info("got tablename {$tableName}");
        // $this->_logger->info("got order id {$order_id}");
        $sql = "Select value FROM " . $tableName . " WHERE `entity_id` = '{$entity_id}' AND `attribute_id`='{$attribute_id}'";
        // $this->_logger->info("query: {$sql}");
        $result = $connection->fetchAll($sql);
        // $this->_logger->info("results " . print_r($result));
        $value = (@$result[0]['value'])? $result[0]['value'] : "";
        
        $this->product_attribute_values[$attribute_id][$entity_id] = $value;

         return $value;
    }
}
