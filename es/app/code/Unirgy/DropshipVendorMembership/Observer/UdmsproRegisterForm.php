<?php

namespace Unirgy\DropshipVendorMembership\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class UdmsproRegisterForm extends AbstractObserver implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $form = $observer->getForm();
        $this->_addMembershipOptions($form);
    }
}
