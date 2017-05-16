<?php
/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ekr\Sales\Controller\AbstractController;

class OrderViewAuthorization implements \Magento\Sales\Controller\AbstractController\OrderViewAuthorizationInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $orderConfig;

    /**
     * Attribute id
     * @var integer
     */
    private $parent_account_att_id = 149;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig
    ) {
        $this->customerSession = $customerSession;
        $this->orderConfig = $orderConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function canView(\Magento\Sales\Model\Order $order)
    {
        $customerId = $this->customerSession->getCustomerId();
        $availableStatuses = $this->orderConfig->getVisibleOnFrontStatuses();
        if ($order->getId()
            && $order->getCustomerId()
            && ($order->getCustomerId() == $customerId || $this->canViewAsParent($order,$customerId))
            && in_array($order->getStatus(), $availableStatuses, true)
        ) {
            return true;
        }
        return false;
    }

    /**
     * check to see if the order can be viewed by a parent
     * @param  \Magento\Sales\Model\Order   $order          Order object
     * @param  int                          $parent_id      Customer Record entity ID
     * @return boolean true if user can see order
     */
    private function canViewAsParent($order,$parent_id){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $dbResource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $dbConnection = $dbResource->getConnection();
        $tableName = $dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT count(*) as count FROM " . $tableName . " WHERE `entity_id`='" . $order->getCustomerId(). "' AND `attribute_id`='{$this->parent_account_att_id}' AND value='{$parent_id}'";
        $result = $dbConnection->fetchAll($sql);
        return ($result[0]['count']);
    }
}
