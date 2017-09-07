<?php

namespace Cibilia\Redemption\Block;

/**
 * Redemption content block
 */
class Withdraw extends \Magento\Framework\View\Element\Template
{
    /**
     * Redemption collection
     *
     * @var Cibilia\Redemption\Model\ResourceModel\Redemption\Collection
     */
    protected $_redemptionCollection = null;
    
    /**
     * Redemption factory
     *
     * @var \Cibilia\Redemption\Model\RedemptionFactory
     */
    protected $_redemptionCollectionFactory;
    
    /** @var \Cibilia\Redemption\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $redemptionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $redemptionCollectionFactory,
        \Cibilia\Redemption\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_redemptionCollectionFactory = $redemptionCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve redemption collection
     *
     * @return Cibilia\Redemption\Model\ResourceModel\Redemption\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_redemptionCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared redemption collection
     *
     * @return Cibilia\Redemption\Model\ResourceModel\Redemption\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_redemptionCollection)) {
            $this->_redemptionCollection = $this->_getCollection();
            $this->_redemptionCollection->setCurPage($this->getCurrentPage());
            $this->_redemptionCollection->setPageSize($this->_dataHelper->getRedemptionPerPage());
            $this->_redemptionCollection->setOrder('published_at','asc');
        }

        return $this->_redemptionCollection;
    }
    
    /**
     * Fetch the current page for the redemption list
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
     * @param Cibilia\Redemption\Model\Redemption $redemptionItem
     * @return string
     */
    public function getItemUrl($redemptionItem)
    {
        return $this->getUrl('*/*/view', array('id' => $redemptionItem->getId()));
    }
    
    /**
     * Return URL for resized Redemption Item image
     *
     * @param Cibilia\Redemption\Model\Redemption $item
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
        $pager = $this->getChildBlock('redemption_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $redemptionPerPage = $this->_dataHelper->getRedemptionPerPage();

            $pager->setAvailableLimit([$redemptionPerPage => $redemptionPerPage]);
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
