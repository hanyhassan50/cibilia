<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\CustomOptions;
use Magento\Framework\View\Element\Template;

class Option extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options\Option
{
    protected function _prepareLayout()
    {
        foreach ($this->_productOptionConfig->getAll() as $option) {
            $this->addChild($option['name'] . '_option_type', $this->udMapRenderer($option['renderer']));
        }

        return Template::_prepareLayout();
    }
    public function udMapRenderer($renderer)
    {
        $objMng = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Unirgy\DropshipVendorProduct\Helper\Data $udpHlp */
        $udpHlp = $objMng->get('\Unirgy\DropshipVendorProduct\Helper\Data');
        return $udpHlp->mapCustomOptionsRenderer($renderer);
    }
}