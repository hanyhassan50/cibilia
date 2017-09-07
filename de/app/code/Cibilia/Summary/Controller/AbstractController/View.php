<?php

namespace Cibilia\Summary\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\Summary\Controller\AbstractController\SummaryLoaderInterface
     */
    protected $summaryLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, SummaryLoaderInterface $summaryLoader, PageFactory $resultPageFactory)
    {
        $this->summaryLoader = $summaryLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Summary view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->summaryLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
