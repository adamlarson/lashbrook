<?php
namespace Ekr\Cart\Observer;

class UpdateMessage implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magento\Framework\Message\ManagerInterface */
    protected $_messageManager;

    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $managerInterface
    ) {
        $this->_messageManager = $managerInterface;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $messageCollection = $this->_messageManager->getMessages(true);
        $message = "Product added to cart.";
        $this->_messageManager->addSuccess($message);
    }
}