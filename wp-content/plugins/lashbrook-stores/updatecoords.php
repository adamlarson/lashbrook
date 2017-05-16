<?php

require_once( '../../../wp-load.php' );

$db = new MySQLi(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if($db->connect_errno){
    echo "Connection failure $db->connect_errno: $db->connect_error";
}

$stores = $db->query("SELECT * FROM `{$wpdb->prefix}stores` WHERE `lat` IS NULL");

foreach($stores as $store){
    $store = (Object) $store;
    echo ".";
    $searchAddress = "$store->address $store->city $store->state, $store->zip";
    $coords = GMGetCoordinates($searchAddress);
    $db->query("UPDATE `{$wpdb->prefix}stores` SET `lng` = $coords->lng, `lat` = $coords->lat WHERE `id` = $store->id");
    sleep(1); //google only allows so many requests per second. Wait a sec.
}

echo "\r\n\r\n";