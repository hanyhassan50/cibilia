<?php
namespace Cibilia\Idproofs\Controller\Index; 

class Vendorpost extends \Magento\Framework\App\Action\Action {
   
    
	public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ( $this->getRequest()->getPost() ) {
            $r = $this->getRequest();
            $hlp = $this->_hlp;
            try {
                $id = $r->getParam('id');
                $data = $r->getParams();
                
                $data['vendor_attn'] = $data['vendor_fname'].' '.$data['vendor_lname'];
            	$data['password_confirm'] = $data['password'];
            	if(isset($data['vendor_categories'])){
                    $data['vendor_categories'] = implode(',', $data['vendor_categories']);
                }
                
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    			$model = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($id);


                //$hlp->processPostMultiselects($data);
                $model->addData($data);

                $model->save();
                
                //$objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendVendorTypeSelectEmail($model);

                $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendVendorTypeEmail($model);
                
                $this->messageManager->addSuccess(__('Vendor Information successfully saved.'));

                $model->setIsInfoReviewed(1);
                
                $model->setStatus('V');

                $model->save();

            } catch (\Exception $e) {

                $this->messageManager->addError($e->getMessage());
            }
        }
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		        
        return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());
    }
}
