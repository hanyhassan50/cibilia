<?php

namespace Unirgy\DropshipTierShipping\Observer;

use Magento\Framework\Event\ObserverInterface;

class UpdateExcludedFieldListObserver implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $block = $observer->getEvent()->getObject();
        $list = $block->getFormExcludedFieldList();
        $list = array_merge($list, ['udtiership_use_custom','udtiership_rates']);
        $block->setFormExcludedFieldList($list);

        return $this;
    }
}
