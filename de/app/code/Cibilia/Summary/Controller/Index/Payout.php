<?php

namespace Cibilia\Summary\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Payout extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Summary Index page
     *
     * @return void
     */
    public function execute()
    {
        
        $this->_view->loadLayout();
        //$this->_view->getLayout()->initMessages();
		/*$this->_view->getPage()->getConfig()->getTitle()->set(__('Site Summary'));
        $listBlock = $this->_view->getLayout()->getBlock('summary.show');

        if ($listBlock) {
            $currentPage = abs(intval($this->getRequest()->getParam('p')));
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            
            $listBlock->setCurrentPage($currentPage);
        }
        
        */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Default Payout'));
        return $resultPage;
    }
}
