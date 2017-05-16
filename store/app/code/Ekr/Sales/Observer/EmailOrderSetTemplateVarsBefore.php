<?php

namespace Ekr\Sales\Observer;

class EmailOrderSetTemplateVarsBefore implements \Magento\Framework\Event\ObserverInterface{

  
  	/**
  	 * logger object
  	 * @var \Ekr\Sales\Logger\Logger
  	 */
	protected $_logger;

	/**
	 * trasnport builder
	 * @var \Magento\Framework\Mail\Template\TransportBuilder
	 */
	protected $_transportBuilder;

	protected $_customerRepositoryInterface;

	protected $_scopeInterface;

	protected $letters = "abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	protected $numbers = '0123456789';

	/**
	 * table name for eauth tokens
	 * @var string
	 */
	protected $_tokensTableName = "";

	protected $objectManager;
    protected $dbResource;
    protected $dbConnection;

    /**
     * Attribute id
     * @var integer
     */
    private $store_name_att_id = 151;
    private $parent_account_att_id = 149;
    private $parent_approval_att_id = 152;

	/**
	 * contruct method
	 * @param \Ekr\Cart\Logger\Logger	$logger         logger object
	 */
	public function __construct(
		\Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
		\Ekr\Sales\Logger\Logger $logger){
		$this->_logger = $logger;
		$this->_transportBuilder = $transportBuilder;
		$this->_scopeInterface = $scopeInterface;
		$this->_customerRepositoryInterface = $customerRepositoryInterface;
		$this->_logger->info("construct EmailOrderSetTemplateVarsBefore");

		$this->objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $this->dbResource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->dbConnection = $this->dbResource->getConnection();
	}		
		
	public function execute(\Magento\Framework\Event\Observer $observer){

		$this->_logger->info("EmailOrderSetTemplateVarsBefore:execute");

		// get template variables
		$transport = $observer->getData('transport');
		$this->_logger->info("got transport");

		$this->_tokensTableName = $this->dbResource->getTableName("sales_order_eauth_tokens"); //gives table name with prefix
		
		// create token
		$token = $this->createEAuthRecord($transport);
		$tokenUrl = "http://" . $_SERVER['HTTP_HOST'] . "/lashbrook-order-verification/". $token;
		
		$this->_logger->info("token_url: " . $tokenUrl);

		$order = $transport->getOrder();
		$this->_logger->info('got order');
		$customer_id = $order->getCustomerId();
		$this->_logger->info("customer id " . $customer_id);
		$parent_id = $this->getParentAccountId($customer_id);
		$this->_logger->info("customers parent account " . $parent_id);
		$ask_approval = true;
		// if user has parent
		if($parent_id){
			$this->_logger->info("has parent id");
			$ask_approval  = (!$this->requiresParentApproval($customer_id));
			$this->_logger->info("ask customer for order approval? {$ask_approval}");
			if(!$ask_approval){
				$this->_logger->info("no. Parent needs to approve it");
				$parent = $this->_customerRepositoryInterface->getById($parent_id);
				$this->_logger->info("customer loaded");
				$this->sendParentApprovalEmail($parent_id,$parent,$order,$tokenUrl);
			}
		}

		// save in transport.
		$transport->setData('token_url',$tokenUrl);
		$transport->setData('ask_approval',$ask_approval);

		$this->_logger->info("now send mail email. ");

		return $this;
  	}

  	private function sendParentApprovalEmail($parent_id,$parent,$order,$token_url){
  		$this->_logger->info("sendParentApprovalEmail() called");
  		$email = $parent->getEmail();
  		$this->_logger->info("customer email : {$email}");
  		$data = [
  			'order' => $order,
  			'token_url' => $token_url,
  			'parent_name' => $parent->getFirstName() . " " . $parent->getLastName(),
  			'store_name' => $this->getStoreName($order->getCustomerId()),
  			'store_email' => $this->_scopeInterface->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
  			'store_phone' => $this->_scopeInterface->getValue('general/store_information/phone',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),

  		];
  		// data log
  		$this->_logger->info("token_url : " . $data['token_url']);
  		$this->_logger->info("store_name : " . $data['store_name']);
  		$this->_logger->info("store_email : " . $data['store_email']);
  		$this->_logger->info("store_phone : " . $data['store_phone']);

  		$this->sendEmail($email,$data,"ekr_parent_order_approval");
  	}

