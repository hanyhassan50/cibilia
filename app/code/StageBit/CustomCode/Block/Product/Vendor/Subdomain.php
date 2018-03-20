<?php

namespace StageBit\CustomCode\Block\Product\Vendor;

use Magento\Framework\View\Element\Template;

/**
 * Class Subdomain
 * @package StageBit\CustomCode\Block\Product\Vendor
 */
class Subdomain extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Unirgy\DropshipMicrosite\Helper\Data
     */
    protected $_unirgyData;

    /**
     * @var \Unirgy\Dropship\Helper\Data
     */
    protected $_dropShipHelper;

    /**
     * Subdomain constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Cibilia\Idproofs\Model\Idproof $idproof
     * @param \Unirgy\Dropship\Model\ResourceModel\Vendor\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Unirgy\DropshipMicrosite\Helper\Data $unirgyData,
        \Unirgy\Dropship\Helper\Data $dropShipHelper,
        array $data = [])
    {
        $this->_dropShipHelper = $dropShipHelper;
        $this->_registry    =   $registry;
        $this->_unirgyData  =   $unirgyData;
        parent::__construct($context, $data);
    }

    /*
     * Get vendor subdomain
     *
     * return []
     */
    public function getVendorSubdomain(){

        $product    =   $this->_registry->registry('current_product');
        $vendor = $this->_dropShipHelper->getVendor($product->getUdropshipVendor());
        $urlKey =   $this->_unirgyData->getVendorUrl($product->getUdropshipVendor());

        return ['vendor' => $vendor, 'url' => $urlKey];
    }

}