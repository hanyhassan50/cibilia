<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\LayoutFactory;

class Edit extends AbstractIndex
{
    public function execute()
    {
        $resultPage = $this->_initAction();
        $title = $resultPage->getConfig()->getTitle();

        $questionData = $this->_questionFactory->create();
        if( $this->getRequest()->getParam('id') ) {
            $questionData->load($this->getRequest()->getParam('id'));
            $this->_coreRegistry->register('question_data', $questionData);
        }

        $title->prepend($questionData->getId()
            ? __("Edit Question")
            : __('New Question'));

        return $resultPage->addContent($this->_view->getLayout()->createBlock('\Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Edit'));
    }
}
