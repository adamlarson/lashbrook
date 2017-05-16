<?php

namespace Ekr\Authorization\Controller\Pending;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;

class Show extends \Magento\Framework\App\Action\Action
{

	protected $pageFactory;
	protected $socf;
    public function __construct(Context $context, PageFactory $pageFactory, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollectionFactory)
    {
        $this->pageFactory = $pageFactory;
        $this->socf = $salesOrderCollectionFactory->create()->addAttributeToSelect('*');
        return parent::__construct($context);
    }

	public function execute(){
	    $pageObject = $this->pageFactory->create();
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $orders = $om->create('\Magento\Sales\Model\ResourceModel\Order\Collection');
        $session = $om->create('Magento\Customer\Model\Session');
        
        $this->socf->addFieldToFilter("status", "holded");
        //TODO: get current logged-in customer
        $this->socf->addFieldToFilter("customer_id", $session->getCustomerId());

        $pageObject->getLayout()->getBlock('ekr_order_authorization')->setOrders($this->socf);

        
	    
        return $pageObject;	
    }

}