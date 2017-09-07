<?php

namespace Unirgy\Dropship\Controller\Adminhtml\Vendor;

use \Unirgy\Dropship\Model\Vendor;

class Delete extends AbstractVendor
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (($id = $this->getRequest()->getParam('id')) > 0 ) {
            try {
                $model = $this->_hlp->createObj('\Unirgy\Dropship\Model\Vendor');
                /* @var $model Vendor */
                $model->load($id);
                $vendorEmail = $model->getEmail();
                ///$model->setId($id)->delete();
                $model->delete();
                $this->_removeRegistration($vendorEmail);
                $this->_removeReferer($vendorEmail);
                $this->messageManager->addSuccess(__('Vendor was successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
    public function _removeRegistration($email)
    {
        $objVendorReg = $this->_hlp->createObj('Unirgy\DropshipMicrosite\Model\Registration');
        /* @var $model Vendor */
        $objVendorReg->load($email,'email');
        if($objVendorReg && $objVendorReg->getId()){
            $objVendorReg->delete();    
        }
    }
    public function _removeReferer($email)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
        
        $connection->query("DELETE FROM cibilian_referrals where email_id='".$email."'");

    }
}
