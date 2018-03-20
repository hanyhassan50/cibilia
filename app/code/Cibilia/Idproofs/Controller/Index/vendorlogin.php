<?php
namespace Cibilia\Idproofs\Controller\Index; 

class Vendorlogin extends \Magento\Framework\App\Action\Action {
   
    
	public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        
        if ( $this->getRequest()->getPost() ) {
            $r = $this->getRequest();
            $hlp = $this->_hlp;
            try {
                $id = $r->getParam('id');
                
                $objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($id);
                
                if($objVendor && $objVendor->getId()){
                    $objVendorSession = $objectManager->get('Unirgy\Dropship\Model\Session');

                    $vendorUrl = $storeManager->getStore()->getUrl('udropship');

                    if($objVendorSession->isLoggedIn()){
                        return $resultRedirect->setPath($vendorUrl);

                    }else{
                        $objVendorSession->setVendorAsLoggedIn($objVendor);
                        
                        if (!$objVendorSession->getBeforeAuthUrl()) {
                            $objVendorSession->setBeforeAuthUrl($vendorUrl);
                        }
                        $objVendorSession->setCreatedBy('2');
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
