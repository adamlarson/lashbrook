<?php

namespace Ekr\Cart\Observer;

class CheckoutProductAddAfter implements \Magento\Framework\Event\ObserverInterface{
  
  	/**
  	 * logger object
  	 * @var \Ekr\Cart\Logger\Logger
  	 */
	protected $_logger;

	/**
	 * Post request object
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	/**
	 * Magento product factory to load products
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $_productFactory;

	/**
	 * Ekr Data Helper
	 * @var \Ekr\Catalog\Helper\Data
	 */
	protected $_dataHelper;

	/**
	 * contruct method
	 * @param \Ekr\Cart\Logger\Logger                 $logger         logger object
	 * @param \Magento\Framework\App\RequestInterface $request        Post request object
	 * @param \Magento\Catalog\Model\ProductFactory   $productFactory product factury
	 * @param \Magento\Checkout\Model\Cart            $cart           cart object
	 */
	public function __construct(
		\Ekr\Cart\Logger\Logger $logger,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Ekr\Catalog\Helper\Data $dataHelper){
		$this->_logger = $logger;
		$this->_request = $request;
		$this->_productFactory = $productFactory;
		$this->_dataHelper = $dataHelper;
	}
		
	public function execute(\Magento\Framework\Event\Observer $observer){
		$ekr_params = $this->_request->getParam('ekr_params');
		if(empty($ekr_params)) return $this;

		// finish
		$finish_id = $ekr_params['finish'];
		$finishProduct = $this->_productFactory->create()->load($finish_id);
		$finishOption = [
			'label' => __("Finish"),
			'value' => $finishProduct->getName()
		];

		// engraving options.
		$engraving = @$ekr_params['engraving'];
		$engravingOption = [];
		if(!empty($engraving)){
			// font name.
			if(!empty($engraving['text'])){
				$engravingOption = [
					'label' => __("Engraving"),
					'value' => $engraving['font'] .' | ' . $engraving['location'] .' | ' . $engraving['text']
				];
			}
		}

		$notesOption = [];
		$product_notes = @$ekr_params['product_notes'];
		if(!empty($product_notes)){
			$notesOption = [
				'label' => __("Notes"),
				'value' => $product_notes
			];
		}
		
		// ring size
		$ring_size = $ekr_params['ring_size'];
		// load simple product
		$simpleProduct = $this->_productFactory->create()->load($ekr_params['simple_product']);
		// loop through product options to find correct option.
		$sizeOption = [];
        foreach($simpleProduct->getProductOptionsCollection() as $option){
            // size options
            if($option->getTitle() == "Size"){
            	// loop through size values
                foreach($option->getValues() as $values){
                    $sizeData = $values->getData();
                    // if value matches selected ring size
                    if($sizeData['option_type_id'] == $ring_size){
                    	$sizeOption = [
                    		'label' => __("Ring Size"),
                    		'value' => $sizeData['default_title']
                    	];
                    	break;
                    }
                }
                if($sizeOption) break;
            }
        }

		// get item added to cart
		$item = $observer->getQuoteItem();

		// get price from simple product
		$basePrice = $simpleProduct->getPrice();

		$newPrice = $basePrice;

		// if size data
		if(@$sizeData){
			// modify price based on ring size price
			$newPrice += $sizeData['default_price'];
		}

		// change price for finish
		$newPrice += $finishProduct->getPrice();

		$multiplier = $this->_dataHelper->getCustomerPriceMultiplier();

		$newPrice = $newPrice * $multiplier;

		// change price based on engraving.
		if(!empty($engravingOption)){
			// location.
			if($engraving['location'] == "outside"){
				$ePrice = 30;
			}else{
				// inside (zirconium?)
				$ePrice = ($simpleProduct->getData('base_metal') == 11)? 30 : 15;
			}
			$newPrice += $ePrice;
		}

		// Set the custom price
        $item->setCustomPrice($newPrice);
        $item->setOriginalCustomPrice($newPrice);
        // Enable super mode on the product.
        $item->getProduct()->setIsSuperMode(true);

		// additional options for item.
		$additionalOptions = [];
		if(!empty($finishOption)) $additionalOptions[] = $finishOption;
		if(!empty($sizeOption)) $additionalOptions[] = $sizeOption;
		if(!empty($engravingOption)) $additionalOptions[] = $engravingOption;
		if(!empty($notesOption)) $additionalOptions[] = $notesOption;


		//$this->_logger->info(print_r($additionalOptions));
		// add additional options.
		$item->addOption(array(
            'code' => 'additional_options',
            'value' => serialize($additionalOptions)
        ));

		return $this;
  	}
}