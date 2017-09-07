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

        //$this->_notifyVendor($objOrder);
        
        
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

            $objEmail = $this->_objectManager->create('Cibilia\Idproofs\Model\Idproof');

            foreach ($arrayVendors as $vendor) {
                //$this->_sendOrderNotifyToVendor($order,$vendor['vendor']['email']);

                $objEmail->_sendOrderNotifyToVendor($order,$vendor['vendor']['email']);

                if($vendor['cibilian']['id']){
                    $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($vendor['cibilian']['id']);
                    if($objCustomer && $objCustomer->getId()){

                        //$this->_sendOrderNotifyToVendor($order,$objCustomer->getEmail());

                        $objEmail->_sendOrderNotifyToVendor($order,$objCustomer->getEmail());
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
    public function _sendOrderNotifyToVendor($order,$reciever){

        $emailTemplateVariables = array(
            'order' => $order
        );

        $senderInfo = array(
            'name' => $this->_scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->_scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        );

        $receiverInfo = array(
            'email' => $reciever
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_ORDER_NOFICATION_VENDOR_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
           /* $this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );*/
        
        } catch (\Exception $e) {
            /*$this->inlineTranslation->resume();
            $this->_messageManager->addError(
               __('Information Review email not sent.')
            );*/
            //$this->_sendVendorNotifyEmailAgain($vendor);
        }
    }
    public function getEmailtemplate($templatepath)
    {
        return $this->_scopeConfig->getValue($templatepath,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}