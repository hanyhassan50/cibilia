<?php

namespace Unirgy\DropshipVendorProduct\Controller\Vendor;

use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Model\App;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipVendorProduct\Helper\Data as DropshipVendorProductHelperData;
use Unirgy\DropshipVendorProduct\Model\ProductFactory;
use Unirgy\Dropship\Helper\Catalog;
use Unirgy\Dropship\Helper\Data as HelperData;

class ProductDelete extends AbstractVendor
{
    public function execute()
    {
        $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        if (!$this->scopeConfig->isSetFlag('udprod/general/allow_remove', ScopeInterface::SCOPE_STORE)) {
            $this->messageManager->addError(__('Forbidden'));
        } else {
            $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
            $oldStoreId = $this->_storeManager->getStore()->getId();
            try {
                $this->_storeManager->setCurrentStore(0);
                $this->_checkProduct();
                $pId = $this->_request->getParam('id');
                $simplePids = $this->_helperCatalog->getCfgSimplePids($pId);
                $this->_modelProductFactory->create()->setId($pId)->delete();
                if (!empty($simplePids) && is_array($simplePids)) {
                    foreach ($simplePids as $simplePid) {
                        $this->_modelProductFactory->create()->setId($simplePid)->delete();
                    }
                }
                $this->messageManager->addSuccess(__('Product was deleted'));
                $this->_storeManager->setCurrentStore($oldStoreId);
            } catch (\Exception $e) {
                $this->_storeManager->setCurrentStore($oldStoreId);
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirectAfterPost();
    }
}
