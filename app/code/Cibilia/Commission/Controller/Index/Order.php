<?php

namespace Cibilia\Commission\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Order extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
    

    protected $_objectManager;

    protected $coreRegistry = null;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Registry $registry,
         \Magento\Framework\ObjectManagerInterface $objectmanager,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->_objectManager = $objectmanager;
        parent::__construct($context);
    }
	
    /**
     * Default Commission Index page
     *
     * @return void
     */
    public function execute()
    {   
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objOrder = $this->_objectManager->create('Magento\Sales\Model\Order')->load($this->getRequest()->getParam('oid'));
        $this->coreRegistry->register('current_order', $objOrder);
        /*$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		//$this->_view->getPage()->getConfig()->getTitle()->set(__('Site Commission'));
        $listBlock = $this->_view->getLayout()->getBlock('commission.list');

        if ($listBlock) {
            $currentPage = abs(intval($this->getRequest()->getParam('p')));
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            
            $listBlock->setCurrentPage($currentPage);
        }
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Commission Earned list'));
        return $resultPage;*/

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        //$resultPage->getConfig()->getTitle()->set(__('All Orders List'));

        return $resultPage;
    }
}
