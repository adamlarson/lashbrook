<?php

namespace Ekr\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Area;
use Magento\Store\Model\Store;
use Magento\Framework\DataObject;

class CustomerRegisterSuccess implements ObserverInterface
{

    /**
     * email where confirmation that user wants retailer account should be sent to.
     * @var string
     */
    protected $lashbrook_support_email = "rico@ekragency.com";

    /**
     * email template to use.
     * @var string
     */
    protected $template_identifier = "ekr_retailer_account_template";

    /**
     * Transport Builder
     * @var \Magento\Framework\Mail\Template\TransportBuilder 
     */
    protected $_transportBuilder;

    /**
     * logger object
     * @var \Ekr\Customer\Logger\Logger
     */
    protected $_logger;

    /**
     * Post request object
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * Store configuration values
     * @var \Magento\Store\Model\ScopeInterface
     */
    protected $_scopeInterface;

    /**
     * contruct method
     * @param \Magento\Framework\App\RequestInterface $request        Post request object
     */
    public function __construct(
        \Ekr\Customer\Logger\Logger $logger,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\RequestInterface $request /*,
        \Magento\Store\Model\ScopeInterface $scopeInterface*/
    ) {
        $this->_logger = $logger;
        $this->_request = $request;
        $this->_transportBuilder = $transportBuilder;
        //$this->_scopeInterface = $scopeInterface;
        //
        $this->_logger->info("construct observer");
    }

    /**
     * execute event dispatched
     * @param  \Magento\Framework\Event\Observer $observer Observer
     * @return CustomerRegisterSuccess
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_logger->info("execute observer");
        // check if account type is in post
        $ekr_account_type = $this->_request->getParam('ekr_account_type');
        $this->_logger->info("checking for retailer account {$ekr_account_type}.");
        if(empty($ekr_account_type)) return $this;

        $this->_logger->info("requesting retailer account.");
        // get customer
        $customer = $observer->getEvent()->getCustomer();
        $this->_logger->info("got customer object");
        // if account type retailer
        if($ekr_account_type == "retailer"){
            // should send email
            $data = [
                'username' => $customer->getFirstName() . " " . $customer->getLastName(),
                'email' => $customer->getEmail(),
                'user_role' => "Retailer"
            ];
            if($this->sendEmail($data)){
                $this->_logger->info('email sent correctly');
            }else{
                $this->_logger->info('email not sent');
            }
        }

        // return
        return $this;
    }

    /**
     * send email to lashbrook
     * @param  array $data email template data
     * @return boolean
     */
    private function sendEmail($data){

        // recipient
        //$email = $this->scopeInterface->getValue('trans_email/ident_custom2/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $email = $this->lashbrook_support_email;

        $this->_logger->info("recipient: {$email}");

        // email template data.
        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($data);

        $this->_logger->info("new data object");

        // sender
/*        $senderEmail = $this->scopeInterface->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $senderName  = $this->scopeInterface->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
*/
        $senderName = "Retail Account Request";
        $senderEmail = "retail_accounts@elikirk-dev.com";
        
        $sender = [
                    'name' => $senderName,
                    'email' => $senderEmail,
                    ];

        $this->_logger->info("sender: {$senderEmail}");
        $this->_logger->info("sender name: {$senderName}");

        $transport = $this->_transportBuilder->setTemplateIdentifier($this->template_identifier)
        ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])
        ->setTemplateVars(['data' => $postObject])
        ->setFrom($sender)
        ->addTo($email)
        ->setReplyTo($senderEmail)            
        ->getTransport();

        $this->_logger->info("finish transport set up");

        return $transport->sendMessage();
    }
}