<?php

namespace Unirgy\DropshipVendorMembership\Block\Vendor;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class MembershipOptionsRenderer extends Template implements RendererInterface
{
    protected $_element;
    protected function _construct()
    {
        $this->setTemplate('Unirgy_DropshipVendorMembership::unirgy/udmember/vendor/membership_options.phtml');
    }
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement(AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    public function getElement()
    {
        return $this->_element;
    }
}