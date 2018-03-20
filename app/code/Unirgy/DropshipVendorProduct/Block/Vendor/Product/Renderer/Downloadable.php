<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer;

use Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable as TabDownloadable;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Downloadable extends TabDownloadable implements RendererInterface
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Unirgy_DropshipVendorProduct::product/edit/downloadable.phtml');
    }

    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    protected $_element;
    public function setElement(AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild('samples','Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\Downloadable\Samples');
        $this->addChild('links','Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\Downloadable\Links');
        return $this;
    }
}