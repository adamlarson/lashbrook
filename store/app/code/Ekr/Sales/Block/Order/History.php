<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ekr\Sales\Block\Order;

use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Sales order history block
 */
class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'order/history.phtml';

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;

    /**
     * @var CollectionFactoryInterface
     */
    private $orderCollectionFactory;

    /**
     * Attribute id
     * @var integer
     */
    private $is_parent_account_att_id = 150;

    /**
     * Attribute id
     * @var integer
     */
    private $parent_account_att_id = 149;

    /**
     * Attribute id
     * @var integer
     */
    private $store_name_att_id = 151;

    protected $objectManager;
    protected $dbResource;
    protected $dbConnection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;

        $this->objectManager = ObjectManager::getInstance(); // Instance of object manager
        $this->dbResource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->dbConnection = $this->dbResource->getConnection();

        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Orders'));
    }

    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }

        if (!$this->orders) {
            $this->orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            );

            if($this->_ekrIsParentAccount($customerId)){
                // filter on status and child accounts
                $statuses = $this->_orderConfig->getVisibleOnFrontStatuses();
                $entities = $this->_ekrGetChildAccounts($customerId);
                $this->orders
                        ->addFieldToFilter('status',['in' => $statuses])
                        ->addFieldToFilter('customer_id',['in' => $entities]);
            }else{
                // display my orders only.
                $this->orders
                        ->addFieldToFilter('customer_id',$customerId)
                        ->addFieldToFilter('status',['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]);
            }
            // display order
            $this->orders->setOrder(
                'created_at',
                'desc'
            );
        }
        return $this->orders;
    }

    /**
     * check if the current account is a parent account
     * @param  int  $customer_id    Customer Record entity_id
     * @return boolean              true if is parent account
     */
    private function _ekrIsParentAccount($customer_id){
        
        $tableName = $this->dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->is_parent_account_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : 0;
        return ($value)? true : false;
    }

    /**
     * get list of child accounts that belong to this customer.
     * @param  int $customer_id     Customer Record entity_id
     * @return array                List of entity_ids
     */
    private function _ekrGetChildAccounts($customer_id){
        $tableName = $this->dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT entity_id FROM " . $tableName . " WHERE `attribute_id`='{$this->parent_account_att_id}' AND `value` = '{$customer_id}'";
        $results = $this->dbConnection->fetchAll($sql);
        $entity_ids = [];
        if(!empty($results)){
            foreach($results as $result){
                $entity_ids[] = $result['entity_id'];
            }
        }
        $entity_ids[] = $customer_id; // add parent to list
        return $entity_ids;
    }

    /**
     * Get the name of the store associated with a customer
     * @param  int $customer_id     Customer Record entity_id
     * @return string               store name
     */
    public function _ekrGetStoreName($customer_id){
        $tableName = $this->dbResource->getTableName("customer_entity_varchar"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->store_name_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : "";
        return $value;
    }

    /**
     * check if user has access to reorder for selected order
     * @param  \Magento\Sales\Model\Order $order 
     * @return boolean
     */
    public function _ekrCanReorder($order){
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if($customerId != $order->getCustomerId()) return false;
        return true;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('sales/order/track', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
