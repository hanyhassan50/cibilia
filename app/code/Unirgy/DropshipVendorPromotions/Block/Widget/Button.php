<?php

namespace Unirgy\DropshipVendorPromotions\Block\Widget;

class Button extends \Magento\Backend\Block\Widget\Button
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Unirgy_DropshipVendorPromotions::widget/button.phtml');
    }
}
