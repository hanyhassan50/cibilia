<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;

use Magento\Captcha\Helper\Data as CaptchaHelperData;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\LayoutFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

class Form extends AbstractCustomer
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('udqa/customer');
        }
        $this->_view->renderLayout();
    }
}
