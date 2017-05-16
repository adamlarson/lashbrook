<?php

namespace Ekr\Cms\Plugin;

use Magento\Framework\Controller\ResultFactory; 

class NoRoute
{
    /**
     * Redirect Factory
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $_redirectFactory;

    /**
     * Result Factory
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $_resultFactory;

    public function __construct(
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
    ){
        $this->_redirectFactory = $redirectFactory;
        $this->_resultFactory = $resultFactory;
    }

    public function aroundExecute(
        \Magento\Cms\Controller\Noroute\Index $index,
        \Closure $proceed
    ) {
        /*return $this->redirectFactory->create()
                    ->setPath('/404');*/

        $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl("/404");

        return $resultRedirect; 
    }
}