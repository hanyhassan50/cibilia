<?php
    
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_CustomRegistration::customregistration');
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        
        $resultPage->setActiveMenu('Webkul_CustomRegistration::index');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Fields'));
        $resultPage->addBreadcrumb(__('Manage Fields'),__('Manage Fields'));
        return $resultPage;
    }   
}