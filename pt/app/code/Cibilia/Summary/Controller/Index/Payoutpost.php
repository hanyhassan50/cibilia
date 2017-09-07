<?php

namespace Cibilia\Summary\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Payoutpost extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $customerFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        PageFactory $resultPageFactory
    ) {
        $this->customerFactory  = $customerFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Summary Index page
     *
     * @return void
     */
    public function execute()
    {
        
        $data = $this->getRequest()->getPostValue();
        
        if ($data) {

            
            $objCustomer   = $this->customerFactory->create()->load($data['custid']);

            if($objCustomer && $objCustomer->getId()){

                try {
                    
                    $customerData = $objCustomer->getDataModel();
                    
                    $customerData->setCustomAttribute('default_payout_type', $data['paytype']);
                    $customerData->setCustomAttribute('payout_paypal_email', $data['paypal_email']);
                    $customerData->setCustomAttribute('payout_bank_details', $data['payout_bank_detail']);
                    
                    $objCustomer->updateData($customerData);
                    
                    $objCustomer->save();


                    $this->messageManager->addSuccess(__('Your Default Payout has been saved'));

                    $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                    
                    $this->_redirect('summary/index/payout/');
                    return;
                
                } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Cannot save your default payout.'));
                }

            }else{
                $this->messageManager->addError(__('Cannot save your default payout.'));
            }
            

        }
        $this->_redirect('summary/index/payout/');
    }
}
