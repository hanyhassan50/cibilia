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

        if($objOrder->getRedeem()){

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();    

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
        


        $customerId = $observer->getEvent()->getOrder()->getCustomerId();
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
}