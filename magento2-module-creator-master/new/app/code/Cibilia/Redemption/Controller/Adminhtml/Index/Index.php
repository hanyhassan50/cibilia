<?php

namespace Cibilia\Redemption\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cibilia_Redemption::redemption_manage');
    }

    /**
     * Redemption List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cibilia_Redemption::redemption_manage'
        )->addBreadcrumb(
            __('Redemption'),
            __('Redemption')
        )->addBreadcrumb(
            __('Manage Redemption'),
            __('Manage Redemption')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Redemption'));
        return $resultPage;
    }
}
