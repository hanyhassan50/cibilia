<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Accordion as WidgetAccordion;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutFactory;

class Accordion extends WidgetAccordion
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Unirgy_DropshipVendorProduct::unirgy/udprod/vendor/product/renderer/widget/accordion.phtml');
    }
    public function addItem($itemId, $config)
    {
        $this->_items[$itemId] = $this->getLayout()
            ->createBlock('Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\Widget\Accordion\Item')
            ->setData($config)
            ->setAccordion($this)
            ->setId($itemId);
        if (isset($config['content']) && $config['content'] instanceof AbstractBlock) {
            $this->_items[$itemId]->setChild($itemId.'_content', $config['content']);
        }

        $this->setChild($itemId, $this->_items[$itemId]);
        return $this;
    }
}