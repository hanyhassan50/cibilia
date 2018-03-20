<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class CustomOptions extends AbstractElement
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_viewLayout;

    public function __construct(
        \Magento\Framework\View\LayoutInterface $viewLayout,
        Factory $factoryElement,
        CollectionFactory $factoryCollection, 
        Escaper $escaper,
        $data = []
    )
    {
        $this->_viewLayout = $viewLayout;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function getHtml()
    {
        $this->_renderer = $this->_viewLayout->createBlock('\Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\CustomOptions', 'admin.product.options');
        $this->_renderer->setProduct($this->_product);
        return parent::getHtml();
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