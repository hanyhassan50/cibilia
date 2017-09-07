<?php

namespace Cibilia\Redemption\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Requested extends \Magento\Framework\App\Action\Action
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
     * Default Redemption Index page
     *
     * @return void
     */
    public function execute()
    {
        /*$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		//$this->_view->getPage()->getConfig()->getTitle()->set(__('Site Redemption'));
        $listBlock = $this->_view->getLayout()->getBlock('redemption.list');

        if ($listBlock) {
            $currentPage = abs(intval($this->getRequest()->getParam('p')));
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            
            $listBlock->setCurrentPage($currentPage);
        }
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Requested Withdrawl list'));
        return $resultPage;*/

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Requested Withdrawl list'));

        return $resultPage;
    }
}