<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Methods
 */

namespace Amasty\Methods\Model;

class ShippingFactory extends ObjectFactory
{
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Amasty\\Methods\\Model\\Shipping')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }
}