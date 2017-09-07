<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;

use Magento\Captcha\Helper\Data as CaptchaHelperData;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

class View extends AbstractCustomer
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    public function __construct(Context $context,
        HelperData $helperData, 
        DropshipHelperData $dropshipHelperData, 
        CaptchaHelperData $captchaHelperData, 
        QuestionFactory $modelQuestionFactory, 
        Registry $frameworkRegistry
    )
    {
        $this->_questionFactory = $modelQuestionFactory;
        $this->_coreRegistry = $frameworkRegistry;

        parent::__construct($context, $helperData, $dropshipHelperData, $captchaHelperData);
    }

    public function execute()
    {
        $question = $this->_questionFactory->create()->load($this->getRequest()->getParam('question_id'));
        if (!$question->getId() || !$question->validateCustomer(ObjectManager::getInstance()->get('Magento\Customer\Model\Session')->getCustomerId())) {
            $this->messageManager->addError(__('Question not found.'));
            $this->_redirect('*/*/index');
            return $this;
        } else {
            $this->_coreRegistry->register('udqa_question', $question);
            $this->_view->loadLayout();
            $this->_view->getLayout()->initMessages();
            $navigationBlock = $this->_view->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('udqa/customer');
            }
            $this->_view->renderLayout();
        }
    }
}
