<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

class Delete extends AbstractIndex
{
    public function execute()
    {
        $questionId = $this->getRequest()->getParam('id', false);

        try {
            $this->_questionFactory->create()->setId($questionId)->delete();

            $this->messageManager->addSuccess(__('The question has been deleted'));
            if( $this->getRequest()->getParam('ret') == 'pending' ) {
                $this->_redirect('*/*/pending');
            } else {
                $this->_redirect('*/*/');
            }
            return;
        } catch (\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }

        $this->_redirect('/');
    }
}
