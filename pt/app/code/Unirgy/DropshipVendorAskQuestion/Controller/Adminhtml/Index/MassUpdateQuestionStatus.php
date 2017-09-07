<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

class MassUpdateQuestionStatus extends AbstractIndex
{
    public function execute()
    {
        $questionsIds = $this->getRequest()->getParam('questions');
        if(!is_array($questionsIds)) {
             $this->messageManager->addError(__('Please select question(s).'));
        } else {
            try {
                $status = $this->getRequest()->getParam('status');
                foreach ($questionsIds as $questionId) {
                    $model = $this->_questionFactory->create()->load($questionId);
                    $model->setIsAdminChanges(true);
                    $model->setQuestionStatus($status)->save();
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 record(s) have been updated.', count($questionsIds))
                );
            }
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
            catch (\Exception $e) {
                $this->messageManager->addError(__('An error occurred while updating the selected question(s).'));
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }
}
