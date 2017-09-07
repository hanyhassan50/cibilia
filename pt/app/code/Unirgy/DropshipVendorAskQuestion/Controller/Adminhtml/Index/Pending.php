<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;

class Pending extends AbstractIndex
{
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_coreRegistry->register('usePendingFilter', true);
            return $this->_forward('questionGrid');
        }

        $this->_coreRegistry->register('usePendingFilter', true);
        $page = $this->_initAction();
        $page->addBreadcrumb(__('Pending Questions/Answers'), __('Pending Questions/Answers'));
        $title = $page->getConfig()->getTitle();
        $title->prepend(__('Pending Questions/Answers'));
        return $page->addContent($page->getLayout()->createBlock('Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Main'));
    }
}
