<?php

namespace Cibilia\Redemption\Block;

/**
 * Redemption content block
 */
class Requested extends \Magento\Framework\View\Element\Template
{
    /**
     * Redemption collection
     *
     * @var Cibilia\Redemption\Model\ResourceModel\Redemption\Collection
     */
    protected $_redemptionCollection = null;
    
    protected $_template = "redemption_requested.phtml";
    /**
     * Redemption factory
     *
     * @var \Cibilia\Redemption\Model\RedemptionFactory
     */
    protected $_redemptionCollectionFactory;
    
    /** @var \Cibilia\Redemption\Helper\Data */
    protected $_dataHelper;
    
     protected $_request;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $redemptionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request,
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
        $this->customerSession = $customerSession;
        $this->_request = $request;
    }
    
    /**
     * Retrieve redemption collection
     *
     * @return Cibilia\Redemption\Model\ResourceModel\Redemption\Collection
     */
    protected function _getCollection()
    {
        $collection = $this->_redemptionCollectionFactory->create();

        if($this->customerSession->getCustomer()->getId()){
            $collection->addFieldToFilter('cibilian_id',$this->customerSession->getCustomer()->getId());          
        }
        /*if($this->_request->getParam('id')){
             $collection->addFieldToFilter('status',1);  
        }*/
        $collection->addFieldToFilter('status',2);
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
            //$this->_redemptionCollection->setCurPage($this->getCurrentPage());
            //$this->_redemptionCollection->setPageSize($this->_dataHelper->getRedemptionPerPage());
            $this->_redemptionCollection->setOrder('updated_at','desc');
        }
       
        return $this->_redemptionCollection;
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
