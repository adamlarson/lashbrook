<?php

require_once( '../../../wp-load.php' );

$db = new MySQLi(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

$ret = (Object) array(
	"success" => false,
	"message" => "Something went wrong....",
	"stores" => array()
);

if($db->connect_errno){
	$ret->message = "Connection failure $db->connect_errno: $db->connect_error";
}

$stores = $db->query("SELECT * FROM `{$wpdb->prefix}stores`");

if($stores){
	foreach($stores as $store){
		$store = (Object) $store;
		$storeData = new \StdClass();
		$storeData->name = $store->name;
		$storeData->position = (Object) array("lat" => $store->lat, "lng" => $store->lng);
		$address = nl2br($store->address);
		$storeData->address = "$address <br>$store->city, $store->state $store->zip";
		$storeData->url = $store->website;
		$storeData->phone = $store->phone;
		$storeData->hours = $store->hours_open;
		$storeData->logo = $store->logo;
		$matQuery = "SELECT * FROM {$wpdb->prefix}stores_has_collections RIGHT JOIN {$wpdb->prefix}collections ON {$wpdb->prefix}stores_has_collections.collection_id={$wpdb->prefix}collections.id WHERE {$wpdb->prefix}stores_has_collections.store_id = $store->id";
		$materials = $db->query($matQuery);
		$storeData->materials = array();
		foreach($materials as $material){
			$storeData->materials[] = $material['name'];
		}
		$ret->stores[] = $storeData;
	}

	if(count($ret->stores) > 0){
		$ret->success = true;
		$ret->message = "success";
	}
}



echo json_encode($ret);