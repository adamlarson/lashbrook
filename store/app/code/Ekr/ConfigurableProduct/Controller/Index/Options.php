<?php 


namespace Ekr\ConfigurableProduct\Controller\Index;
 
 
class Options extends \Magento\Framework\App\Action\Action {

    
    /**
     * json factory
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * product factory
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Constructor
     * @param Context                                          $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Catalog\Model\productFactory            $productFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory) {
        
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_productFactory = $productFactory;

        parent::__construct($context);
    }

    /**
     * Execute view action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute(){
        $request = $this->getRequest();
        $action = $request->getPost('action');
        $productId = $request->getPost('product_id');
        switch($action){
            case "ring_size":
                return $this->getRingSizes($productId);
            break;
            case "product_info":
                return $this->getProductInfo($productId);
            break;
        }        
    }

    /**
     * get a list of all ring sizes available for a product
     * @param  int $product_id Product record id
     * @return json
     */
    private function getRingSizes($product_id){
        $json = $this->_resultJsonFactory->create();
        // get product options.
        $simpleProduct = $this->_productFactory->create()->load($product_id);
        $sizeOptions = [];
        foreach($simpleProduct->getProductOptionsCollection() as $option){
            if($option->getTitle() == "Size"){
                foreach($option->getValues() as $values){
                    $sizeOptions[] = $values->getData();
                }
            }
        }

        return $json->setData($sizeOptions);
    }

    /**
     * get a list of all ring sizes available for a product
     * @param  int $product_id Product record id
     * @return json
     */
    private function getProductInfo($product_id){
        $product = $this->_productFactory->create()->load($product_id);
        $json = $this->_resultJsonFactory->create();
        return $json->setData([
            'name' => $product->getName(),
            'description' => $product->getDescription()
        ]);
    }
}
