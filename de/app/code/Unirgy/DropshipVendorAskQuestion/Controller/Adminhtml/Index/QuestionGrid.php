<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

class QuestionGrid extends AbstractIndex
{
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_RAW)
            ->setContents(
                $this->_view->getLayout()
                    ->createBlock('\Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Grid')
                    ->toHtml()
            );
    }
}
