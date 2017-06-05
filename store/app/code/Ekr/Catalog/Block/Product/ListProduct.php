<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ekr\Catalog\Block\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct{

    /**
     * Magento configurable product class
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
	protected $_catalogProductTypeConfigurable;

    /**
     * Magento product loader.
     * @var \Magento\Catalog\Model\ProductFactory
     */
	protected $_productloader;

    /**
     * Catalog Logger
     * @var \Ekr\Catalog\Logger\Logger
     */
	protected $_logger;

    /**
     * Product finish attribute ID
     * @var integer
     */
    protected $finish_attribute_id = 145;
    /**
     * Product price attribute id
     * @var integer
     */
    protected $price_attribute_id = 77;
    /**
     * Product ring weight attribute ID (for -IS or -P products)
     * @var integer
     */
    protected $weight_attribute_id = 159;

    /**
     * save product urls just in case they are requested again
     * @var array
     */
    protected $product_urls = [];
    /**
     * save product attribute values in case they are requested again.
     * @var array
     */
    protected $product_attribute_values = [];
    
    /**
     * \Ekr\Catalog\Helper\Data
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Database entity_id for Classics category.
     * @var integer
     */
    protected $_classicsCategoryId = 5;

    /**
     * Magento category object
     * Current category passed to the block.
     * @var [type]
     */
    protected $_category;

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
        $this->_logger->info('get product url in list product');
        $url = $this->_dataHelper->getProductUrl($simple,$additional);
        // add classics label to url (if found in product).
        $label = $this->classicsLabel($simple);
        if(!empty($label)){
            $url .= "&label=" . urlencode($label);
        }
        return $url;
    }

    /**
     * Get product price
     * @param  \Magento\Catalog\Model\Product $product Magento Product object
     * @return string
     */
    function getProductPrice(\Magento\Catalog\Model\Product $product){

        // get base price
        $price = $product->getPrice();

        // check if product has finish associated
        $entity_id = $product->getEntityId();
        if(isset($this->product_attribute_values[$this->finish_attribute_id][$entity_id])){
            $finish_id = $this->product_attribute_values[$this->finish_attribute_id][$entity_id];
        }else{
            $finish_id = $this->_dataHelper->getProductAttribute($entity_id,$this->finish_attribute_id);
        }
        // get finish price and add to product price
        if($finish_id){
            $fPrice = $this->_dataHelper->getProductAttribute($finish_id,$this->price_attribute_id,"catalog_product_entity_decimal");
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

    /**
     * Get the name of the product
     * If current category is Classic then return ekr_classic_cat_label
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Helper\Output $helper (created in product list)
     * @return string
     */
    public function getProductName($product,$helper = null){
        $name = ($helper == null)? $product->getName() : $helper->productAttribute($product, $product->getName(), 'name');
        $label = $this->classicsLabel($product);
        if(!empty($label)) return "<span class=\"classics-label\">{$label}</span>";
        return $name;
    }

    /**
     * find the label for the classics section
     * check if currently in classics category.
     * @param  object $product \Magento\Catalog\Model\Product
     * @return string
     */
    private function classicsLabel($product){
        $product = $this->_dataHelper->loadProduct($product->getEntityId());
        if($this->_category != null){
            $this->_logger->info('category id ' . $this->_category->getId());
            if($this->_category->getId() == $this->_classicsCategoryId){
                $label = $product->getData('ekr_classic_cat_label');
                //$label = $this->_dataHelper->getProductAttribute($product->getEntityId(),$this->classics_label_attr_id);
                $this->_logger->info("label " . $label);
                return $label;
            }
        }
        return "";
    }

    /**
     * set category object in block
     * @param object $category Magento Category Object
     */
    public function setCategory($category){
        $this->_category = $category;
    }
}
