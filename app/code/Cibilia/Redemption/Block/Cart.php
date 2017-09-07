<?php

namespace Cibilia\Redemption\Block;

/**
 * Redemption content block
 */
class Cart extends \Magento\Framework\View\Element\Template
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
    
     protected $_request;

      protected $_checkoutSession;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $redemptionCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $_checkoutSession,
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
        $this->_checkoutSession = $_checkoutSession;
        $this->customerSession = $customerSession;
        $this->_request = $request;
    }
    
    /**
     * Retrieve redemption collection
     *
     * @return Cibilia\Redemption\Model\ResourceModel\Redemption\Collection
     */
   
    public function getRedeemAmount()
    {

        $redeemAmount = 0;

        $quoteId = $this->_checkoutSession->getQuote()->getId();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $objQuote = $objectManager->create('Magento\Quote\Model\Quote')->load($quoteId);

        if($objQuote && $objQuote->getId()){

            $redeemAmount = $objQuote->getRedeemAmount();
        }

        return $redeemAmount;

    }
    public function getAvailableRedeem()
    {

        $redeemAmount = 0;

        $availableRedeem = 0;

        $quoteId = $this->_checkoutSession->getQuote()->getId();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $objQuote = $objectManager->create('Magento\Quote\Model\Quote')->load($quoteId);

        if($objQuote && $objQuote->getId()){

            $redeemAmount = $objQuote->getRedeemAmount();
        }
        
        $objCibilian = $objectManager->create('Cibilia\Summary\Model\Summary')->load($this->customerSession->getCustomer()->getId(),'cibilian_id');
        if($objCibilian && $objCibilian->getId()){

            $requestedAmount = 0;
        
            $arrRequestedCollection = $objectManager->create('Cibilia\Redemption\Model\Redemption')->getCollection()->addFieldToFilter('cibilian_id',$objCibilian->getCibilianId())->addFieldToFilter('status',2);
            
            foreach ($arrRequestedCollection as $withdraw) {
               $requestedAmount = $requestedAmount + $withdraw->getAmount();
            }
            
            $finalAmount = $objCibilian->getPendingAmount() - $requestedAmount;
            
            $availableRedeem = $finalAmount;
        }

        return $availableRedeem;
    }
}
