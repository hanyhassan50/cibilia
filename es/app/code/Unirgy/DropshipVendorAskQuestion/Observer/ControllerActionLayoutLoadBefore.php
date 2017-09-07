<?php

namespace Unirgy\DropshipVendorAskQuestion\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;

class ControllerActionLayoutLoadBefore extends AbstractObserver implements ObserverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(ScopeConfigInterface $configScopeConfigInterface)
    {
        $this->_scopeConfig = $configScopeConfigInterface;

    }

    public function execute(Observer $observer)
    {
        if ($observer->getAction()
            && $observer->getAction()->getFullActionName()=='catalog_product_view'
        ) {
            if ($this->_scopeConfig->isSetFlag('udqa/general/product_info_tabbed', ScopeInterface::SCOPE_STORE)) {
                $observer->getAction()->getLayout()->getUpdate()->addHandle('udqa_catalog_product_view_tabbed');
            } else {
                $observer->getAction()->getLayout()->getUpdate()->addHandle('udqa_catalog_product_view');
            }
        }
    }
}
