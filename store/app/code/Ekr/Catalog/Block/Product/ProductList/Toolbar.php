<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ekr\Catalog\Block\Product\ProductList;

use Magento\Catalog\Helper\Product\ProductList;
use Magento\Catalog\Model\Product\ProductList\Toolbar as ToolbarModel;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Registry;

/**
 * Product list toolbar
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Catalog\Model\Layer\AvailabilityFlagInterface
     */
    protected $visibilityFlag;

    protected $baseMetals = [];
    protected $options = [];
    protected $prices = [];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Catalog\Model\Config $catalogConfig,
        ToolbarModel $toolbarModel,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        ProductList $productListHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\Layer\AvailabilityFlagInterface $visibilityFlag,
        array $data = []
    ) {
        $storeManager = $context->getStoreManager();
        $this->_catalogLayer = $layerResolver->get();
        $this->filterList = new \Magento\Catalog\Model\Layer\FilterList($objectManager, new \Magento\Catalog\Model\Layer\Category\FilterableAttributeList($collectionFactory, $storeManager));
        $this->visibilityFlag = $visibilityFlag;
        //$this->sm = $storeManager;
        parent::__construct($context, $catalogSession, $catalogConfig, $toolbarModel, $urlEncoder, $productListHelper, $postDataHelper, $data);
    }

    /**
     * Get all layer filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filterList->getFilters($this->_catalogLayer);
    }

    private function compileFilterTypes(){

        foreach ($this->getFilters() as $filter){
            $currentArray = [];
            switch(strtolower($filter->getName())){
                case "base metal":
                    foreach($filter->getItems() as $item){
                        $this->baseMetals[] = $item;
                    }
                break;
                case "finish":
                case "inlay":
                    foreach($filter->getItems() as $item){
                        $this->options[] = $item;
                    }
                break;
                case "price":
                    foreach($filter->getItems() as $item){
                        $this->prices[] = $item;
                    }
                break;
            }
        }
    }

    public function getBaseMetalFilters(){
        if(empty($this->baseMetals)){
            $this->compileFilterTypes();
        }

        return $this->baseMetals;
    }

    public function getOptionFilters(){
        if(empty($this->options)){
            $this->compileFilterTypes();
        }

        return $this->options;
    }

    public function getPriceFilters(){
        if(empty($this->prices)){
            $this->compileFilterTypes();
        }

        return $this->prices;
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }
        return $filters;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = [];
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * Retrieve Layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer($this->_catalogLayer);
        }
        return $this->_getData('layer');
    }
}
