<?php

namespace Cibilia\Summary\Block;

/**
 * Summary content block
 */
class Summary extends \Magento\Framework\View\Element\Template
{
    /**
     * Summary collection
     *
     * @var Cibilia\Summary\Model\ResourceModel\Summary\Collection
     */
    protected $_summaryCollection = null;
    
    /**
     * Summary factory
     *
     * @var \Cibilia\Summary\Model\SummaryFactory
     */
    protected $_summaryCollectionFactory;
    
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
        \Cibilia\Summary\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_summaryCollectionFactory = $summaryCollectionFactory;
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
        $collection = $this->_summaryCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared summary collection
     *
     * @return Cibilia\Summary\Model\ResourceModel\Summary\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_summaryCollection)) {
            $this->_summaryCollection = $this->_getCollection();
            $this->_summaryCollection->setCurPage($this->getCurrentPage());
            $this->_summaryCollection->setPageSize($this->_dataHelper->getSummaryPerPage());
            $this->_summaryCollection->setOrder('updated_at','asc');
        }
        if($this->customerSession->getCustomer()->getId()){
            $this->_summaryCollection->addFieldToFilter('cibilian_id',$this->customerSession->getCustomer()->getId());          
        }

        return $this->_summaryCollection;
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
}
