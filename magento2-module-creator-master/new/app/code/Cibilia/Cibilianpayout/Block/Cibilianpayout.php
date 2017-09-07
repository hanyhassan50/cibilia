<?php

namespace Cibilia\Cibilianpayout\Block;

/**
 * Cibilianpayout content block
 */
class Cibilianpayout extends \Magento\Framework\View\Element\Template
{
    /**
     * Cibilianpayout collection
     *
     * @var Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout\Collection
     */
    protected $_cibilianpayoutCollection = null;
    
    /**
     * Cibilianpayout factory
     *
     * @var \Cibilia\Cibilianpayout\Model\CibilianpayoutFactory
     */
    protected $_cibilianpayoutCollectionFactory;
    
    /** @var \Cibilia\Cibilianpayout\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout\CollectionFactory $cibilianpayoutCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout\CollectionFactory $cibilianpayoutCollectionFactory,
        \Cibilia\Cibilianpayout\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_cibilianpayoutCollectionFactory = $cibilianpayoutCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve cibilianpayout collection
     *
     * @return Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_cibilianpayoutCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared cibilianpayout collection
     *
     * @return Cibilia\Cibilianpayout\Model\ResourceModel\Cibilianpayout\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_cibilianpayoutCollection)) {
            $this->_cibilianpayoutCollection = $this->_getCollection();
            $this->_cibilianpayoutCollection->setCurPage($this->getCurrentPage());
            $this->_cibilianpayoutCollection->setPageSize($this->_dataHelper->getCibilianpayoutPerPage());
            $this->_cibilianpayoutCollection->setOrder('published_at','asc');
        }

        return $this->_cibilianpayoutCollection;
    }
    
    /**
     * Fetch the current page for the cibilianpayout list
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
     * @param Cibilia\Cibilianpayout\Model\Cibilianpayout $cibilianpayoutItem
     * @return string
     */
    public function getItemUrl($cibilianpayoutItem)
    {
        return $this->getUrl('*/*/view', array('id' => $cibilianpayoutItem->getId()));
    }
    
    /**
     * Return URL for resized Cibilianpayout Item image
     *
     * @param Cibilia\Cibilianpayout\Model\Cibilianpayout $item
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
        $pager = $this->getChildBlock('cibilianpayout_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $cibilianpayoutPerPage = $this->_dataHelper->getCibilianpayoutPerPage();

            $pager->setAvailableLimit([$cibilianpayoutPerPage => $cibilianpayoutPerPage]);
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
