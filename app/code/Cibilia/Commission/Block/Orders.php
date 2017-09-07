<?php

namespace Cibilia\Commission\Block;

/**
 * Commission content block
 */
class Orders extends \Magento\Framework\View\Element\Template
{
    /**
     * Commission collection
     *
     * @var Cibilia\Commission\Model\ResourceModel\Commission\Collection
     */
    protected $_commissionCollection = null;
    
    protected $_template = "orders.phtml";
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
            //$this->_commissionCollection->setCurPage($this->getCurrentPage());
            //$this->_commissionCollection->setPageSize($this->_dataHelper->getCommissionPerPage());
            $this->_commissionCollection->setOrder('updated_at','desc');
        }
        
        return $this->_commissionCollection;
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
