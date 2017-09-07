<?php

namespace Cibilia\Cibilianpayout\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cibilia_Cibilianpayout::cibilianpayout_manage');
    }

    /**
     * Cibilianpayout List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cibilia_Cibilianpayout::cibilianpayout_manage'
        )->addBreadcrumb(
            __('Cibilianpayout'),
            __('Cibilianpayout')
        )->addBreadcrumb(
            __('Manage Cibilianpayout'),
            __('Manage Cibilianpayout')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cibilianpayout'));
        return $resultPage;
    }
}
