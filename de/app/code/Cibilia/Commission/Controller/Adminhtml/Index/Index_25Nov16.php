<?php

namespace Cibilia\Commission\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cibilia_Commission::commission_manage');
    }

    /**
     * Commission List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cibilia_Commission::commission_manage'
        )->addBreadcrumb(
            __('Commission'),
            __('Commission')
        )->addBreadcrumb(
            __('Manage Commission'),
            __('Manage Commission')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Earned History'));
        return $resultPage;
    }
}
