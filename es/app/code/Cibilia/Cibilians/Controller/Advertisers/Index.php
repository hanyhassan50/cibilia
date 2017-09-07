<?php
namespace Cibilia\Cibilians\Controller\Advertisers; 
 
class Index extends \Magento\Framework\App\Action\Action {
    /** @var  \Magento\Framework\View\Result\Page */
    protected $resultPageFactory;
    /**      * @param \Magento\Framework\App\Action\Context $context      */
    public function __construct(\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory)     {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
 
    /**
     * Blog Index, shows a list of recent blog posts.
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		// echo "Here";die;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $custId = $objectManager->create('Magento\Customer\Model\Session')->getCustomer()->getId();
        
        if($custId){
            $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($custId);
            if($objCustomer->getId() && $objCustomer->getApprovalStatus() != '9'){
                
                $resultRedirect = $this->resultRedirectFactory->create();
                
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

                return $resultRedirect->setPath($storeManager->getStore()->getUrl('customregistration/customfields'));
            }
        }
        $resultPage = $this->resultPageFactory->create();
    	$resultPage->getConfig()->getTitle()->prepend(__('Advertisers'));
    	return $resultPage;
    }
}