<?php

namespace Ekr\Cart\Model;

abstract class AbstractItem extends \Magento\Quote\Model\Quote\Item\AbstractItem {
    
    /**
     * Checking item data
     *
     * @return $this
     */
    public function checkData()
    {
        $abstract_item->setHasError(false);
        $abstract_item->clearMessage();

        $qty = $abstract_item->_getData('qty');

        try {
            $abstract_item->setQty($qty);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $abstract_item->setHasError(true);
            $abstract_item->setMessage($e->getMessage());
        } catch (\Exception $e) {
            $abstract_item->setHasError(true);
            $abstract_item->setMessage(__('Item qty declaration error'));
        }

        try {
            $abstract_item->getProduct()->getTypeInstance()->checkProductBuyState($abstract_item->getProduct());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $abstract_item->setHasError(true)->setMessage($e->getMessage());
            $abstract_item->getQuote()->setHasError(
                true
            )->addMessage(
                __('Some of the products below do not have all the required options.')
            );
        } catch (\Exception $e) {
            $abstract_item->setHasError(true)->setMessage(__('Something went wrong during the item options declaration.'));
            $abstract_item->getQuote()->setHasError(true)->addMessage(__('We found an item options declaration error.'));
        }

        if ($abstract_item->getProduct()->getHasError()) {
            $abstract_item->setHasError(true)->setMessage(__('Some of the selected options are not currently available.'));
            $abstract_item->getQuote()->setHasError(true)->addMessage($abstract_item->getProduct()->getMessage(), 'options');
        }

        if ($abstract_item->getHasConfigurationUnavailableError()) {
            $abstract_item->setHasError(
                true
            )->setMessage(
                __('Selected option(s) or their combination is not currently available.')
            );
            $abstract_item->getQuote()->setHasError(
                true
            )->addMessage(
                __('Some item options or their combination are not currently available.'),
                'unavailable-configuration'
            );
            $abstract_item->unsHasConfigurationUnavailableError();
        }

        return $abstract_item;
    }
}
