<?php

namespace Cibilia\Commissions\Block;

/**
 * Commissions content block
 */
class Commissions extends \Magento\Framework\View\Element\Template
{
    /**
     * Commissions collection
     *
     * @var Cibilia\Commissions\Model\ResourceModel\Commissions\Collection
     */
    protected $_commissionsCollection = null;
    
    /**
     * Commissions factory
     *
     * @var \Cibilia\Commissions\Model\CommissionsFactory
     */
    protected $_commissionsCollectionFactory;
    
    /** @var \Cibilia\Commissions\Helper\Data */
    protected $_dataHelper;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Commissions\Model\ResourceModel\Commissions\CollectionFactory $commissionsCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cibilia\Commissions\Model\ResourceModel\Commissions\CollectionFactory $commissionsCollectionFactory,
        \Cibilia\Commissions\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->_commissionsCollectionFactory = $commissionsCollectionFactory;
        $this->_dataHelper = $dataHelper;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Retrieve commissions collection
     *
     * @return Cibilia\Commissions\Model\ResourceModel\Commissions\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_commissionsCollectionFactory->create();
        return $collection;
    }
    
    /**
     * Retrieve prepared commissions collection
     *
     * @return Cibilia\Commissions\Model\ResourceModel\Commissions\Collection
     */
    public function getCollection()
    {
        if (is_null($this->_commissionsCollection)) {
            $this->_commissionsCollection = $this->_getCollection();
            $this->_commissionsCollection->setCurPage($this->getCurrentPage());
            $this->_commissionsCollection->setPageSize($this->_dataHelper->getCommissionsPerPage());
            $this->_commissionsCollection->setOrder('published_at','asc');
        }

        return $this->_commissionsCollection;
    }
    
    /**
     * Fetch the current page for the commissions list
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
     * @param Cibilia\Commissions\Model\Commissions $commissionsItem
     * @return string
     */
    public function getItemUrl($commissionsItem)
    {
        return $this->getUrl('*/*/view', array('id' => $commissionsItem->getId()));
    }
    
    /**
     * Return URL for resized Commissions Item image
     *
     * @param Cibilia\Commissions\Model\Commissions $item
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
        $pager = $this->getChildBlock('commissions_list_pager');
        if ($pager instanceof \Magento\Framework\Object) {
            $commissionsPerPage = $this->_dataHelper->getCommissionsPerPage();

            $pager->setAvailableLimit([$commissionsPerPage => $commissionsPerPage]);
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
