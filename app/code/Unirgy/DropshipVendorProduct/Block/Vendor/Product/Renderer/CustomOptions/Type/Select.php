<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\CustomOptions\Type;

class Select extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Type\Select
{
    protected function _prepareLayout()
    {
        $result = parent::_prepareLayout();
        $this->getLayout()
            ->getBlock($this->getNameInLayout() . '.add_select_row_button')
            ->setTemplate('Unirgy_DropshipVendorProduct::widget/button.phtml');
        ;
        $this->getLayout()
            ->getBlock($this->getNameInLayout() . '.delete_select_row_button')
            ->setTemplate('Unirgy_DropshipVendorProduct::widget/button.phtml');
        ;
        return $result;
    }
}