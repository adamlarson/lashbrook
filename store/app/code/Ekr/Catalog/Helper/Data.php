<?php
namespace Ekr\Catalog\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $objectManager;
    protected $dbResource;
    protected $dbConnection;

    protected $ekr_price_multiplier = 156;

    protected $price_multiplier;

    /**
     * constructor
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session       $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        parent::__construct($context);

        // database connection
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $this->dbResource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->dbConnection = $this->dbResource->getConnection();
    }

    /**
     * check wether customer is logged in
     * @return boolean true if logged in.
     */
    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * get price multiplier for customer.
     * @return [type] [description]
     */
    public function getCustomerPriceMultiplier(){
        // if customer is logged in.
        if($this->isLoggedIn()){
            //if($this->price_multiplier) return $this->price_multiplier;
            // get value from database.
            $customer_id = $this->customerSession->getCustomer()->getId();
            $tableName = $this->dbResource->getTableName("customer_entity_varchar"); //gives table name with prefix
            $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->ekr_price_multiplier}'";
            $result = $this->dbConnection->fetchAll($sql);
            $value = (@$result[0]['value'])? $result[0]['value'] : 3;
            $this->price_multiplier = $value;
            return $value;
        }else{
            return 3; // retail price.
        }
    }
}
