<?php
namespace Cibilia\Cibilians\Controller\Advertisers; 
 
class Edit extends \Magento\Framework\App\Action\Action {
   
	public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $custId = $objectManager->create('Magento\Customer\Model\Session')->getCustomer()->getId();
        
        if($custId){
            $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($custId);
            if($objCustomer->getId() && $objCustomer->getApprovalStatus() != '9'){
                
                $resultRedirect = $this->resultRedirectFactory->create();
                
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

                return $resultRedirect->setPath($storeManager->getStore()->getUrl('customregistration/customfields'));
            }
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('newadvertisers_type1');
        $this->_view->renderLayout();
    }
}