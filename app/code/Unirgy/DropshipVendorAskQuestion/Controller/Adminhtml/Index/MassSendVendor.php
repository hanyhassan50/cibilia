<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

class MassSendVendor extends AbstractIndex
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
                    if ($model->getId()) {
                        $model->setForcedVendorNotificationFlag(1);
                        $this->_qaHlp->notifyVendor($model);
                    }
                }
                $this->messageManager->addSuccess(
                    __('Total of %1 vendor notification(s) have been sent.', count($questionsIds))
                );
            }
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            }
            catch (\Exception $e) {
                $this->messageManager->addError(__('An error occurred while sending vendor notification(s).'));
            }
        }

        $this->_redirect('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }
}
