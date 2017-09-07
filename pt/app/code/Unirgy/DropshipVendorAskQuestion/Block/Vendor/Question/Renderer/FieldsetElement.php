<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Vendor\Question\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;

class FieldsetElement extends Template implements RendererInterface
{
    protected $_element;

    protected function _construct()
    {
        $this->setTemplate('Unirgy_DropshipVendorAskQuestion::udqa/vendor/question/renderer/fieldset_element.phtml');
    }

    public function getElement()
    {
        return $this->_element;
    }

    public function render(AbstractElement $element)
    {
        $this->_element = $element;
        $element->addClass('udvalidate-'.$element->getId());
        return $this->toHtml();
    }

    public function getElementHtml()
    {
        $html = $this->_element->getElementHtml();
        return $html;
    }
}