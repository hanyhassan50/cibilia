<?php
namespace Cibilia\Idproofs\Controller\Index; 
 
class Info extends \Magento\Framework\App\Action\Action {
   
    public function execute()
    {
        
		
        if ($this->getRequest()->getPost()) {

        	$id = $this->getRequest()->getParam('id');

        	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        	
        	$objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($id);
        	if($objVendor->getId()){
        		if($objVendor->getIsInfoReviewed()){

        			$resultRedirect = $this->resultRedirectFactory->create();
        			$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

        			$this->messageManager->addNotice(__('Information had already been reviewd by Vendor.'));

					return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());
        		}	
        	}
        }
        
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('vendor_review_form1');
        $this->_view->renderLayout();
    }
}