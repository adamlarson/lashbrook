<?php
namespace Ekr\Catalog\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $objectManager;
    protected $dbResource;
    protected $dbConnection;

    // hard coded attributes.
    protected $ekr_price_multiplier = 156;
    //protected $finish_attribute_id = 145;
    protected $price_attribute_id = 77;
    //protected $weight_attribute_id = 158;

    protected $price_multiplier;

    protected $_logger;
    protected $_catalogProductTypeConfigurable;
    protected $_productloader;

    /**
     * constructor
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session       $customerSession
     */
    public function __construct(
        \Ekr\Catalog\Logger\Logger $logger,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $catalogProductTypeConfigurable,
        \Magento\Catalog\Model\ProductFactory $productLoader,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_logger = $logger;
        $this->customerSession = $customerSession;
        $this->_catalogProductTypeConfigurable = $catalogProductTypeConfigurable;
        $this->_productloader = $productLoader;
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

    function getProductUrl($product,$additional = []){
        $product = $this->loadProduct($product->getEntityId());
        $this->_logger->info("getting product url");
        $this->_logger->info("get parent id for " . $product->getEntityId());
        // check if product is has a parent
        $parentByChild = $this->_catalogProductTypeConfigurable->getParentIdsByChild($product->getEntityId());
        $parent_id = null;
        $this->_logger->info("parentByChild: " . $parentByChild[0]);
        if(isset($parentByChild[0])){
            $parent_id = $parentByChild[0];          
        }
        $this->_logger->info("parent_id: " . $parent_id);
        // if product has a parent
        if($parent_id){
            $configurable = $this->loadProduct($parent_id);
            $this->_logger->info("got configurable: " . $parent_id );
            $url = $this->getDefaultUrl($configurable,$additional); 
            $this->_logger->info("default url" . $url);
            // get attribute values
            $base_metal = $product->getData('base_metal');
            $inlay = $product->getData('inlay');
            $weight = $product->getData('ring_weight_is_p');
            $finish = $product->getData('image_finish');
            //$weight = $this->getProductAttribute($product->getEntityId(),$this->weight_attribute_id,'catalog_product_entity_int');
            //$finish = $this->getProductAttribute($product->getEntityId(),$this->finish_attribute_id);

            $this->_logger->info("base_metal:" . $base_metal);
            $this->_logger->info("inlay:" . $inlay);
            $this->_logger->info("finish:" . $finish);
            $this->_logger->info("ring weight:" . $weight);

            $url .= (strpos($url,"?") === false)? "?" : "&";
            $url .= "base_metal={$base_metal}";
            if(!empty($inlay)) $url .= "&inlay={$inlay}";
            if(!empty($finish)) $url .= "&finish={$finish}";
            if(!empty($weight)) $url .= "&weight={$weight}";

            $this->_logger->info("final url" . $url);
        }else{
            $url = $this->getDefaultUrl($product,$additional);
        }

        $this->product_urls[$product->getEntityId()] = $url;
        return $url;
    }

    /**
     * load product object from id
     * @param  int $id product_id
     * @return product \Magento\Catalog\Model\Product
     */
    public function loadProduct($id)
    {
        return $this->_productloader->create()->load($id);
    }

    /**
     * get product base url
     * @param  object $product \Magento\Catalog\Model\Product
     * @return string
     */
    public function getDefaultUrl($product,$additional){
        $url = "/store/catalog/product/view/id/" . $product->getEntityId() . "/";
        if(@$additional['category_id']){
            $url .= "?category_id=" . $additional['category_id'];
        }
        return $url;
    }

    /**
     * get attribute directly from table.
     * @param  int $entity_id    product id
     * @param  int $attribute_id attribute id
     * @return return value
     */
    public function getProductAttribute($entity_id,$attribute_id,$table = "catalog_product_entity_varchar"){

        if(isset($this->product_attribute_values[$attribute_id][$entity_id])) return $this->product_attribute_values[$attribute_id][$entity_id];

        $this->_logger->info("getting " . $attribute_id);
        
        // $this->_logger->info("got connection");
        $tableName = $this->dbResource->getTableName($table); //gives table name with prefix
        $this->_logger->info("got tablename {$tableName}");
        // $this->_logger->info("got order id {$order_id}");
        $sql = "Select value FROM " . $tableName . " WHERE `entity_id` = '{$entity_id}' AND `attribute_id`='{$attribute_id}'";
        // $this->_logger->info("query: {$sql}");
        $result = $this->dbConnection->fetchAll($sql);
        // $this->_logger->info("results " . print_r($result));
        $value = (@$result[0]['value'])? $result[0]['value'] : "";
        
        $this->product_attribute_values[$attribute_id][$entity_id] = $value;

         return $value;
    }
}
