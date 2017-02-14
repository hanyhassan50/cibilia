<?php

namespace Cibilia\Commission\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\Commission\Controller\AbstractController\CommissionLoaderInterface
     */
    protected $commissionLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CommissionLoaderInterface $commissionLoader, PageFactory $resultPageFactory)
    {
        $this->commissionLoader = $commissionLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Commission view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->commissionLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
