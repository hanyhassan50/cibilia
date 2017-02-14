<?php

namespace Unirgy\DropshipVendorMembership\Observer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;

class ControllerActionPredispatchUdprodVendorProductNew extends AbstractObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $sess = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        $v = $sess->getVendor();
        if (($limit = $v->getData('udmember_limit_products'))) {
            $pIds = $v->getAttributeProductIds();
            if (count($pIds)>=$limit) {
                throw new \Exception(__('Product Limit Exceed'));
            }
        }
    }
}
