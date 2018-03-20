<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Redemption\Observer;

use Magento\Framework\Event\ObserverInterface;
/**
 * Redemption Observer Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AdminhtmlCustomerSaveAfter implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    protected $cibiliaDeniedEmail;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \StageBit\CustomCode\Model\CibilianDeniedEmail $cibiliaDenied
    ) {
        $this->customerFactory  = $customerFactory;
        $this->cibiliaDeniedEmail = $cibiliaDenied;
    }
    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $customer = $observer->getEvent()->getCustomer();

        $objCustomer   = $this->customerFactory->create()->load($customer->getId());

        $isChanges = false;

        if($objCustomer && $objCustomer->getId()){

            $customerData = $objCustomer->getDataModel();

            if($objCustomer->getApprovalStatus() == 9 && !$objCustomer->getIsIdproofNotified()) {

                $customerData->setCustomAttribute('is_idproof_notified', 1);

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendCustomerIdproofApproval($objCustomer);
                
                $isChanges = true;
            
            }
            if($objCustomer->getApprovalStatus() == 10) {
                $customerData->setCustomAttribute('is_idproof_notified', 0);
                $this->cibiliaDeniedEmail->sentIdentityNotVerifiedEmailToCibilia($objCustomer);
                $isChanges = true;
            }
            if($objCustomer->getApprovalStatus() != 9 && $objCustomer->getIsIdproofNotified()) {
                $customerData->setCustomAttribute('is_idproof_notified', 0);
                $isChanges = true;
            }
            if($isChanges){

                try {
                    
                    $objCustomer->updateData($customerData);
                    
                    $objCustomer->save();

                } catch (\Magento\Framework\Model\Exception $e) {
                    //$this->messageManager->addError($e->getMessage());
                } catch (\RuntimeException $e) {
                    //$this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    //$this->messageManager->addException($e, __('Cannot save your default payout.'));
                }

            }
        }
    }
    
}