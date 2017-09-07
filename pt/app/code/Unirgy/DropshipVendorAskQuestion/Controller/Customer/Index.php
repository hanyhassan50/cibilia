<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;

use Magento\Captcha\Helper\Data as CaptchaHelperData;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

class Index extends AbstractCustomer
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        if ($navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('udqa/customer');
        }
        $this->_view->renderLayout();
    }
}
