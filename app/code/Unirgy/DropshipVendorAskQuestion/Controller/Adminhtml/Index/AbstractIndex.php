<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\App\Action\Context;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

abstract class AbstractIndex extends Action
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;
    protected $_coreRegistry;
    protected $_hlp;
    protected $_qaHlp;

    public function __construct(
        Context $context,
        QuestionFactory $modelQuestionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Unirgy\DropshipVendorAskQuestion\Helper\Data $udqaHelper,
        \Unirgy\Dropship\Helper\Data $udropshipHelper
    )
    {
        $this->_hlp = $udropshipHelper;
        $this->_qaHlp = $udqaHelper;
        $this->_questionFactory = $modelQuestionFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    protected $_publicActions = ['edit'];

    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'pending':
                return $this->_authorization->isAllowed('Unirgy_DropshipVendorAskQuestion::udqa_pending');
                break;
            default:
                return $this->_authorization->isAllowed('Unirgy_DropshipVendorAskQuestion::udqa_all');
                break;
        }
    }
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu("Unirgy_DropshipVendorRatings::rating")
            ->addBreadcrumb(__('Sales'), __('Sales'))
            ->addBreadcrumb(__('Dropship'), __('Dropship'))
            ->addBreadcrumb(__('Vendor Questions'), __('Vendor Questions'));
        $title = $resultPage->getConfig()->getTitle();
        $title->prepend(__('Sales'));
        $title->prepend(__('Dropship'));
        $title->prepend(__('Vendor Questions'));
        return $resultPage;
    }
}
