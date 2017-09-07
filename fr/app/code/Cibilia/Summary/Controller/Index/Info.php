<?php

namespace Cibilia\Summary\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Info extends \Magento\Framework\App\Action\Action
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
        
        
       if ($this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('id');

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            
            $objVendor = $objectManager->create('\Unirgy\Dropship\Model\Vendor')->load($id);
            if($objVendor->getId()){
                if($objVendor->getIsInfoReviewed()){

                    $resultRedirect = $this->resultRedirectFactory->create();
                    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

                    $this->messageManager->addNotice(__('Information had already been reviewd by Vendor.'));

                    return $resultRedirect->setPath($storeManager->getStore()->getBaseUrl());
                }   
            }
        }
        
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		$resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Review Your Information'));
        return $resultPage;
    }
}
