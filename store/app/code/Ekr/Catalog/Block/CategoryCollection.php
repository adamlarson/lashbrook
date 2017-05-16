<?php 
namespace Ekr\Catalog\Block;

class CategoryCollection extends \Magento\Framework\View\Element\Template {

	protected $_categoryCollectionFactory;
    protected $_storeManager;

	public function __construct(
        \Magento\Backend\Block\Template\Context $context,        
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []){
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_storeManager = $context->getStoreManager();

        parent::__construct($context, $data);
    }
    
    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection or
     * \Magento\Catalog\Model\ResourceModel\Category\Collection or array
     */
    public function getStoreCategories(){
        $categories = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('parent_id',array('eq' => 2))
            ->setStore($this->_storeManager->getStore()); //categories from current store will be fetched
        return $categories;
    }
}