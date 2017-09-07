<?php
namespace Cibilia\Idproofs\Controller\Adminhtml\Idproof;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;


class Commission extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $resultPage;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
		
		$this->resultPage = $this->resultPageFactory->create();  
		$this->resultPage->setActiveMenu('Cibilia_Idproof::idproof');
		$this->resultPage ->getConfig()->getTitle()->set((__('Manage Cibilian Commission')));

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $arrOrdersCollection = $objectManager->create('Cibilia\Idproofs\Model\Idproof')->genrateOrders();
        die;
		return $this->resultPage;

    }
}
