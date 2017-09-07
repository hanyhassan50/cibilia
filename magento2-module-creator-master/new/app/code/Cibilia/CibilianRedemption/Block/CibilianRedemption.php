<?php

namespace Cibilia\CibilianRedemption\Block;

/**
 * CibilianRedemption content block
 */
class CibilianRedemption extends \Magento\Framework\View\Element\Template
{
    /**
     * CibilianRedemption collection
     *
     * @var Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption\Collection
     */
    protected $_cibilianredemptionCollection = null;
    
    /**
     * CibilianRedemption factory
     *
     * @var \Cibilia\CibilianRedemption\Model\CibilianRedemptionFactory
     */
    protected $_cibilianredemptionCollectionFactory;
    
    /** @var \Cibilia\CibilianRedemption\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption\CollectionFactory $cibilianredemptionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption\CollectionFactory $cibilianredemptionCollectionFactory,
        \Cibilia\CibilianRedemption\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_cibilianredemptionCollectionFactory = $cibilianredemptionCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve cibilianredemption collection
     *
     * @return Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_cibilianredemptionCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared cibilianredemption collection
     *
     * @return Cibilia\CibilianRedemption\Model\ResourceModel\CibilianRedemption\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_cibilianredemptionCollection)) {
            $this->_cibilianredemptionCollection = $this->_getCollection();
            $this->_cibilianredemptionCollection->setCurPage($this->getCurrentPage());
            $this->_cibilianredemptionCollection->setPageSize($this->_dataHelper->getCibilianRedemptionPerPage());
            $this->_cibilianredemptionCollection->setOrder('published_at','asc');
        }

        return $this->_cibilianredemptionCollection;
    }
    
    /**
     * Fetch the current page for the cibilianredemption list
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
     * @param Cibilia\CibilianRedemption\Model\CibilianRedemption $cibilianredemptionItem
     * @return string
     */
    public function getItemUrl($cibilianredemptionItem)
    {
        return $this->getUrl('*/*/view', array('id' => $cibilianredemptionItem->getId()));
    }
    
    /**
     * Return URL for resized CibilianRedemption Item image
     *
     * @param Cibilia\CibilianRedemption\Model\CibilianRedemption $item
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
        $pager = $this->getChildBlock('cibilianredemption_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $cibilianredemptionPerPage = $this->_dataHelper->getCibilianRedemptionPerPage();

            $pager->setAvailableLimit([$cibilianredemptionPerPage => $cibilianredemptionPerPage]);
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
