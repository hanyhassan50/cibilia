<?php
namespace Cibilia\Idproofs\Controller\Index; 

class Approve extends \Magento\Framework\App\Action\Action {
   
    
	public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        
        if ( $this->getRequest()->getPost() ) {
            $r = $this->getRequest();
            $hlp = $this->_hlp;
            try {
                $vid = $r->getParam('vid');
                $pid = $r->getParam('id');
                
                /*var_dump($vid);
                var_dump($pid);
                die;*/
                $objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($vid);
                
                if($objVendor && $objVendor->getId()){

                    
                    $objProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($pid);
                    
                    if(!$objProduct && !$objProduct->getId()){
                        $this->messageManager->addError(__('Product Canot be approved'));
                        return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());
                    }

                    $objVendorSession = $objectManager->get('Unirgy\Dropship\Model\Session');

                    $vendorUrl = $storeManager->getStore()->getUrl('udprod/vendor/productEdit', ['id'=>$pid]);

                    if($objVendorSession->isLoggedIn()){

                        $objVendorSession->unsCreatedBy();
                        return $resultRedirect->setPath($vendorUrl);

                    }else{

                        $objVendorSession->unsCreatedBy();
                        $objVendorSession->setVendorAsLoggedIn($objVendor);
                        
                        if (!$objVendorSession->getBeforeAuthUrl()) {
                            $objVendorSession->setBeforeAuthUrl($vendorUrl);
                        }
                        return $resultRedirect->setPath($vendorUrl);    
                    }
                }else{
                    $this->messageManager->addError(__('Cannot login as vendor'));
                    return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());
                }
                
            }catch (\Exception $e) {

                $this->messageManager->addError($e->getMessage());
            }
        }
        
        $this->messageManager->addError(__('Cannot login as vendor'));
        return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());    
    }
}
