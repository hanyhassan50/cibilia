<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;

class Index extends AbstractIndex
{
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            return $this->_forward('questionGrid');
        }
        $page = $this->_initAction();
        $page->addBreadcrumb(__('All Questions/Answers'), __('All Questions/Answers'));
        $title = $page->getConfig()->getTitle();
        $title->prepend(__('All Questions/Answers'));
        return $page->addContent($page->getLayout()->createBlock('Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Main'));
    }
}
