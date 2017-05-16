<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ekr\Catalog\Block\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct{

	protected $_catalogProductTypeConfigurable;

	protected $_productloader;

	protected $_logger;

    protected $finish_attribute_id = 145;
    protected $price_attribute_id = 77;

    protected $product_urls = [];
    protected $product_attribute_values = [];

    protected $_dataHelper;

	public function __construct(
		\Ekr\Catalog\Logger\Logger $logger,
		\Magento\Catalog\Model\ProductFactory $productloader,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Ekr\Catalog\Helper\Data $dataHelper,
        array $data = []
    ) {

        $this->_dataHelper = $dataHelper;
		$this->_logger = $logger;
		$this->_productloader = $productloader;
    	$this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;

        $this->_logger->info("constructing ListProduct");

        $this->_dataHelper = $dataHelper;
    	
    	// \Magento\Catalog\Block\Product\ListProduct::contruct()
        parent::__construct($context,$postDataHelper,$layerResolver,$categoryRepository,$urlHelper,$data);
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
        if(isset($this->product_urls[$simple->getEntityId()])) return $this->product_urls[$simple->getEntityId()];
    	$this->_logger->info("getting product url");
    	$this->_logger->info("get parent id for " . $simple->getEntityId());
    	// check if product is has a parent
    	$parentByChild = $this->_catalogProductTypeConfigurable->getParentIdsByChild($simple->getEntityId());
    	$parent_id = null;
        if(isset($parentByChild[0])){
            $this->_logger->info("parentByChild: " . $parentByChild[0]);
            $parent_id = $parentByChild[0];          
        }
        $this->_logger->info("parent_id: " . $parent_id);
        // if product has a parent
        if($parent_id){
        	$configurable = $this->getLoadProduct($parent_id);
        	$this->_logger->info("got configurable: " . $parent_id );
         	// $url = $this->getUrlRewrite( "product" , $parent_id );
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
     * load product object from id
     * @param  int $id product_id
     * @return product \Magento\Catalog\Model\Product
     */
    public function getLoadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    /**
     * [getUrlRewrite description]
     * @param  string $type     entity type
     * @param  int $entity_id   entity id
     * @return string           url
     */
    public function getUrlRewrite($type,$entity_id){
        
        $this->_logger->info("getting url rewrite");
        // get database connection
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        // $this->_logger->info("got objectManager");
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        // $this->_logger->info("got resource");
        $connection = $resource->getConnection();
        // $this->_logger->info("got connection");
        $tableName = $resource->getTableName("url_rewrite"); //gives table name with prefix
        $this->_logger->info("got tablename {$tableName}");
        // $this->_logger->info("got order id {$order_id}");
        $sql = "Select * FROM " . $tableName . " WHERE `entity_id` = '{$entity_id}' AND `entity_type`='{$type}'";
        // $this->_logger->info("query: {$sql}");
        $result = $connection->fetchAll($sql);
        // $this->_logger->info("results " . print_r($result));
         return "/store/" . $result[0]['request_path'];
        //return "Some string right now";
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

    /**
     * Get product price
     * @param  \Magento\Catalog\Model\Product $product Magento Product object
     * @return string
     */
    function getProductPrice(\Magento\Catalog\Model\Product $product){

        //return parent::getProductPrice($product);

        // get base price
        $price = $product->getPrice();

        // check if product has finish associated
        $entity_id = $product->getEntityId();
        if(isset($this->product_attribute_values[$this->finish_attribute_id][$entity_id])){
            $finish_id = $this->product_attribute_values[$this->finish_attribute_id][$entity_id];
        }else{
            $finish_id = $this->getProductAttribute($entity_id,$this->finish_attribute_id);
        }
        // get finish price and add to product price
        if($finish_id){
            $fPrice = $this->getProductAttribute($finish_id,$this->price_attribute_id,"catalog_product_entity_decimal");
            $price += $fPrice;
        }

        $multiplier = $this->_dataHelper->getCustomerPriceMultiplier();
        $this->_logger->info("Customer multiplier " . $multiplier);
        $this->_logger->info("old price " . $price);
        $price = $price * $multiplier;
        $this->_logger->info("new price " . $price);
        
        return "<div class=\"price-box price-final_price\" data-role=\"priceBox\" data-product-id=\"{$entity_id}\">
        <span class=\"price-container price-final_price tax weee\">
        <span id=\"product-price-{$entity_id}\" data-price-amount=\"{$price}\" data-price-type=\"finalPrice\" class=\"price-wrapper\">
        <span class=\"price\">$" . number_format($price,2) ."</span></span></span></div>";
    }
}
