<?php

namespace Unirgy\Dropship\Controller\Vendor;


class ProductSave extends AbstractVendor
{
    public function execute()
    {
        $hlp = $this->_hlp;
        $session = $this->_hlp->session();
        try {
            $cnt = $hlp->saveVendorProducts($this->getRequest()->getParam('vp'));
            if ($this->_hlp->isUdmultiActive()) {
                $cnt += $this->_hlp->udmultiHlp()->saveVendorProductsPidKeys($this->getRequest()->getParam('vp'));
            }
            if ($cnt) {
                $this->messageManager->addSuccess(__($cnt==1 ? '%1 product was updated' : '%1 products were updated', $cnt));
            } else {
                $session->addNotice(__('No updates were made'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $this->resultRedirectFactory->create()->setUrl($this->_httpHeader->getHttpReferer());
    }
}
