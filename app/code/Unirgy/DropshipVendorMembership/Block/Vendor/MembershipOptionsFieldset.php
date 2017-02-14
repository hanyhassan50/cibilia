<?php

namespace Unirgy\DropshipVendorMembership\Block\Vendor;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class MembershipOptionsFieldset extends Template implements RendererInterface
{
    protected $_element;

    protected function _construct()
    {
        $this->setTemplate('Unirgy_DropshipVendorMembership::unirgy/udmember/vendor/membership_options_fieldset.phtml');
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        return $this->toHtml();
    }
    public function getChildElementHtml($elem='_cfg_quick_create')
    {
        return $this->getElement()->getForm()->getElement($elem)->toHtml();
    }
    public function getChildElement($elem='_cfg_quick_create')
    {
        return $this->getElement()->getForm()->getElement($elem);
    }
    protected $_product;
    public function setProduct($product)
    {
        $this->_product = $product;
        return $this;
    }
    public function getProduct()
    {
        return $this->_product;
    }
}