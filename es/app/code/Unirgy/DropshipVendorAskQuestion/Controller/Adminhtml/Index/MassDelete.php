<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

class MassDelete extends AbstractIndex
{
    public function execute()
    {
        $questionsIds = $this->getRequest()->getParam('questions');
        if(!is_array($questionsIds)) {
             $this->messageManager->addError(__('Please select question(s).'));
        } else {
            try {
                foreach ($questionsIds as $questionId) {
                    $model = $this->_questionFactory->create()->load($questionId);
                    $model->delete();
                }
                $this->messageManager->addSuccess(
                    __('Total of %d record(s) have been deleted.', count($questionsIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }
}
