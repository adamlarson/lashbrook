<?php

namespace Ekr\Cart\Block\Item;

class Renderer extends \Magento\Checkout\Block\Cart\Item\Renderer{

	/**
	 * configurable
	 * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
	 */
	protected $_catalogProductTypeConfigurable;

	/**
	 * logger object
	 * @var \Ekr\Cart\Logger\Logger
	 */
	protected $_logger;

	 /**
	 * @param \Ekr\Cart\Logger\Logger $logger,
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param InterpretationStrategyInterface $messageInterpretationStrategy
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codeCoverageIgnore
     */
    public function __construct(
    	\Ekr\Cart\Logger\Logger $logger,
    	\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Element\Message\InterpretationStrategyInterface $messageInterpretationStrategy,
        array $data = []
    ) {

    	$this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
    	$this->_logger = $logger;

    	$this->_logger->info("set something");

        // call parent contruct method.
        parent::__construct(
        $context,
        $productConfig,
        $checkoutSession,
        $imageBuilder,
        $urlHelper,
        $messageManager,
        $priceCurrency,
        $moduleManager,
        $messageInterpretationStrategy,
        $data);
    }

    /**
     * create configurable product url
     * pass information to in url for preselected options
     * @return string url
     */
    public function getProductUrl(){

    	$simple = $this->getProduct();

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
        	$configurable = $this->getLoadProduct($parent_id);
        	$this->_logger->info("got configurable: " . $parent_id );
         	// $url = $this->getUrlRewrite( "product" , $parent_id );
            $url = $this->getDefaultUrl($configurable); 
         	$this->_logger->info("default url" . $url);
         	// get attribute values
         	$base_metal = $simple->getData('base_metal');
         	$inlay = $simple->getData('inlay');
         	$finish = $this->getSelectedFinish();
         	$this->_logger->info("base_metal" . $base_metal);
         	$this->_logger->info("inlay" . $inlay);
         	$this->_logger->info("finish" . $finish);

         	$url .= (strpos($url,"?") === false)? "?" : "&";
         	$url .= "base_metal={$base_metal}";
         	if(!empty($inlay)) $url .= "&inlay={$inlay}";
         	if(!empty($finish)) $url .= "&finish={$finish}";

         	$this->_logger->info("final url" . $url);
        }else{
        	$url = $this->getDefaultUrl($simple);
        }

        $this->product_urls[$simple->getEntityId()] = $url;
        return $url;
    }

    /**
     * get product base url
     * @param  object $product \Magento\Catalog\Model\Product
     * @return string
     */
    public function getDefaultUrl($product){
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }

    /**
     * get finish id from selected options
     * @return int finish_id
     */
    private function getSelectedFinish(){
    	$options = $this->getOptionList();
    	$finish = "";
    	foreach($options as $option){
    		if($option['label'] == "Finish"){
    			$finish = $option['value'];
    		}
    	}

    	return $finish;
    }	
}