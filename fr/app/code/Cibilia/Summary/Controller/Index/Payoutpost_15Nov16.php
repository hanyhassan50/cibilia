<?php

namespace Cibilia\Summary\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Payoutpost extends \Magento\Framework\App\Action\Action
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
     * Default Summary Index page
     *
     * @return void
     */
    public function execute()
    {
        
        $data = $this->getRequest()->getPostValue();
        
        if ($data) {

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($data['custid']);

            if($objCustomer && $objCustomer->getId()){

                try {
                    
                   /* var_dump($data['paytype']);
                    var_dump($data['paypal_email']);
                    var_dump($data['payout_bank_detail']);
                    echo "<pre>";
                    print_r($objCustomer->getData());
                    die;*/

                    $objCustomer->setDefaultPayoutType($data['paytype']);
                    $objCustomer->setPayoutPaypalEmail($data['paypal_email']);
                    $objCustomer->setPayoutBankDetails($data['payout_bank_detail']);

                    $objCustomer->save();
                    
                    //$this->_updateCibilianTotal($model->getCibilianId(),$model->getAmount());

                    $this->messageManager->addSuccess(__('Your Default Payout has been saved'));

                    //$this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                    
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
