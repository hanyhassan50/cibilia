<?php

namespace Unirgy\DropshipVendorProduct\Observer;

use Magento\CatalogInventory\Model\Stock\Item;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipVendorProduct\Helper\Data as DropshipVendorProductHelperData;
use Unirgy\Dropship\Helper\Data as HelperData;

class CatalogProductEditAction extends AbstractObserver implements ObserverInterface
{

    public function __construct(HelperData $helperData, 
        ScopeConfigInterface $configScopeConfigInterface, 
        StoreManagerInterface $modelStoreManagerInterface, 
        ProductFactory $modelProductFactory, 
        DropshipVendorProductHelperData $dropshipVendorProductHelperData)
    {


        parent::__construct($helperData, $configScopeConfigInterface, $modelStoreManagerInterface, $modelProductFactory, $dropshipVendorProductHelperData);
    }

    public function execute(Observer $observer)
    {
        return;
        $product = $observer->getEvent()->getProduct();
        $vendor = $this->_hlp->getVendor($product->getUdropshipVendor());
        if ($vendor && $vendor->getId()) {
            foreach ([
                [
                'udprod_manage_stock'=>Item::XML_PATH_MANAGE_STOCK,
                'udprod_backorders'=>Item::XML_PATH_BACKORDERS,
                ],
                [
                'udprod_min_qty'=>Item::XML_PATH_MIN_QTY,
                'udprod_min_sale_qty'=>Item::XML_PATH_MIN_SALE_QTY,
                'udprod_max_sale_qty'=>Item::XML_PATH_MAX_SALE_QTY,
                ],
            ] as $isFloat=>$cfgKeyMap) {
                foreach ($cfgKeyMap as $vKey=>$cfgPath) {
                    if ($vendor->getData('is_'.$vKey)) {
                    foreach ([
                        $this->_storeManager->getStore(),
                        $this->_storeManager->getStore(0),
                    ] as $store) {
                        if ($isFloat) {
                            $store->setConfig($cfgPath, (float)$vendor->getData($vKey));
                            Mage::getConfig()->setNode('default/'.$cfgPath, (float)$vendor->getData($vKey));
                        } else {
                            $store->setConfig($cfgPath, (int)$vendor->getData($vKey));
                            Mage::getConfig()->setNode('default/'.$cfgPath, (int)$vendor->getData($vKey));
                        }
                    }}
                }
            }
        }
    }
}
