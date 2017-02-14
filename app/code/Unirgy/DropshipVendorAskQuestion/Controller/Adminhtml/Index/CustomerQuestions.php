<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;

class CustomerQuestions extends AbstractIndex
{
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents(
            $this->_view->getLayout()
                ->createBlock('\Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Grid', 'admin.customer.questions')
                ->setCustomerId($this->getRequest()->getParam('id'))
                ->setUisMassactionAvailable(false)
                ->setUseAjax(true)
                ->toHtml()
        );
    }
}
