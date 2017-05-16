<?php 

/*
Sample data from post
ekr_params[finish] = 1090
ekr_params[ring_size] = 13742420
ekr_params[simple_product] = 168202
form_key = tcRI6GOIwlbf5sTZproduct168138
related_product
selected_configurable_option
super_attribute[137] = 10
super_attribute[139] = 29
 */

namespace Ekr\Cart\Model;

class Cart {

	/**
	 * Product Factory
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $_productFactory;

	protected $_logger;

	/**
	 * Construct Method
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory 
	 */
	public function __construct(
		\Ekr\Cart\Logger\Logger $logger,
        \Magento\Catalog\Model\ProductFactory $productFactory) {

        $this->_productFactory = $productFactory;
        $this->_logger = $logger;
    }

	/**
	 * swap cart product.
	 * @param  \Magento\Checkout\Model\Cart $subject     cart object
	 * @param  Product                      $productInfo  Magento Product Object
	 * @param  array                        $requestInfo  Post data
	 * @return array to pass to addProduct function in Cart.
	 */
	public function beforeAddProduct(
        \Magento\Checkout\Model\Cart $subject,
        $productInfo,
        $requestInfo = null) {

		$this->_logger->info("beforeAddProduct");
        
        // swap simple configurable product for simple product.
        $ekr_params = $requestInfo['ekr_params'];
        $simple_product_id = $ekr_params['simple_product'];
        $this->_logger->info("simple_product_id: $simple_product_id");
        if($simple_product_id){
        	$productInfo = $this->_productFactory->create()->load($simple_product_id);
        	$requestInfo['product'] = $simple_product_id;
        	$this->_logger->info("changed product to simple");
        }
        return [$productInfo, $requestInfo];
    }
}