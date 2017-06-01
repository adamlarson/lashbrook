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

    protected $_dataHelper;

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
        \Ekr\Catalog\Helper\Data $dataHelper,
        array $data = []
    ) {

    	$this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
    	$this->_logger = $logger;
        $this->_dataHelper = $dataHelper;

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
        return $this->_dataHelper->getProductUrl($simple,[]);
    }	
}