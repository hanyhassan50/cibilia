<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Form;

class BaseImage extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\BaseImage
{
    protected $_urlBuilder;
    public function __construct(
        \Magento\Framework\Url $urlBuilder,
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Backend\Model\UrlFactory $backendUrlFactory,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\File\Size $fileConfig,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $assetRepo, $backendUrlFactory, $catalogData, $fileConfig, $layout, $data);
    }
    const ELEMENT_OUTPUT_TEMPLATE = 'Unirgy_DropshipVendorProduct::unirgy/udprod/vendor/product/base_image.phtml';
    public function createElementHtmlOutputBlock()
    {
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->layout->createBlock(
            'Magento\Framework\View\Element\Template',
            'product.details.form.base.image.element'
        );
        $block->setTemplate(self::ELEMENT_OUTPUT_TEMPLATE);

        return $block;
    }
    public function getLabel()
    {
        return __('Images and Videos');
    }
    public function assignBlockVariables(\Magento\Framework\View\Element\Template $block)
    {
        $block->assign([
            'htmlId' => $this->_escaper->escapeHtml($this->getHtmlId()),
            'fileMaxSize' => $this->maxFileSize,
            'uploadUrl' => $this->_escaper->escapeHtml($this->_getUploadUrl()),
            'spacerImage' => $this->assetRepo->getUrl('images/spacer.gif'),
            'imagePlaceholderText' => __('Click here or drag and drop to add images.'),
            'deleteImageText' => __('Delete image'),
            'makeBaseText' => __('Make Base'),
            'hiddenText' => __('Hidden'),
            'imageManagementText' => __(''),
        ]);

        return $block;
    }
    protected function _getUploadUrl()
    {
        return $this->_urlBuilder->getUrl('udprod/vendor/upload');
    }
}