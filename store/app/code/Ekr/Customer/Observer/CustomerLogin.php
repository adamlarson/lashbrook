<?php

namespace Ekr\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;
use Magento\Framework\DataObject;

class CustomerLogin implements ObserverInterface
{
	/**
     * logger object
     * @var \Ekr\Customer\Logger\Logger
     */
    protected $_logger;

    protected $_response;

    protected $_customerSession;

    protected $not_approved_url = "/store/customer/account/login?message=account_not_approved";

	/**
	 * construct method.
	 */
	public function __construct(
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Ekr\Customer\Logger\Logger $logger){

		$this->_logger = $logger;
		$this->_response = $responseFactory;
		$this->_customerSession = $customerSession;
		$this->_logger->info("CustomerLogin:__construct");
	}

	/**
	 * execute observer 
	 * @param  \Magento\Framework\Event\Observer $observer [description]
	 * @return [type]                                      [description]
	 */
	public function execute(\Magento\Framework\Event\Observer $observer){
		$customer = $observer->getCustomer();
		$approved = $customer->getData('ekr_account_status');
		$this->_logger->info('approved status: ' . $approved);
		if(!$approved){
			$this->_logger->info("customer is not approved.");
			$this->_customerSession->logout();
			$this->_logger->info("logged user out");
			$this->_logger->info("redirect to " . $this->not_approved_url);
			$this->_response->create()->setRedirect($this->not_approved_url)->sendResponse();
			exit();
		}
	}
}