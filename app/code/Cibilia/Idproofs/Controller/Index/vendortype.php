<?php
namespace Cibilia\Idproofs\Controller\Index; 

class Vendortype extends \Magento\Framework\App\Action\Action {
   
    
	public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ( $this->getRequest()->getPost() ) {
            $r = $this->getRequest();
            try {
                $id = $r->getParam('id');
                $data = $r->getParams();
                
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $model = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($id);

                $model->setVendorType($data['vendor_type']);
                
                $model->save();
                
                $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendVendorTypeEmail($model);

                $this->messageManager->addSuccess(__('Vendor Type successfully saved.'));

                $model->setIsTypeSelected(1);

                $model->save();
                

            } catch (\Exception $e) {

                $this->messageManager->addError($e->getMessage());
            }
        }
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                
        return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());
    }
}
