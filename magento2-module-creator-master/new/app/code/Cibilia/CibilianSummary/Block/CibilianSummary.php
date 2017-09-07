<?php

namespace Cibilia\CibilianSummary\Block;

/**
 * CibilianSummary content block
 */
class CibilianSummary extends \Magento\Framework\View\Element\Template
{
    /**
     * CibilianSummary collection
     *
     * @var Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary\Collection
     */
    protected $_cibiliansummaryCollection = null;
    
    /**
     * CibilianSummary factory
     *
     * @var \Cibilia\CibilianSummary\Model\CibilianSummaryFactory
     */
    protected $_cibiliansummaryCollectionFactory;
    
    /** @var \Cibilia\CibilianSummary\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary\CollectionFactory $cibiliansummaryCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary\CollectionFactory $cibiliansummaryCollectionFactory,
        \Cibilia\CibilianSummary\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_cibiliansummaryCollectionFactory = $cibiliansummaryCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve cibiliansummary collection
     *
     * @return Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_cibiliansummaryCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared cibiliansummary collection
     *
     * @return Cibilia\CibilianSummary\Model\ResourceModel\CibilianSummary\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_cibiliansummaryCollection)) {
            $this->_cibiliansummaryCollection = $this->_getCollection();
            $this->_cibiliansummaryCollection->setCurPage($this->getCurrentPage());
            $this->_cibiliansummaryCollection->setPageSize($this->_dataHelper->getCibilianSummaryPerPage());
            $this->_cibiliansummaryCollection->setOrder('published_at','asc');
        }

        return $this->_cibiliansummaryCollection;
    }
    
    /**
     * Fetch the current page for the cibiliansummary list
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
     * @param Cibilia\CibilianSummary\Model\CibilianSummary $cibiliansummaryItem
     * @return string
     */
    public function getItemUrl($cibiliansummaryItem)
    {
        return $this->getUrl('*/*/view', array('id' => $cibiliansummaryItem->getId()));
    }
    
    /**
     * Return URL for resized CibilianSummary Item image
     *
     * @param Cibilia\CibilianSummary\Model\CibilianSummary $item
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
        $pager = $this->getChildBlock('cibiliansummary_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $cibiliansummaryPerPage = $this->_dataHelper->getCibilianSummaryPerPage();

            $pager->setAvailableLimit([$cibiliansummaryPerPage => $cibiliansummaryPerPage]);
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
