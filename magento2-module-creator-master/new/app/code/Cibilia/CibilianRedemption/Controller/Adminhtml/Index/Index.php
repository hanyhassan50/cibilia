<?php

namespace Cibilia\CibilianRedemption\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Cibilia_CibilianRedemption::cibilianredemption_manage');
    }

    /**
     * CibilianRedemption List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Cibilia_CibilianRedemption::cibilianredemption_manage'
        )->addBreadcrumb(
            __('CibilianRedemption'),
            __('CibilianRedemption')
        )->addBreadcrumb(
            __('Manage CibilianRedemption'),
            __('Manage CibilianRedemption')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('CibilianRedemption'));
        return $resultPage;
    }
}
