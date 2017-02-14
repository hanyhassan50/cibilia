<?php

namespace Cibilia\Redemption\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Redeem extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
    protected $_checkoutSession;
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $_checkoutSession,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_checkoutSession = $_checkoutSession;
        parent::__construct($context);
    }
	
    /**
     * Default Redemption Index page
     *
     * @return void
     */
    public function execute()
    {
        
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $quoteId = $this->_checkoutSession->getQuote()->getId();
            $canRedeem = false;
            $objQuote = $this->_objectManager->create('Magento\Quote\Model\Quote')->load($quoteId);

            if($objQuote && $objQuote->getId()){

                if($data['remove_redeem']){
                    $objQuote->setRedeemAmount(0);
                    $canRedeem = true;
                }else{
                    $intRedeemAmount = (int) $data['redem_amount'];
                    if($this->_isAvailableRedeem($intRedeemAmount)) {

                        $intRedeemAmount = abs($intRedeemAmount);
                        if($intRedeemAmount > $objQuote->getSubtotalWithDiscount()){
                            $intRedeemAmount = $objQuote->getSubtotalWithDiscount();
                        }
                        $objQuote->setRedeemAmount($intRedeemAmount);
                        $canRedeem = true;
                        
                    }
                }
                if($canRedeem){
                    
                    $objQuote->setTotalsCollectedFlag(false)->collectTotals();

                    $objQuote->save(); 
                }else{
                    $this->messageManager->addError(__('Can not Apply for Redeem.'));
                }
            }
        }
        
        $this->_redirect('checkout/cart/');
    }
    public function _updateCibilianTotal($cibilian_id,$amount){
    
        $objSummarry = $this->_objectManager->create('Cibilia\Summary\Model\Summary')->load($cibilian_id,'cibilian_id');

        /*if($objSummarry && $objSummarry->getId()){
            $oldTotal
        }*/


    }
    public function _isAvailableRedeem($amount){
        
        $canRedeem = false;
        $objCustomerSession = $this->_objectManager->create('\Magento\Customer\Model\Session');

        $objCibilian = $this->_objectManager->create('Cibilia\Summary\Model\Summary')->load($objCustomerSession->getCustomer()->getId(),'cibilian_id');
        if($objCibilian && $objCibilian->getId()){

            $requestedAmount = 0;
        
            $arrRequestedCollection = $this->_objectManager->create('Cibilia\Redemption\Model\Redemption')->getCollection()->addFieldToFilter('cibilian_id',$objCibilian->getCibilianId())->addFieldToFilter('status',2);
            
            foreach ($arrRequestedCollection as $withdraw) {
               $requestedAmount = $requestedAmount + $withdraw->getAmount();
            }
            
            $finalAmount = $objCibilian->getPendingAmount() - $requestedAmount;
            
            if($finalAmount >= $amount){
                $canRedeem = true;
            }
        }

        return $canRedeem;
    }
}
