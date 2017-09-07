<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class VendorQuestions extends AbstractIndex
{
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setContents(
                $this->_view->getLayout()
                    ->createBlock('\Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Grid', 'admin.vendor.questions')
                    ->setVendorId($this->getRequest()->getParam('id'))
                    ->setUisMassactionAvailable(false)
                    ->setUseAjax(true)
                    ->toHtml()
                );
    }
}
