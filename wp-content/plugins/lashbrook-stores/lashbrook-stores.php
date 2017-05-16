<?php defined( 'ABSPATH' ) or die( '*!' );

/*
Plugin Name: Lashbrook Stores
Plugin URI:  http://ekragency.com/
Description: Tracks store data for the locater maps
Version:     0.5
Author:      Brent Allen
Author URI:  http://bwallen.info
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: lashbrook-stores
*/

add_action( 'admin_menu', 'stores_menu' );

function stores_menu(){
	add_options_page("Store Locations", "Store Locations", "publish_pages", "store_locations", "stores_page");
}

function store_admin_init() {
    /* Register our script. */
    wp_enqueue_script('jquery-datatables',get_template_directory_uri().'/js/jquery.dataTables.min.js');

    wp_register_style('jquery.dataTables.css', get_template_directory_uri().'/css/jquery.dataTables.css');
    wp_enqueue_style('jquery.dataTables.css');
}

add_action( 'admin_init', 'store_admin_init' );

function stores_page(){
	global $wpdb;
	$db = create_store_tables();
	if(!empty($_POST)){
		saveStore($db);
	}

	$stores = $db->query("SELECT * FROM `{$wpdb->prefix}stores`");
	$collections = $db->query("SELECT * FROM `{$wpdb->prefix}collections`");
	$loadedStore = (Object) array(
		"id" => "",
		"name" => "",
		"address" => "",
		"city" => "",
		"state" => "",
		"zip" => "",
		"phone" => "",
		"website" => "",
		"hours_open" => ""
	);

	$relatedMetals = array();


	if(!empty($_GET['id']) && is_numeric($_GET['id'])){
		$storeId = (int) $_GET['id'];
		$reslt = $db->query("SELECT * FROM `{$wpdb->prefix}stores` WHERE `id` = $storeId");
		$loadedStore = $reslt->fetch_object();
		$metals = $db->query("SELECT `collection_id` FROM `{$wpdb->prefix}stores_has_collections` WHERE `store_id` = $loadedStore->id");
		if($metals){
			foreach($metals as $metal){
				$relatedMetals[] = $metal['collection_id'];
			}
		}

	}
	?>
	<div class="wrap">
	<h1>
		Store Locations
	</h1>
	<table id="stores" class="wp-list-table widefat fixed striped posts">
		<thead>
			<tr>
				<th scope="col" id="title" class="manage-column column-title column-primary">Store</th>
				<th scope="col" id="author" class="manage-column ">Address</th>
				<th scope="col" id="categories" class="manage-column column-categories">Phone</th>
				<th scope="col" id="tags" class="manage-column column-tags">Website</th>
				<th scope="col" id="comments" class="manage-column column-comments num">Map</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($stores as $store){
			$store = (Object) $store;
			?>
			<tr>
				<td><a href="?page=store_locations&id=<?= $store->id ?>"><?= $store->name ?></a></td>
				<td><?= "$store->address $store->city $store->state, $store->zip" ?></td>
				<td><?= $store->phone ?></td>
				<td><a href="<?= $store->website ?>" target="_blank"><?= $store->website ?></a></td>
				<td><a href="http://www.google.com/maps/place/<?= $store->lat ?>,<?= $store->lng ?>" target="_blank">Map</a></td>
			</tr>
			<?php
		} ?>
		</tbody>
	</table>
	<h2>
		<?= empty($storeId) ?  "New Store":"Edit Store" ?>
	</h2>
		<?php if(!empty($storeId)){
		?><a href="?page=store_locations">New Store</a> <?php
	} ?>
	<form enctype="multipart/form-data" method="post">
		<table class="form-table" >
			<input type="hidden" name="id" value="<?= $loadedStore->id ?>" />
			<tbody>
				<tr>
					<th scope="row">
						<label for="store_name">
							Store Name
						</label>
					</th>
					<td>
						<input id="store_name" type="text" name="store_name" class="regular-text" value="<?= $loadedStore->name ?>" required />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="address">Address</label>
					</th>
					<td>
						<textarea id="address" name="address" class="regular-text" required ><?= $loadedStore->address ?></textarea>
					</td>
				</tr>
				<tr>
					<th scpoe="row">
						<label for="city">City</label>
					</th>
					<td>
						<input id="city" name="city" type="text" class="regular-text" value="<?= $loadedStore->city ?>" required />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="state">State Code</label>
					</th>
					<td>
						<input id="state" name="state" maxlength="2" class="small-text" value="<?= $loadedStore->state ?>" required />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="zip">Zip</label>
					</th>
					<td>
						<input id="zip" name="zip" type="text" class="regular-text" value="<?= $loadedStore->zip ?>" required />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="country">Country</label>
					</th>
					<td>
						<select name="country">
							<option value="US"<?php if($loadedStore->country == "US"){echo "selected";} ?>>United States</option>
							<option value="CA"<?php if($loadedStore->country == "CA"){echo "selected";} ?>>Canada</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="phone">Phone</label>
					</th>
					<td>
						<input type="phone" name="phone" class="regular-text" value="<?= $loadedStore->phone ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="website">Website</label>
					</th>
					<td>
						<input id="website" name="website" class="regular-text" value="<?= $loadedStore->website ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="hours_open">Hours</label>
					</th>
					<td>
						<input id="hours_open" name="hours_open" class="regular-text" value="<?= $loadedStore->hours_open ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="logo">Logo URL</label>
					</th>
					<td>
						<input id="hours_open" name="logo" class="regular-text" value="<?= $loadedStore->logo ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						Metals
					</th>
					<td>
						<?php
							foreach($collections as $collection){
								$collection = (Object) $collection;
								?><label style="margin-right: 1.5em"><input name="collections[]" type="checkbox" value="<?= $collection->id ?>" <?= in_array($collection->id, $relatedMetals) ? "checked":"" ?> /> <?= $collection->name ?></label> <?php
							}
						?>
						<label style="display:block;">New Metals (comma separated) <input name="new_collections" /> </label>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save"></p>
	</form>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $("#stores").DataTable({});
    });
