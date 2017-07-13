<?php
namespace Ekr\Sales\Block\Order;

class View extends \Magento\Sales\Block\Order\View{

	/**
  	 * logger object
  	 * @var \Ekr\Sales\Logger\Logger
  	 */
	protected $_logger;

	/**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

	protected $objectManager;
    protected $dbResource;
    protected $dbConnection;

    /**
     * Attribute id
     * @var integer
     */
    private $store_name_att_id = 151;
    private $parent_account_att_id = 149;
    private $parent_approval_att_id = 152;

	/**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param array $data
     */
    public function __construct(
    	\Ekr\Sales\Logger\Logger $logger,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
    	$this->_logger = $logger;
    	$this->_customerSession = $customerSession;
    	$this->_logger->info("View Construct");
    	parent::__construct($context,$registry,$httpContext,$paymentHelper,$data);
    	$this->_logger->info("After parent construct");

    	$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $this->dbResource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->dbConnection = $this->dbResource->getConnection();
    }

	public function getEauth(){
		$this->_logger->info("getting eauth");
		// get database connection
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		// $this->_logger->info("got objectManager");
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		// $this->_logger->info("got resource");
		$connection = $resource->getConnection();
		// $this->_logger->info("got connection");
		$tableName = $resource->getTableName("sales_order_eauth_tokens"); //gives table name with prefix
		$this->_logger->info("got tablename {$tableName}");
		// order id
		$order_id = $this->getOrder()->getId();
		// $this->_logger->info("got order id {$order_id}");
		$sql = "Select * FROM " . $tableName . " WHERE `sales_order_id` = '{$order_id}'";
		// $this->_logger->info("query: {$sql}");
		$result = $connection->fetchAll($sql);
		// $this->_logger->info("results " . print_r($result));
		 return $result[0];
		//return "Some string right now";
	}

	/**
     * Get the name of the store associated with a customer
     * @param  int $customer_id     Customer Record entity_id
     * @return string               store name
     */
    public function getStoreName($customer_id){
        $tableName = $this->dbResource->getTableName("customer_entity_varchar"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->store_name_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : "";
        return $value;
    }

    /**
     * check and see if current user can approve order.
     * @return [type] [description]
     */
    public function canApprove(){
    	$order = $this->getOrder();
    	$customer_id = $order->getCustomerId();
    	$loggedId = $this->_customerSession->getCustomerId();
    	$parent_id = $this->getParentAccountId($customer_id);
    	// if customer has a parent.
    	if($parent_id){
    		// check if user requires parent approval and 
    		// the parent is not logged in
    		if( $this->requiresParentApproval($customer_id) && 
    			$loggedId == $parent_id){
    				return true;
    		}
    	}

    	return false;

    }

    /**
     * check if the current account is a parent account
     * @param  int  $customer_id    Customer Record entity_id
     * @return boolean              true if is parent account
     */
    private function getParentAccountId($customer_id){
        
        $tableName = $this->dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->parent_account_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : 0;
        return $value;
    }

    private function requiresParentApproval($customer_id){
    	$tableName = $this->dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->parent_approval_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : 0;
        return $value;
    }
}
