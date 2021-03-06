<?php

namespace Unirgy\Dropship\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Unirgy\Dropship\Helper\ProtectedCode;
use \Unirgy\Dropship\Observer\AbstractObserver;

class MultishippingAfterSubmitOrder extends AbstractObserver implements ObserverInterface
{
    /**
     * @var ProtectedCode
     */
    protected $_hlpPr;

    public function __construct(
        ProtectedCode $helperProtectedCode,
        \Unirgy\Dropship\Observer\Context $context,
        array $data = []
    )
    {
        $this->_hlpPr = $helperProtectedCode;

        parent::__construct($context, $data);
    }

    public function execute(Observer $observer)
    {
        $orders = $observer->getEvent()->getOrders();
        if (!empty($orders)) {
            foreach ($observer->getEvent()->getOrders() as $order) {
                $observer->getEvent()->setOrder($order->setNoDropshipFlag(false));
                $this->_hlpPr->sales_order_save_after($observer);
            }
        }
    }
}
