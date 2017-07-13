<?php 
	
	namespace Lashbrook\Services;

	class MagentoService{

	/**
	 * Magento REST api url
	 * @var string
	 */
	protected $rest_api_url;

	protected $order_status_signed = "signed";

	/**
	 * magento username
	 * @var string
	 */
	protected $username;

	/**
	 * magento password
	 * @var string
	 */
	protected $password;

	/**
	 * magento api token
	 * @var string
	 */
	protected $api_token = null;

	function __construct(){
		$this->username = MG_API_USER;
		$this->password = MG_API_PASS;
		$this->rest_api_url = MG_API_URL;
		$this->api_token = $this->getToken();
	}

	/**
	 * get Magento api token.
	 * @return string Magento Api Token
	 */
	private function getToken(){

		$userData = ['username'=>$this->username,'password' => $this->password];
		return $this->request("POST","integration/admin/token",$userData);
/*		if(gettype($response) == "string"){
			return $response;
		}else{
			$message = $response->message;
			foreach($response->parameters as $parameter => $value){
				$message = str_replace("%". $parameter, $value, $message);
			}
			print_debug($message,true);
		}*/
	}

	/**
	 * get Magento order
	 * @param  int $order_id Magento order id
	 * @return object.       Order information
	 */
	public function orderDetails($order_id){
		$order = $this->request("GET","orders/" . $order_id);
		return $order;
	}

	/**
	 * user provided a signature for the order
	 * @param  int $order_id       Magento order id
	 * @return bool.        magento REST API response when completed
	 */
	public function orderWasSigned($order_id){
		return $this->addOrderComment(
			$order_id,
			$this->order_status_signed,
			"Order has been signed and authorized by customer."
		);
	}

	/**
	 * add a note.
	 * @param  [type] $order_id [description]
	 * @return [type]           [description]
	 */
	public function orderWasCancelled($order_id){
		return $this->addOrderComment(
			$order_id,
			$this->order_status_signed,
			"Order was canceled by parent account."
		);
	}

	/**
	 * Change the status of a magento order by adding a comment with a status
	 * @param  int  $order_id Magento Order Id
	 * @param  string  $status   New order status
	 * @param  string  $comment  optional comment
	 * @param  integer $notify   notify customer of change
	 * @return mixed            Magento REST API response
	 */
	public function addOrderComment($order_id,$status,$comment = "",$notify = 0){
		$data = [
			'statusHistory' => [
				'comment' => $comment,
				'status' => $status,
				'isCustomerNotified' => $notify
			]
		];
		return $this->request("POST","orders/{$order_id}/comments",$data);
	}

	/**
	 * Make an API request
	 * @param  string $method   GET, POST
	 * @param  string $resource api resource
	 * @param  [type] $data     post data if needed
	 * @return mixed           API call response
	 */
	private function request($method,$resource,$data = null){
		$method = strtoupper($method);
		$ch = curl_init($this->rest_api_url . $resource);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		$headers = [
			"Content-Type: application/json",
		];
		if($method == "POST"){
			$json_data = json_encode($data);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
			$headers[] = "Content-Length: " . strlen($json_data);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		if($this->api_token != null){
			$headers[] = "Authorization: Bearer " . $this->api_token;
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
		$response = json_decode(curl_exec($ch));
		return $response;
	}
}