<?php

namespace Unirgy\DropshipVendorMembership\Helper\ProtectedCode;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Eav\Model\Config;
use Unirgy\DropshipVendorProduct\Model\ProductStatus;
use Unirgy\Dropship\Helper\Data as HelperData;
use Unirgy\Dropship\Model\Source;

class Context
{
    /**
     * @var Config
     */
    public $_eavConfig;

    /**
     * @var HelperData
     */
    public $_hlp;

    public $_catalogHelper;

    public function __construct(
        \Unirgy\Dropship\Helper\Catalog $catalogHelper,
        \Magento\Eav\Model\Config $modelConfig,
        \Unirgy\Dropship\Helper\Data $helperData
    )
    {
        $this->_catalogHelper = $catalogHelper;
        $this->_eavConfig = $modelConfig;
        $this->_hlp = $helperData;
    }
}