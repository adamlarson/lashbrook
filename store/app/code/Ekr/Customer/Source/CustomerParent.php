<?php

namespace Ekr\Customer\Source;

class CustomerParent extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

	/**
	 * ID for ekr_is_parent_account
	 * @var integer
	 */
	const ekr_is_parent_account_att_id = 150;
	const ekr_store_name_att_id = 151;


    /**
     * Retrieve options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
	   	list($connection,$tableName) = self::getDatabaseConnection("customer_entity_int");
        $sql = "Select * FROM " . $tableName . " WHERE `attribute_id`='". self::ekr_is_parent_account_att_id ."' AND `value`=1";
        $results = $connection->fetchAll($sql);
        $accountids = [
        	0 => "No Parent"
        ];
        if(!empty($results)){
        	foreach($results as $result){
        		$entity_id = $result['entity_id'];
        		// load customer.
        		$customer = self::getCustomerData($entity_id);
        		if(empty($customer)) continue;
        		$name = $customer['store'] . " : " . $customer['firstname'] . " " . $customer['lastname'];
        		$accountids[$customer['entity_id']] = $name;
        	}
        }
        
        return $accountids;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * get database connection and table name
     * @param  str $table table to connect to
     * @return array connection and actual table name
     */
    public static function getDatabaseConnection($table){
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName($table); //gives table name with prefix
        return [$connection,$tableName];
    }

    /**
     * get table row data for a customer
     * @param  int $entity_id  customer ID
     * @return array customer data
     */
    private static function getCustomerData($entity_id){
    	list($connection,$tableName) = self::getDatabaseConnection("customer_entity");
    	$sql = "SELECT * FROM {$tableName} WHERE entity_id='{$entity_id}' AND `is_active`='1'";
    	$result = $connection->fetchAll($sql);
    	$customer = [];
    	if(isset($result[0])){
    		$customer = $result[0];
    		$customer['store'] = self::getStoreName($entity_id);
    	}
    	return $customer;
    }

    /**
     * get the name of the store associated with a customer.
     * @param  int $entity_id customer ID
     * @return string store name (if any)
     */
    private static function getStoreName($entity_id){
    	list($connection,$tableName) = self::getDatabaseConnection("customer_entity_varchar");
    	$sql = "SELECT * FROM {$tableName} WHERE entity_id='{$entity_id}' AND `attribute_id`='" . self::ekr_store_name_att_id ."'";
    	$result = $connection->fetchAll($sql);
    	return (isset($result[0]))? $result[0]['value'] : "";
    }
}