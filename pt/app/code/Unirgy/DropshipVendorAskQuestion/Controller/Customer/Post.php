<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;

use Magento\Captcha\Helper\Data as CaptchaHelperData;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

class Post extends AbstractCustomer
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    public function __construct(Context $context, 
        HelperData $helperData, 
        DropshipHelperData $dropshipHelperData, 
        CaptchaHelperData $captchaHelperData, 
        QuestionFactory $modelQuestionFactory)
    {
        $this->_questionFactory = $modelQuestionFactory;

        parent::__construct($context, $helperData, $dropshipHelperData, $captchaHelperData);
    }

    public function execute()
    {
        if (!$this->getRequest()->isPost() && ($data = $this->_fetchFormData())) {
            $question = [];
            if (isset($data['question']) && is_array($data['question'])) {
                $question = $data['question'];
            }
        } else {
            $data   = (array)$this->getRequest()->getPost();
            $question = $this->getRequest()->getParam('question', []);
        }

        $cSess = ObjectManager::getInstance()->get('Magento\Customer\Model\Session');

        $customer = $cSess->getCustomer();

        $error = false;
        if (!empty($data)) {
            unset($question['question_id']);
            $qModel   = $this->_questionFactory->create()
                ->setData($question)
                ->setQuestionDate($this->_hlp->now());
            if ($cSess->isLoggedIn()) {
                $qModel
                    ->setCustomerEmail($customer->getEmail())
                    ->setCustomerName($customer->getFirstname().' '.$customer->getLastname())
                    ->setCustomerId($customer->getId());
            }
            $validate = $qModel->validate();
            if ($validate === true) {
                try {
                    $this->checkCaptcha();
                    $qModel->save();
                    $this->messageManager->addSuccess(__('Your question has been accepted for moderation.'));
                }
                catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $error = true;
                    $this->_saveFormData($data);
                    $this->messageManager->addError($e->getMessage());
                }
                catch (\Exception $e) {
                    $error = true;
                    $this->_saveFormData($data);
                    $this->messageManager->addError(__('Unable to post the question.'));
                }
            }
            else {
                $error = true;
                $this->_saveFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                }
                else {
                    $this->messageManager->addError(__('Unable to post the question.'));
                }
            }
        }

        return empty($question['product_id'])
            ? $this->_redirect('*/*/index')
            : $this->_redirect($this->_hlp->getObj('\Magento\Framework\HTTP\Header')->getHttpReferer());
    }
}
