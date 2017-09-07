<?php

namespace Unirgy\DropshipVendorMembership\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;
use Unirgy\DropshipVendorProduct\Model\ProductStatus;
use Unirgy\Dropship\Helper\Data as HelperData;
use Unirgy\Dropship\Model\Source;

class CatalogProductSaveBefore extends AbstractObserver implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    public function __construct(Layout $viewLayout, 
        HelperData $helperData)
    {
        $this->_helperData = $helperData;

        parent::__construct($viewLayout);
    }

    public function execute(Observer $observer)
    {
        $product = $observer->getProduct();
        $vId = $product->getUdropshipVendor();
        $v = $this->_helperData->getVendor($vId);
        if ($v && $v->getId()) {
            $disabled = [
                Source::VENDOR_STATUS_CANCELEDMEMBER,
                Source::VENDOR_STATUS_PENDINGMEMBER,
                Source::VENDOR_STATUS_EXPIREDMEMBER,
                Source::VENDOR_STATUS_SUSPENDEDMEMBER
            ];
            $enabled = [
                Source::VENDOR_STATUS_ACTIVE,
            ];
            if (in_array($v->getData('status'), $disabled)) {
                $product->setStatus(ProductStatus::STATUS_SUSPENDED);
            }
        }
    }
}
