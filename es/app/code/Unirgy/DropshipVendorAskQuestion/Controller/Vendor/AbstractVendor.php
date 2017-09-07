<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_DropshipVendorAskQuestion
 * @copyright  Copyright (c) 2011-2012 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipVendorAskQuestion\Controller\Vendor;

use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\Dropship\Controller\Vendor\AbstractVendor as VendorAbstractVendor;
use Unirgy\Dropship\Helper\Data as HelperData;

abstract class AbstractVendor extends VendorAbstractVendor
{


    protected function _redirectQuestionAfterPost()
    {
        $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        if ($session->getUdqaLastQuestionsGridUrl()) {
            $this->getResponse()->setRedirect($session->getUdqaLastQuestionsGridUrl());
        } else {
            $this->_redirect('udqa/vendor/questions');
        }
    }
}