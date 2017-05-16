<?php

namespace Ekr\Cart\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class SalesModelServiceQuoteSubmitBeforeObserver implements ObserverInterface
{
    private $quoteItems = [];
    private $quote = null;
    private $order = null;

    private $_logger;

    /**
     * contruct method
     * @param \Ekr\Cart\Logger\Logger $logger logger object
     */
    public function __construct(
        \Ekr\Cart\Logger\Logger $logger){
        $this->_logger = $logger;
        $this->_logger->info("contruct SalesModelServiceQuoteSubmitBeforeObserver");
    }

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(
        EventObserver $observer
        //\Magento\Framework\App\RequestInterface $request
        )
    {
        $this->_logger->info("execute SalesModelServiceQuoteSubmitBeforeObserver");

        $this->quote = $observer->getQuote();
        $this->order = $observer->getOrder();
        // can not find a equivalent event for sales_convert_quote_item_to_order_item
        

        /* @var  \Magento\Sales\Model\Order\Item $orderItem */
        foreach($this->order->getItems() as $orderItem)
        {
            $parent_id = $orderItem->getParentItemId();
            $product_type = $orderItem->getProductType();
            $simple_type = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;
            $quoteItemId = $orderItem->getQuoteItemId();
            $this->_logger->info("parent_id: " . $parent_id);
            $this->_logger->info("product_type: " . $product_type);
            $this->_logger->info("simple_type: " . $simple_type);
            $this->_logger->info("quoteItemId: " . $quoteItemId);

            if(!$parent_id/* && $product_type  == $simple_type*/){
                $this->_logger->info("--- in loop");
                $this->_logger->info("--- quoteItemId: " . $quoteItemId);
                if($quoteItem = $this->getQuoteItemById($quoteItemId)){
                    $this->_logger->info("--- got quoteItem");
                    $additionalOptions = $quoteItem->getOptionByCode('additional_options');
                    if ($additionalOptions){
                        $this->_logger->info("--- got additionalOptionsQuote");

                        $options = $orderItem->getProductOptions();
                        $this->_logger->info("--- the count is there: " . $additionalOptions->getValue());
                        $options['additional_options'] = unserialize($additionalOptions->getValue());
                        $orderItem->setProductOptions($options);

                    }
                }
            }
        }

        return $this;
    }


    private function getQuoteItemById($id)
    {
        $this->_logger->info("--- getQuoteItemById");
        if(empty($this->quoteItems))
        {
            $this->_logger->info("--- --- looping through quote items");
            /* @var  \Magento\Quote\Model\Quote\Item $item */
            foreach($this->quote->getItems() as $item)
            {
                $parent_id = $item->getParentItemId();
                $product_type = $item->getProductType();
                $itemId = $item->getId();
                $this->_logger->info("--- --- parent_id: " . $parent_id );
                $this->_logger->info("--- --- product_type: " . $product_type);
                $this->_logger->info("--- --- item ID: " . $itemId );
                //filter out config/bundle etc product
                if(!$parent_id /* && $product_type == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE */){
                    $this->_logger->info("--- adding " . $itemId);
                    $this->quoteItems[$itemId] = $item;
                }
            }
        }
        if(array_key_exists($id, $this->quoteItems))
        {
            $this->_logger->info("--- returning item");
            return $this->quoteItems[$id];
        }
        $this->_logger->info("--- returning null");
        return null;
    }
}
