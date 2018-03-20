<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Vendor;

use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\Dropship\Helper\Data as HelperData;

class QuestionPost extends AbstractVendor
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    public function __construct(Context $context,
        ScopeConfigInterface $scopeConfig, 
        DesignInterface $viewDesignInterface, 
        StoreManagerInterface $storeManager, 
        LayoutFactory $viewLayoutFactory, 
        Registry $registry, 
        ForwardFactory $resultForwardFactory, 
        HelperData $helper, 
        PageFactory $resultPageFactory, 
        RawFactory $resultRawFactory, 
        Header $httpHeader, 
        QuestionFactory $modelQuestionFactory
    )
    {
        $this->_questionFactory = $modelQuestionFactory;

        parent::__construct($context, $scopeConfig, $viewDesignInterface, $storeManager, $viewLayoutFactory, $registry, $resultForwardFactory, $helper, $resultPageFactory, $resultRawFactory, $httpHeader);
    }

    public function execute()
    {
        $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');

        if ($data = $this->getRequest()->getPost('question')) {
            $id = $this->getRequest()->getParam('id');
            $updateData = array_intersect_key($data, array_flip(array('answer_text','visibility')));

            try {
                $question = $this->_questionFactory->create()->load($id);

                if (!$question->validateVendor($session->getVendorId())) {
                    throw new \Exception('Question not found');
                }

                if ($this->getRequest()->getParam('send_email')) {
                    $question->setIsCustomerNotified(0);
                }

                $question->addData($updateData)->save();

                $this->messageManager->addSuccess(__('Question was successfully saved'));
                $session->setUdqaData(false);

                $this->_redirectQuestionAfterPost();

                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_hlp->logError($e);
                $session->setUdqaData($data);
                $this->_redirectQuestionAfterPost();
                return;
            }
        }
        $this->messageManager->addError(__('Unable to find a data to save'));
        $this->_redirectQuestionAfterPost();
    }
}
