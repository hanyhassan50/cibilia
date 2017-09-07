<?php

namespace Unirgy\DropshipVendorMembership\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class UmicrositeCheckPermission extends AbstractObserver implements ObserverInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(Layout $viewLayout, 
        StoreManagerInterface $modelStoreManagerInterface)
    {
        $this->_storeManager = $modelStoreManagerInterface;

        parent::__construct($viewLayout);
    }

    public function execute(Observer $observer)
    {
        if (!$observer->getVendor() || !$observer->getVendor()->getId()) {
            return $this;
        }
        switch ($observer->getAction()) {
            case 'microsite':
                if (!$observer->getVendor()->getData('udmember_allow_microsite')) {
                    $observer->getTransport()->setRedirect(
                        $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK)
                    );
                    $observer->getTransport()->setAllowed(false);
                }
                break;
        }
        return $this;
    }
}
