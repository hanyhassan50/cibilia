<?php

namespace Cibilia\Summary\Block;
/**
 * Summary content block
 */
class Referred extends \Magento\Framework\View\Element\Template
{
    /**
     * Summary collection
     *
     * @var Cibilia\Summary\Model\ResourceModel\Summary\Collection
     */
    protected $_summaryCollection = null;
    

    protected $_productCollection = null;


    protected $productCollectionFactory;

    /**
     * Summary factory
     *
     * @var \Cibilia\Summary\Model\SummaryFactory
     */
    protected $_summaryCollectionFactory;

    protected $_commissionCollectionFactory;
    
    /** @var \Cibilia\Summary\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory $summaryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
         \Magento\Customer\Model\Session $customerSession,
        \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory $summaryCollectionFactory,
        \Cibilia\Commission\Model\ResourceModel\Commissions\CollectionFactory $commissionCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Cibilia\Summary\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_summaryCollectionFactory = $summaryCollectionFactory;
        $this->_commissionCollectionFactory = $commissionCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
        $this->customerSession = $customerSession;
    }
    
    /**
     * Retrieve summary collection
     *
     * @return Cibilia\Summary\Model\ResourceModel\Summary\Collection
     */
    protected function _getCollection()
    {
       /* $collection = $this->_summaryCollectionFactory->create();
        return $collection;*/
        /*$collection = $this->_commissionCollectionFactory->create();
        return $collection;*/
        $collection = $this->_getProductCollection();
        return $collection;
    }
    
    /**
     * Retrieve prepared summary collection
     *
     * @return Cibilia\Summary\Model\ResourceModel\Summary\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->_getCollection();
        }
        return $this->_productCollection;
    }
    
    /**
     * Fetch the current page for the summary list
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData('current_page') ? $this->getData('current_page') : 1;
    }
    
    /**
     * Return URL to item's view page
     *
     * @param Cibilia\Summary\Model\Summary $summaryItem
     * @return string
     */
    public function getItemUrl($summaryItem)
    {
        return $this->getUrl('*/*/view', array('id' => $summaryItem->getId()));
    }
    
    /**
     * Return URL for resized Summary Item image
     *
     * @param Cibilia\Summary\Model\Summary $item
     * @param integer $width
     * @return string|false
     */
    public function getImageUrl($item, $width)
    {
        return $this->_dataHelper->resize($item, $width);
    }
    
    /**
     * Get a pager
     *
     * @return string|null
     */
    public function getPager()
    {
        $pager = $this->getChildBlock('summary_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $summaryPerPage = $this->_dataHelper->getSummaryPerPage();

            $pager->setAvailableLimit([$summaryPerPage => $summaryPerPage]);
            $pager->setTotalNum($this->getCollection()->getSize());
            $pager->setCollection($this->getCollection());
            $pager->setShowPerPage(TRUE);
            $pager->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            );

            return $pager->toHtml();
        }

        return NULL;
    }
    public function _getProductCollection() {
        $arrCibilianVendors = array();
        $arrCibilianVendors = $this->getCibilianVendors();
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_productCollectionFactory->create();
        $collection->addFieldToFilter('created_by',2);
        if(count($arrCibilianVendors) > 0){
            $collection->addFieldToFilter('udropship_vendor', array('in' => $arrCibilianVendors));
        }

        return $collection;
    }
    public function getCibilianVendors(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $arrVendors = array();
        $arrVendorRegCollection = $objectManager->create('Unirgy\Dropship\Model\Vendor')->getCollection()->addFieldToFilter('status','V')->addFieldToFilter('vendor_type',1);
        $arrVendorRegCollection->getSelect()->joinleft(
            ['vendor_reg' => 'udropship_vendor_registration'],
            'main_table.email = vendor_reg.email')->where("vendor_reg.referred_by = '".$this->customerSession->getCustomer()->getId()."'");;

        if(count($arrVendorRegCollection) > 0){
            foreach ($arrVendorRegCollection as $vendor) {
                $arrVendors[] = $vendor->getId();
            }
        }
        return $arrVendors;

    }
    public function getCibilianCommissionCollection(){
        $arrCommissionCollection = $this->_commissionCollectionFactory->create();
        $arrCommissionCollection->addFieldToFilter('cibilian_id',$this->customerSession->getCustomer()->getId());

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        
        $arrRefrredProducts = array();
        foreach ($arrCommissionCollection as $commission) {
            $arrEarned = json_decode($commission->getData('cibilia_commision_rate'),true);
            foreach ($arrEarned as $key => $rates) {
                $objProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($rates['pid']);
                if($objProduct && $objProduct->getId() && $objProduct->getCreatedBy() == 2){
                    $objVendor = $objectManager->create('Unirgy\Dropship\Model\Vendor')->load($objProduct->getUdropshipVendor());
                    if($objVendor && $objVendor->getId() && $objVendor->getVendorType() == 1){
                        if($commission->getStatus() == 3){
                            if(array_key_exists($objProduct->getId(), $arrRefrredProducts)){
                                $earned = $arrRefrredProducts[$objProduct->getId()]['lock'];
                                $arrRefrredProducts[$objProduct->getId()]['lock'] = $earned + $rates['earned'];
                            }else{
                                $arrRefrredProducts[$objProduct->getId()]['lock'] = $rates['earned'];
                            }
                        }elseif($commission->getStatus() == 2){
                            if(array_key_exists($objProduct->getId(), $arrRefrredProducts)){
                                $earned = $arrRefrredProducts[$objProduct->getId()]['earn'];
                                $arrRefrredProducts[$objProduct->getId()]['earn'] = $earned + $rates['earned'];
                            }else{
                                $arrRefrredProducts[$objProduct->getId()]['earn'] = $rates['earned'];
                            }
                        }
                    }
                }
            }
                
            
        }
        return $arrRefrredProducts;
    }
}
