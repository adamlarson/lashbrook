<?php

namespace Ekr\Authorization\Controller\Pending;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
//use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;

class Process extends \Magento\Framework\App\Action\Action
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
        
        $orderId = $this->getOrderId();
	    
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $orders = $om->create('\Magento\Sales\Model\ResourceModel\Order\Collection');

        $this->socf->addFieldToFilter("entity_id", $orderId);

        $pageObject->getLayout()->getBlock('ekr_order_authorization_process')->setOrder($this->socf->fetchItem());

        return $pageObject;
    }

    protected function getOrderId(){
        $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\UrlInterface');
        $urlParts = explode("/",$urlInterface->getCurrentUrl());

        $oid = 0;

        while(count($urlParts) > 0){
            $part = array_pop($urlParts);
            if(is_numeric($part)){
                $oid = (int) $part;
                break;
            }
        }

        return $oid;
    }

}