  	/**
  	 * [createEAuthRecord description]
  	 * @param  object $transport \Magento\Framework\DataObject
  	 * @return string token value
  	 */
  	private function createEAuthRecord($transport){
  		$token = $this->uniqueToken(); // create a unique token.
  		$this->_logger->info("chosen token: " . $token);
  		
  		$order = $transport->getOrder(); // get order
  		$this->_logger->info("got order");
  		
  		$order_id = $order->getId(); // get order id.
  		$this->_logger->info("orderId: " . $order_id);
  		
  		$sql = "INSERT INTO {$this->_tokensTableName} (sales_order_id, eauth_token) VALUES ('{$order_id}','{$token}')";
		
		$this->_logger->info("token: " . $token);
		$this->_logger->info("sql: " . $sql);
		
		$this->dbConnection->query($sql);
		return $token;
  	}

  	/**
  	 * check database table and choose a  unique token
  	 * 
  	 * @return string unique token
  	 */
  	private function uniqueToken(){
  		$unique = false;
  		while(!$unique){
  			$token = $this->generateToken();
  			//Select Data from table
  			
			$sql = "Select * FROM " . $this->_tokensTableName . " WHERE eauth_token='{$token}'";
			$result = $this->dbConnection->fetchAll($sql); // gives associated array, table fields as key in array.
			if(empty($result)) $unique = true;
  		}

  		return $token;
  	}

  	
	/**
	 * create a token using numbers and letters
	 * @param  integer $length       [description]
	 * @param  boolean $only_numbers [description]
	 * @return string
	 */
	private function generateToken ($length = 8,$only_numbers = false)
	{
		// initialize variables
		$token = "";
		$i = 0;
		if($only_numbers){
			$possible = $this->numbers;
		}else{
			$possible = $this->numbers . $this->letters;
		}

		// add random characters to $token until $length is reached
		while ($i < $length) {
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);

			// we don't want this character if it's already in the password
			if (!strstr($token, $char)) { 
				$token .= $char;
				$i++;
			}
		}
		return $token;
	}

	/**
     * check if the current account is a parent account
     * @param  int  $customer_id    Customer Record entity_id
     * @return boolean              true if is parent account
     */
    private function getParentAccountId($customer_id){
        
        $tableName = $this->dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->parent_account_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : 0;
        return $value;
    }

    private function requiresParentApproval($customer_id){
    	$tableName = $this->dbResource->getTableName("customer_entity_int"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->parent_approval_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : 0;
        return $value;
    }

    public function getStoreName($customer_id){
        $tableName = $this->dbResource->getTableName("customer_entity_varchar"); //gives table name with prefix
        $sql = "SELECT value FROM " . $tableName . " WHERE `entity_id` = '{$customer_id}' AND `attribute_id`='{$this->store_name_att_id}'";
        $result = $this->dbConnection->fetchAll($sql);
        $value = (@$result[0]['value'])? $result[0]['value'] : "";
        return $value;
    }

    /**
	 * send email template
	 * @param string $email        recipient
	 * @param  array $data      template data
	 * @param  [type] $template email template name
	 * @return boolean true if mail was sent
	 */
    private function sendEmail($email,$data,$template){


        $this->_logger->info("recipient: {$email}");

        // email template data.
/*        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);*/

        $this->_logger->info("new data object");

    	$senderEmail = $this->_scopeInterface->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    	$senderName  = $this->_scopeInterface->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        
        $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                    ];

        $this->_logger->info("sender: {$senderEmail}");
        $this->_logger->info("sender name: {$senderName}");

        $transport = $this->_transportBuilder->setTemplateIdentifier($template)
        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
        ->setTemplateVars($data)
        ->setFrom($sender)
        ->addTo($email)
        ->setReplyTo($senderEmail)            
        ->getTransport();

        $this->_logger->info("finish transport set up");

        return $transport->sendMessage();
    }
}
