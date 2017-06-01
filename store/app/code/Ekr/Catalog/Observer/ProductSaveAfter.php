<?php

namespace Ekr\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Magento observer after a product is saved.
 */
class ProductSaveAfter implements ObserverInterface
{
	/**
	 * catalog logger
	 * @var \Ekr\Catalog\Logger\Logger $_logger
	 */
	protected $_logger;

	public function __construct(
        \Ekr\Catalog\Logger\Logger $logger){
		$this->_logger = $logger;
	}

	/**
	 * execute observer when catalog_product_save_after is dispatched
	 * check if a simple product was updated.
	 * @param  \Magento\Framework\Event\Observer $observer 
	 * @return void
	 */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $_product = $observer->getProduct();
        $product_id =$_product->getId();
        $type_id = $_product->getTypeId();
        $this->_logger->info("{$product_id}:{$type_id}");
        switch($type_id){
        	case "simple":
	        	// database connection
	        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	        	$dbResource = $objectManager->get('Magento\Framework\App\ResourceConnection');
	        	$dbConnection = $dbResource->getConnection();
	        	$tableName = $dbResource->getTableName("catalog_product_entity"); //gives table name with prefix
	        	// reset options that makes simple products no longer show up as parent option.
	        	$query = "UPDATE {$tableName} SET `has_options`=0,`required_options`=0 WHERE `entity_id`='{$product_id}'";
	        	$this->_logger->info($query);
	        	$dbConnection->query($query);
        	break;
        }
    }   
}