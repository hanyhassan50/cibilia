<?php

namespace Cibilia\Commission\Block;

/**
 * Commission content block
 */
class Commission extends \Magento\Framework\View\Element\Template
{
    /**
     * Commission collection
     *
     * @var Cibilia\Commission\Model\ResourceModel\Commission\Collection
     */
    protected $_commissionCollection = null;
    
    /**
     * Commission factory
     *
     * @var \Cibilia\Commission\Model\CommissionFactory
     */
    protected $_commissionCollectionFactory;
    
    /** @var \Cibilia\Commission\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cibilia\Commission\Model\ResourceModel\Commissions\CollectionFactory $commissionCollectionFactory,
        \Cibilia\Commission\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_commissionCollectionFactory = $commissionCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
        $this->customerSession = $customerSession;
    }
    
    /**
     * Retrieve commission collection
     *
     * @return Cibilia\Commission\Model\ResourceModel\Commission\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_commissionCollectionFactory->create();
        if($this->customerSession->getCustomer()->getId()){
            $collection->addFieldToFilter('cibilian_id',$this->customerSession->getCustomer()->getId());          
        }
        return $collection;
    }
    
    /**
     * Retrieve prepared commission collection
     *
     * @return Cibilia\Commission\Model\ResourceModel\Commission\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_commissionCollection)) {
            $this->_commissionCollection = $this->_getCollection();
            $this->_commissionCollection->setCurPage($this->getCurrentPage());
            $this->_commissionCollection->setPageSize($this->_dataHelper->getCommissionPerPage());
            $this->_commissionCollection->setOrder('updated_at','asc');
        }
        
        return $this->_commissionCollection;
    }
    
    /**
     * Fetch the current page for the commission list
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
     * @param Cibilia\Commission\Model\Commission $commissionItem
     * @return string
     */
    public function getItemUrl($commissionItem)
    {
        return $this->getUrl('*/*/view', array('id' => $commissionItem->getId()));
    }
    
    /**
     * Return URL for resized Commission Item image
     *
     * @param Cibilia\Commission\Model\Commission $item
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
        $pager = $this->getChildBlock('commission_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $commissionPerPage = $this->_dataHelper->getCommissionPerPage();

            $pager->setAvailableLimit([$commissionPerPage => $commissionPerPage]);
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
}
