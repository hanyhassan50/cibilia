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
class SalesEventOrderSubmitAfterObserver implements ObserverInterface
{
    
    /**
     * Set gift messages to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objOrder = $observer->getEvent()->getOrder();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();    

        if($objOrder->getRedeem()){

            $model = $objectManager->create('Cibilia\Redemption\Model\Redemption');


            $data = array(
                'cibilian_id' => $objOrder->getCustomerId(),
                'order_id'  => $objOrder->getId(),
                'amount' => $objOrder->getRedeem()
            );

            /*$model->setCibilianId($objOrder->getCustomerId());
            
            $model->setOrderId($observer->getEvent()->getOrder()->getId());
            
            $model->setAmount($objOrder->getRedeem());*/

            $model->setData($data);

            $model->setCreatedAt(date('Y-m-d H:i:s'));
                    
            $model->setUpdatedAt(date('Y-m-d H:i:s'));

            $model->setStatus(1);

           
            try {
                
                $model->save();
                
                $this->_updateCibilianTotals($model->getCibilianId(),$model->getAmount());

            } catch (\Exception $e) {

                //$this->messageManager->addException($e, __('Cannot submit your withdrawl request.'));
            }
        }

        $objectManager->create('Cibilia\Commission\Model\Commissions')->_genrateLockedCommission($objOrder->getId());
        //$this->_objectManager->create('Cibilia\Commission\Model\Commissions')->_genrateLockedCommission($objOrder->getId());

        $this->_notifyVendor($objOrder);
        
        
        return $this;
    }
    public function _updateCibilianTotals($cibilian_id,$amount){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
        $objSummarry = $objectManager->create('Cibilia\Summary\Model\Summary')->load($cibilian_id,'cibilian_id');

        if($objSummarry && $objSummarry->getId()){
            $newPaid = $objSummarry->getPaidAmount() + $amount;
            $newPending = $objSummarry->getPendingAmount() - $amount;
            
            $objSummarry->setPaidAmount($newPaid);
            $objSummarry->setPendingAmount($newPending);
            
            $objSummarry->save();
        }
    }
    public function _notifyVendor($order){

        $arrayVendors = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        foreach ($order->getAllVisibleItems() as $itemId => $item) {

            $ObjProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            
            if($ObjProduct->getUdropshipVendor()){

                
                $objVendor = $objectManager->get('Unirgy\Dropship\Model\Vendor')->load($ObjProduct->getUdropshipVendor());

                if($objVendor && $objVendor->getId()){

                    if(array_key_exists($objVendor->getId(),$arrayVendors)){
                        continue;
                    }
                    
                    $arrayVendors[$objVendor->getId()]['vendor']['id'] = $objVendor->getId();
                    $arrayVendors[$objVendor->getId()]['vendor']['email'] = $objVendor->getEmail();

                    $cibilianId = $this->_getCibilianId($objVendor->getEmail());
                    
                    if($cibilianId){

                        $arrayVendors[$objVendor->getId()]['cibilian']['id'] = $cibilianId;
                    }
                }
            }
             
        }
        if(count($arrayVendors)){

            $objEmail = $objectManager->create('Cibilia\Idproofs\Model\Idproof');

            foreach ($arrayVendors as $vendor) {

                $objEmail->_sendOrderNotifyToVendor($order,$vendor['vendor']['email'],$vendor['vendor']['id']);

                if($vendor['cibilian']['id']){
                    $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($vendor['cibilian']['id']);
                    if($objCustomer && $objCustomer->getId()){

                        $objEmail->_sendOrderNotifyToVendor($order,$objCustomer->getEmail(),$vendor['vendor']['id']);
                    }
                }
            } 
        }
    }
    public function _getCibilianId($email){

        $cibilianId = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendor = $objectManager->get('Unirgy\DropshipMicrosite\Model\Registration')->load($email,'email');

        if($objVendor && $objVendor->getId()){
            $cibilianId = $objVendor->getReferredBy(); 
        }
        return $cibilianId;
         
    }
    
}