</script>
	<?php
}

function createDBConnection(){
	$db = new MySQLi(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if($db->connect_errno){
		die("Connection failure $db->connect_errno: $db->connect_error");
	}

	return $db;
}

function saveStore(MySQLI $db){
	global $wpdb;
	$data = (Object) filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

	if(empty($data) || empty($data->store_name) || empty($data->address)){
		return;
	}

	$searchAddress = "$data->address $data->city $data->state, $data->zip";
	$coords = GMGetCoordinates($searchAddress);

	if(empty($data->id)){
		$dt = $db->query("INSERT INTO `{$wpdb->prefix}stores` (`name`,`address`,`city`,`state`,`zip`,`country`,`phone`,`website`,`hours_open`,`logo`,`lng`,`lat`) VALUES 
		('$data->store_name','$data->address','$data->city','$data->state','$data->zip','$data->country','$data->phone', '$data->website','$data->hours_open','$data->logo','$coords->lng', '$coords->lat')");
		
		if($dt){
			$store = $db->query("SELECT * FROM `{$wpdb->prefix}stores` ORDER BY `created` DESC LIMIT 1")->fetch_object();
		}
	} else {
		
		$db->query("UPDATE `{$wpdb->prefix}stores` SET `name` = '$data->store_name',`address` = '$data->address', `city` = '$data->city', `state` = '$data->state', `zip` = '$data->zip', `country` = '$data->country', `phone` = '$data->phone', `website` = '$data->website', `hours_open` = '$data->hours_open', `logo` = '$data->logo', `lng` = $coords->lng, `lat` = $coords->lat WHERE `id` = $data->id;");
		
		$store = $db->query("SELECT * FROM `{$wpdb->prefix}stores` WHERE `id` = $data->id")->fetch_object();
	}

	if(empty($store) || empty($store->id)){
		return false;
	}

	$newCollections = explode(",", trim($data->new_collections));
	$collectionIds = array();

	if(!empty($newCollections)){
		foreach($newCollections as $new){
			$new = trim($new);
			if(empty($new)){
				break;
			}
			$db->query("INSERT INTO `{$wpdb->prefix}collections` (`name`) VALUES ('$new')");
			$col = $db->query("SELECT `id` FROM `{$wpdb->prefix}collections` WHERE `name` = '$new' ORDER BY `id` DESC LIMIT 1")->fetch_object();
			$collectionIds[] = $col->id;
		}
	}

	if(!empty($data->collections)){
		foreach($data->collections as $colId){
			$collectionIds[] = $colId;
		}
	}

	$db->query("DELETE FROM `{$wpdb->prefix}stores_has_collections` WHERE `store_id` = $store->id");

	$shcQuery = "INSERT INTO `{$wpdb->prefix}stores_has_collections` (`store_id`, `collection_id`) VALUES ";

	foreach($collectionIds as $cid){
		$shcQuery .= "($store->id, $cid),";
	}

	$shcQuery = substr($shcQuery, 0, -1).";";
	$db->query($shcQuery);
	return $store;
}

function GMGetCoordinates($address) {

    $address = urlencode($address);

    $url = "http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false";

    $ch = curl_init();
    $options = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_URL            => $url,
        CURLOPT_HEADER         => false,
    );

    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    curl_close($ch);

    if (!$response) {
        return false;
    }

    $response = json_decode($response);

    if ($response->status !== 'OK') {
        return false;
    }

    $ret = $response->results[0]->geometry->location;


    return $ret;
}

function create_store_tables(){
	global $wpdb;
	$db = createDBConnection();
	$query[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}stores` (
	  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `name` VARCHAR(256) NOT NULL,
	  `address` VARCHAR(512) NOT NULL,
	  `city` VARCHAR(90) NOT NULL,
	  `state` CHAR(2) NOT NULL,
	  `zip` VARCHAR(15) NOT NULL,
	  `country` CHAR(2) DEFAULT 'US',
	  `phone` VARCHAR(20) NULL,
	  `website` VARCHAR(90) NULL,
	  `hours_open` VARCHAR(90) NULL,
	  `logo` VARCHAR(256) NULL,
	  `lng` DOUBLE NULL,
	  `lat` DOUBLE NULL,
	  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	  PRIMARY KEY (`id`))
	ENGINE = InnoDB;";


	$query[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}collections` (
	  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	  `name` VARCHAR(90) NULL,
	  PRIMARY KEY (`id`))
	ENGINE = InnoDB;";


	$query[] = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}stores_has_collections` (
	  `store_id` INT UNSIGNED NOT NULL,
	  `collection_id` INT UNSIGNED NOT NULL,
	  PRIMARY KEY (`store_id`, `collection_id`),
	  INDEX `fk_stores_has_collections_collections1_idx` (`collection_id` ASC),
	  INDEX `fk_stores_has_collections_stores_idx` (`store_id` ASC),
	  CONSTRAINT `fk_stores_has_collections_stores`
		FOREIGN KEY (`store_id`)
		REFERENCES `{$wpdb->prefix}stores` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	  CONSTRAINT `fk_stores_has_collections_collections1`
		FOREIGN KEY (`collection_id`)
		REFERENCES `{$wpdb->prefix}collections` (`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE)
	ENGINE = InnoDB;";
	$success = true;
	foreach($query as $q){
		if($success){
			$success = $db->query($q);
		}
	}


	if(!$success){
		echo "<pre> $db->errno: $db->error

		$query[0]

		$query[1]

		$query[2]

		</pre>";
	}

	return $db;
}
