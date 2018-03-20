<?php

namespace Unirgy\DropshipPayout\Controller\Adminhtml\Payout;

use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\ResultFactory;

class RefundRowGrid extends AbstractPayout
{
    public function execute()
    {
        return $this->resultFactory->create(ResultFactory::TYPE_RAW)->setContents(
            $this->_view->getLayout()
                ->createBlock('\Unirgy\DropshipPayout\Block\Adminhtml\Payout\Edit\Tab\RefundRows', 'admin.udpayout.refund_rows')
                ->setPayoutId($this->getRequest()->getParam('id'))
                ->toHtml()
        );
    }
}
