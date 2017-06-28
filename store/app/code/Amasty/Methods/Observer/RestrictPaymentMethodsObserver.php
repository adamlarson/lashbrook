<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Methods
 */


namespace Amasty\Methods\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class RestrictPaymentMethodsObserver extends \Amasty\Methods\Model\Manager
    implements ObserverInterface
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ){
        parent::__construct($request);
        $this->_objectManager = $objectManager;
    }

    /**
     * @param $websiteId
     * @return \Amasty\Methods\Model\Structure
     */
    public function getMethodsStructure($websiteId)
    {
        if (!array_key_exists($websiteId, $this->_structures)){
            $this->_structures[$websiteId] = $this->_objectManager->create('\Amasty\Methods\Model\Structure\Payment')
                ->load($websiteId);
        }
        return $this->_structures[$websiteId];
    }

    public function execute(EventObserver $observer)
    {
        $event = $observer->getEvent();
        $methodInstance = $event->getMethodInstance();

        if ($quote = $event->getQuote()){
            $structure = $this->getMethodsStructure(
                $this->getWebsiteId($quote->getStore()->getWebsiteId())
            );

            if ($structure->getSize() > 0) {
                $result = $observer->getEvent()->getResult();
                $result->setData('is_available', false);

                $methodGroups = $structure->get($methodInstance->getCode());

                if ($methodGroups) {
                    $groupsIds = $methodGroups->getGroupIds();

                    if ($structure->validate(
                        $quote->getCustomerGroupId(),
                        $groupsIds)
                    ){
                        $result->setData('is_available', true);
                    }
                }
            }
        }
    }
}
