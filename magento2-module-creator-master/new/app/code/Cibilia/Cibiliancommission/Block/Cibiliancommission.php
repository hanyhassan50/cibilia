<?php

namespace Cibilia\CibilianCommission\Block;

/**
 * CibilianCommission content block
 */
class CibilianCommission extends \Magento\Framework\View\Element\Template
{
    /**
     * CibilianCommission collection
     *
     * @var Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission\Collection
     */
    protected $_cibiliancommissionCollection = null;
    
    /**
     * CibilianCommission factory
     *
     * @var \Cibilia\CibilianCommission\Model\CibilianCommissionFactory
     */
    protected $_cibiliancommissionCollectionFactory;
    
    /** @var \Cibilia\CibilianCommission\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission\CollectionFactory $cibiliancommissionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission\CollectionFactory $cibiliancommissionCollectionFactory,
        \Cibilia\CibilianCommission\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_cibiliancommissionCollectionFactory = $cibiliancommissionCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve cibiliancommission collection
     *
     * @return Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_cibiliancommissionCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared cibiliancommission collection
     *
     * @return Cibilia\CibilianCommission\Model\ResourceModel\CibilianCommission\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_cibiliancommissionCollection)) {
            $this->_cibiliancommissionCollection = $this->_getCollection();
            $this->_cibiliancommissionCollection->setCurPage($this->getCurrentPage());
            $this->_cibiliancommissionCollection->setPageSize($this->_dataHelper->getCibilianCommissionPerPage());
            $this->_cibiliancommissionCollection->setOrder('published_at','asc');
        }

        return $this->_cibiliancommissionCollection;
    }
    
    /**
     * Fetch the current page for the cibiliancommission list
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
     * @param Cibilia\CibilianCommission\Model\CibilianCommission $cibiliancommissionItem
     * @return string
     */
    public function getItemUrl($cibiliancommissionItem)
    {
        return $this->getUrl('*/*/view', array('id' => $cibiliancommissionItem->getId()));
    }
    
    /**
     * Return URL for resized CibilianCommission Item image
     *
     * @param Cibilia\CibilianCommission\Model\CibilianCommission $item
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
        $pager = $this->getChildBlock('cibiliancommission_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $cibiliancommissionPerPage = $this->_dataHelper->getCibilianCommissionPerPage();

            $pager->setAvailableLimit([$cibiliancommissionPerPage => $cibiliancommissionPerPage]);
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
