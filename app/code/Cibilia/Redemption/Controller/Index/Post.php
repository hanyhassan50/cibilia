<?php

namespace Cibilia\Redemption\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Post extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
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
            
            if($data['defaultpay']){

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($data['cibilian_id']);

                
                if($objCustomer->getDefaultPayoutType() == 1){
                   $data['email_bank_details'] = $objCustomer->getPayoutBankDetails();
                }
                if($objCustomer->getDefaultPayoutType() == 2){
                    $data['email_bank_details'] = $objCustomer->getPayoutPaypalEmail();
                }
                
                $data['withdrawal_type'] = $objCustomer->getDefaultPayoutType();

            }else{
                if($data['withdrawal_type'] == 2){
                    $data['email_bank_details'] = $data['paypal_email'];
                }
                if($data['withdrawal_type'] == 1){
                    $data['email_bank_details'] = $data['payout_bank_detail'];
                }
            }

            if($this->_isAvailableRedeem($data['amount'])) {

                $model = $this->_objectManager->create('Cibilia\Redemption\Model\Redemption');

                $model->setData($data);

                $model->setCreatedAt(date('Y-m-d H:i:s'));
                        
                $model->setUpdatedAt(date('Y-m-d H:i:s'));

                $model->setStatus(2);

               /* echo "<pre>";
                print_r($model->getData());
                die;*/
                
                try {
                    
                    $model->save();

                    $this->_objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendWithdrawRequestEmail($model);
                    
                    //$this->_updateCibilianTotal($model->getCibilianId(),$model->getAmount());

                    $this->messageManager->addSuccess(__('Your withdrawl request has been submited'));

                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                    


                    
                    $this->_redirect('summary/index/show/');
                    return;
                
                } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Cannot submit your withdrawl request.'));
                }

            }else{
                $this->messageManager->addError(__('Your withdrawl request can not submited.'));
            }

            

        }
        $this->_redirect('redemption/index/withdraw/');
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
