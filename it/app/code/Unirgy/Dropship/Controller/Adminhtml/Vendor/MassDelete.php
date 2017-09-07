<?php

namespace Unirgy\Dropship\Controller\Adminhtml\Vendor;

class MassDelete extends AbstractVendor
{
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $certIds = $this->getRequest()->getParam('vendor');
        if (!is_array($certIds)) {
            $this->messageManager->addError(__('Please select vendor(s)'));
        }
        else {
            try {
                /** @var \Unirgy\Dropship\Model\Vendor $cert */
                $cert = $this->_hlp->createObj('\Unirgy\Dropship\Model\Vendor');
                foreach ($certIds as $certId) {
                    $cert->load($certId);
                    $vendorEmail = $cert->getEmail();
                    $cert->delete();
                    $this->_removeRegistration($vendorEmail);
                    $this->_removeReferer($vendorEmail);
                    //$cert->setId($certId)->delete();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) were successfully deleted', count($certIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/index');
    }
    public function _removeRegistration($email)
    {
        $objVendorReg = $this->_hlp->createObj('Unirgy\DropshipMicrosite\Model\Registration');
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
