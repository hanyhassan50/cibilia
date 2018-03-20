<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\View\Element\Template;
use Unirgy\Dropship\Helper\Catalog;

class CustomOptions extends Options implements RendererInterface
{
    /**
     * @var Catalog
     */
    protected $_helperCatalog;

    public function __construct(Context $context, 
        Catalog $helperCatalog, 
        array $data = [])
    {
        $this->_helperCatalog = $helperCatalog;

        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild(
            'add_button',
            'Magento\Backend\Block\Widget\Button',
            ['label' => __('Add New Option'), 'class' => 'add', 'id' => 'add_new_defined_option']
        )->setTemplate('Unirgy_DropshipVendorProduct::widget/button.phtml');

        $this->addChild('options_box', 'Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\CustomOptions\Option');

        return Template::_prepareLayout();
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