<?php

namespace Cibilia\Redemption\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CatalogProductSaveAfter implements ObserverInterface
{
    
	protected $_productAction;

    public function __construct(
        \Magento\Catalog\Model\Product\Action $productAction,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_productAction = $productAction;
        $this->_scopeConfig = $scopeConfig;

    }
    public function execute(Observer $observer)
    {   
        if($observer->getProduct()->getCreatedBy() == 2 && $observer->getProduct()->getIsApproved()){
        	
            if($this->_canNotify($observer->getProduct()->getId())){
        		
        		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
	        	$objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendProductNotifyToVendorFromAdmin($observer->getProduct()->getId());

	        	$this->_productAction->updateAttributes(['0'=> $observer->getProduct()->getId()], ['is_vendor_notified' => 1], \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        	}
        }
        if($observer->getProduct()->getCreatedBy() == 1 && $observer->getProduct()->getIsApproved() && $observer->getProduct()->getStatus() == 1){
            
            if($this->_canNotify($observer->getProduct()->getId())){
                
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendProductNotifyToVendorFromAdminApproved($observer->getProduct()->getId());

                $this->_productAction->updateAttributes(['0'=> $observer->getProduct()->getId()], ['is_vendor_notified' => 1], \Magento\Store\Model\Store::DEFAULT_STORE_ID);
            }
        }
    }
    public function _canNotify($pid)
    {
    	$canNotify = false;

        //$attrId = 164;
    	$attrId = $this->_scopeConfig->getValue('custom_cibilia/product_email_notify/product_email_notify_vendor',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
        
        $result = $connection->fetchAll("SELECT value FROM catalog_product_entity_int where attribute_id='".$attrId."' AND entity_id='".$pid."'");

        $isSend = $result[0]['value'];

        if(!$isSend){
            $canNotify = true;    
        }
        return $canNotify;
    }
}
