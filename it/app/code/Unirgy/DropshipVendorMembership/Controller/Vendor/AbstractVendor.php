<?php


namespace Unirgy\DropshipVendorMembership\Controller\Vendor;

use Magento\Captcha\Helper\Data as HelperData;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipMicrosite\Controller\Vendor\AbstractVendor as VendorAbstractVendor;
use Unirgy\DropshipMicrosite\Helper\Data as DropshipMicrositeHelperData;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

abstract class AbstractVendor extends VendorAbstractVendor
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(Context $context, 
        ScopeConfigInterface $scopeConfig, 
        DesignInterface $viewDesignInterface, 
        LayoutFactory $viewLayoutFactory, 
        HelperData $captchaHelperData, 
        DropshipHelperData $udropshipHelper, 
        DropshipMicrositeHelperData $micrositeHelper, 
        PageFactory $resultPageFactory, 
        StoreManagerInterface $modelStoreManagerInterface)
    {
        $this->_storeManager = $modelStoreManagerInterface;

        parent::__construct($context, $scopeConfig, $viewDesignInterface, $viewLayoutFactory, $captchaHelperData, $udropshipHelper, $micrositeHelper, $resultPageFactory);
    }
}
