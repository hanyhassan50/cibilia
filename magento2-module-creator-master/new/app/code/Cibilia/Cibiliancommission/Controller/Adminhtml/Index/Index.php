<?php

namespace Cibilia\CibilianCommission\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cibilia_CibilianCommission::cibiliancommission_manage');
    }

    /**
     * CibilianCommission List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cibilia_CibilianCommission::cibiliancommission_manage'
        )->addBreadcrumb(
            __('CibilianCommission'),
            __('CibilianCommission')
        )->addBreadcrumb(
            __('Manage CibilianCommission'),
            __('Manage CibilianCommission')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('CibilianCommission'));
        return $resultPage;
    }
}
