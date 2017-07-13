<?php
namespace Ekr\Customer\Observer;

class AdminCustomerPrepareSave  implements \Magento\Framework\Event\ObserverInterface{

	protected $_logger;
	protected $_transportBuilder;
	protected $_objectManager;

	protected $status_key = 'ekr_account_status';
	protected $template_approved = "ekr_retailer_account_approved";
	protected $template_denied = "ekr_retailer_account_denied";

	/**
	 * Construct function
	 * @param \Ekr\Sales\Logger\Logger $logger [description]
	 */
	public function __construct(
		\Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
		\Ekr\Customer\Logger\Logger $logger){
		
		$this->_logger = $logger;
		$this->_transportBuilder = $transportBuilder;
		$this->_logger->info("construct AdminCustomerPrepareSave");
		$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	}

	/**
	 * execute observer
	 * @param  \Magento\Framework\Event\Observer $observer observer data
	 * @return void
	 * event dispatched:
	 	$this->_eventManager->dispatch(
            'adminhtml_customer_prepare_save',
            ['customer' => $customer, 'request' => $request]
        );
	 */
	public function execute(\Magento\Framework\Event\Observer $observer){
		$customerData = $observer->getCustomer();
		$request = $observer->getRequest();

		$this->_logger->info('customer : ' . get_class($customerData));
		$this->_logger->info('request: ' . get_class($request));

		$data = $request->getParam('customer');
		$approved = $data[$this->status_key];
		$customer_id = $customerData->getId();
		$this->_logger->info("customer id: " . $customer_id);

		if(empty($customer_id)){
			$this->_logger->info("customer is being created");
			return;
		}

		$customer = $this->getCustomer($customerData->getId());
		$attValue = $customer->getCustomAttribute($this->status_key);
			
		if($attValue){
			$wasApproved = $attValue->getValue();
		}else{
			$wasApproved = $approved;
		}

		$this->_logger->info("approved: {$approved} was:{$wasApproved}");
		
		// if status is changing
		if($approved != $wasApproved){
			// get store url
			$store_url = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID)
                ->getBaseUrl();
            $this->_logger->info("store url: " . $store_url);
            //template data.
			$data = [
				'username' => $customer->getFirstName() . " " . $customer->getLastName(),
				'store_url' => $store_url
			];
			// if approved
			if($approved){
				// send email that it was approved.
				$this->sendEmail($customer->getEmail(),$data,$this->template_approved);
			}else{
				// send email that account was denied.
				//$this->sendEmail($customer->getEmail(),$data,$this->template_denied);
			}
		}
	}

	/**
	 * get customer object
	 * @param  int $customer_id [description]
	 * @return [type]              [description]
	 */
	private function getCustomer($customer_id){
		$this->_logger->info('customer id ' . $customer_id);
		$customerRepository = $this->_objectManager->get('Magento\Customer\Api\CustomerRepositoryInterface');
		$customer = $customerRepository->getById($customer_id);
		return $customer;
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
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);

        $this->_logger->info("new data object");

        $senderName = "Retail Account Request";
        $senderEmail = "retail_accounts@elikirk-dev.com";
        
        $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                    ];

        $this->_logger->info("sender: {$senderEmail}");
        $this->_logger->info("sender name: {$senderName}");

        $transport = $this->_transportBuilder->setTemplateIdentifier($template)
        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
        ->setTemplateVars(['data' => $postObject])
        ->setFrom($sender)
        ->addTo($email)
        ->setReplyTo($senderEmail)            
        ->getTransport();

        $this->_logger->info("finish transport set up");

        return $transport->sendMessage();
    }
}