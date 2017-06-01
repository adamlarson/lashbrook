<?php 
	
	namespace Lashbrook\Controllers;

	use Lashbrook\Models\SalesOrder;
	use Lashbrook\Models\SalesOrderEauthToken;
	use Lashbrook\Controllers\AppController;

	use Lashbrook\Services\MagentoService;

	class OrdersController extends AppController{

	protected $order_token = 0;
	protected $nonce_action = 'lashbrook-nonce';

	protected $authorize_order_action = "lashbrook_authorize_order";

	public function __construct(){
		// ajax calls
		add_action('wp_ajax_' . $this->authorize_order_action,[&$this,'authorizeOrder'],10);
		add_action('wp_ajax_nopriv_' . $this->authorize_order_action,[&$this,'authorizeOrder'],10);
		
		add_filter('the_posts', array(&$this, 'loadOrder'));
		add_filter('template_include', [&$this,'overwriteTemplate'], 99 );
		add_action('wp_enqueue_scripts',[&$this,'loadPublicScripts']);
		add_action('wp_head',[&$this,'loadPublicStyles']);

		// filters
		add_filter('query_vars',[&$this,'virtualpageQueryVars']);
		// actions
		add_action('init', [&$this,'virtualpageAddRewriteRules']);
	}

	/**
	 * Add query vars for order verification
	 * @param  array $vars current vars
	 * @return array modified array
	 */
	public function virtualpageQueryVars($vars) {
  		$vars[] = 'virtualpage';
  		return $vars;
	}

	public function virtualpageAddRewriteRules() {

		add_rewrite_tag('%magento_order%', '([^&]+)');
		add_rewrite_rule(
			LASHBROOK_SALE_VERIFICATION_SLUG . '/([^/]*)/?$',
			'index.php?virtualpage=' . LASHBROOK_SALE_VERIFICATION_SLUG. '&magento_order=$matches[1]',
			'top'
		);
	}

	public function initializeOrderPage($order_token){
		$this->order_token = $order_token;
		
	}

	/**
	 * Load public styles
	 * @return void
	 */
	public function loadPublicStyles(){
		wp_enqueue_style('lashbrook-styles-css',LASHBROOK_ASSETS_URL . "css/styles.css",array(),LASHBROOK_ASSET_VERSION );
	}

	/**
	 * load public scripts and pass data to script
	 * @return void
	 */
	public function loadPublicScripts(){
		wp_enqueue_script('lashbrook-jsignature-js',LASHBROOK_ASSETS_URL . "third-party/jSignature/jSignature.min.noconflict.js",array('jquery'),LASHBROOK_ASSET_VERSION);
		wp_enqueue_script('lashbrook-order-authorization-js',LASHBROOK_ASSETS_URL . "js/order-authorization.js",array('lashbrook-jsignature-js'),LASHBROOK_ASSET_VERSION);
		wp_localize_script('lashbrook-order-authorization-js', 'lashbrook',[
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce($this->nonce_action),
			'order_token' => $this->order_token,
			'actions' => array(
				'authorize_order' => $this->authorize_order_action)
			]);
	}

	/**
	 * load magento order information based token in url
	 * @param  string $order_token key to load user order
	 * @return string              complete order signoff view
	 */
	public function loadOrder($posts){

		global $wp_query;
		if(array_key_exists('virtualpage',$wp_query->query_vars)){
			switch($wp_query->query_vars['virtualpage']){
				case LASHBROOK_SALE_VERIFICATION_SLUG:
					// convert token to order id
					$this->order_token = $wp_query->query_vars['magento_order'];
					$eauth = SalesOrderEauthToken::where(["eauth_token"=>$this->order_token])->first();
					//print_debug($eauth);
					if($eauth != null){
						$order = SalesOrder::where(["entity_id" => $eauth->sales_order_id])->with('addresses','items','token','payment')->first();
						if(@$order->token->signature_image_data){
							$signature_display = "";
							$signature_source = $order->token->signature_image_data;
						}else{
							$signature_display = "display:none";
							$signature_source = "";
						}
						
						$default_post = get_post(LASHBROOK_DEFAULT_POST_ID);
						$content = $this->renderView('order-signoff',compact(
								'order',
								'default_post',
								'signature_display',
								'signature_source'
						));
						$post = $this->fakePost(
									LASHBROOK_SALE_VERIFICATION_SLUG . "/" . $this->order_token,
									"Please Authorize Your Order",
									$content);
						$posts = array($post);
						return $posts;
					}
				break;
			}
		}
		return $posts;
	}

	public function overwriteTemplate($template){
		global $wp_query;
		if(array_key_exists('virtualpage',$wp_query->query_vars)){
			return get_template_directory() . "/inc/template-eauth.php";
		}else{
			return $template;
		}
	}

	public function authorizeOrder(){
		$nonce = @$_POST['nonce'];
		if(!empty($_POST) && $nonce && wp_verify_nonce($nonce, $this->nonce_action )){
			$order_token = $_POST['order_token'];
			$eauth = SalesOrderEauthToken::where(["eauth_token"=>$order_token])->first();
			//print_debug($eauth);
			if($eauth != null){
				$order = SalesOrder::where(["entity_id" => $eauth->sales_order_id])->first();
				$eauth->update([
					'signature_image_data'=>$_POST['signature']]);

				$Service = new MagentoService;
				$Service->orderWasSigned($order->entity_id,$eauth->signature_image_data);

				die(json_encode([
					'success' => true,
					'order_token' => $order->entity_id,
					'message' => __('<strong>Success!</strong> Order successfully authorized. Please check your email for further info.','laskbrook-magento-integration')
				]));
			}
		}		 
	}
}
