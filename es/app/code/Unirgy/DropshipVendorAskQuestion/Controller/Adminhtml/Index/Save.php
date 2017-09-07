<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

class Save extends AbstractIndex
{
    public function execute()
    {
        if (($data = (array)$this->getRequest()->getPost()) && ($questionId = $this->getRequest()->getParam('id'))) {
            $question = $this->_questionFactory->create()->load($questionId);

            if (! $question->getId()) {
                $this->messageManager->addError(__('The question was removed by another user or does not exist.'));
            } else {
                try {
                    $question->setIsAdminChanges(true);
                    $question->addData($data)->save();
                    $this->messageManager->addSuccess(__('The question has been saved.'));
                } catch (\Exception $e){
                    $this->messageManager->addError($e->getMessage());
                }
            }

            return $this->_redirect($this->getRequest()->getParam('ret') == 'pending' ? '*/*/pending' : '*/*/');
        }
        $this->_redirect('/');
    }
}
