<?php

/**
 * 	Plugin Name: Lashbrook Magento Integration
 * 	Description: Handle order agreement and status from Workpress
 * 	Version: 1.0
 * 	Author: EKR Agency
 * 	Author URI: http://www.ekragency.com
 */

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

require __DIR__ . '/vendor/autoload.php';

if(!function_exists('print_debug')){
	function print_debug($data,$exit = false){
		echo '<pre>'; print_r($data);
		if($exit) die();
		echo '</pre>';
	}
}

if(is_plugin_active("lashbrook-magento-integration/lashbrook-magento-integration.php" )){

	define('LASHBROOK_SALE_VERIFICATION_SLUG','lashbrook-order-verification');
	define('LASHBROOK_VIEW_PATH',plugin_dir_path(__FILE__) . "src/Views/");
	define('LASHBROOK_LAYOUT_PATH',plugin_dir_path(__FILE__) . "src/Views/layouts/");
	define('LASHBROOK_DEFAULT_POST_ID',241); // post id for content
	define('LASHBROOK_SIMPLE_TEMPLATE',plugin_dir_path(__FILE__) . "inc/template-simple.php");
	define('LASHBROOK_ASSETS_URL',plugin_dir_url(__FILE__ ) ."src/assets/");
	define('LASHBROOK_ASSET_VERSION','3.0');

	$charset = "utf8";
    $collation = "utf8_general_ci";

	// connect to magento database.
	$lashbrook_capsole = new Illuminate\Database\Capsule\Manager;
	$lashbrook_capsole->addConnection([
		'driver' => 'mysql',
		'host' => DB_HOST,
		'database' => 'magento',
		'username' => DB_USER,
		'password' => DB_PASSWORD,
		'charset' => $charset,
		'collation' => $collation,
		'prefix' => "m_",
		''
	],'magento');

	$lashbrook_capsole->bootEloquent();

	$lashbrook_orderController = new Lashbrook\Controllers\OrdersController();

	/**
	 * load page based on url for custom plugin actions
	 * @return void
	 */
	
/*	function lashbrook_checkOrderVerification(){
		global $lashbrook_orderController;
		$full_url = lashbrook_getCurrentUrl();
		$home_url = get_home_url();
		$url = str_replace($home_url,"",$full_url);
		$url = trim(parse_url($url, PHP_URL_PATH), '/');
        $parts = explode('/',$url);
        // sales verification.
        if (count($parts) == 2 && @$parts[0] == LASHBROOK_SALE_VERIFICATION_SLUG && !preg_match('/[^a-z0-9]/i',$parts[1])){
        	$lashbrook_orderController->initializeOrderPage($parts[1]);
        }
	}*/

/*	function lashbrook_content_filter( $content ) {
		print_debug("filtering");
    	if(!isset($lashbrook_orderToken)) return $token;
     	$Controller = new Lashbrook\Controllers\OrdersController;
     	return $Controller->loadOrder($lashbrook_orderToken);
	}*/

	function lashbrook_getCurrentUrl()
	{
	    $currentURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
	    $currentURL .= $_SERVER["SERVER_NAME"];

	    if($_SERVER["SERVER_PORT"] != "80" && $_SERVER["SERVER_PORT"] != "443")
	    {
	        $currentURL .= ":".$_SERVER["SERVER_PORT"];
	    } 

	        $currentURL .= $_SERVER["REQUEST_URI"];
	    return $currentURL;
	}
}