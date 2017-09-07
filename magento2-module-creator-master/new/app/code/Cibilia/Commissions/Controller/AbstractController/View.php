<?php

namespace Cibilia\Commissions\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\Commissions\Controller\AbstractController\CommissionsLoaderInterface
     */
    protected $commissionsLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CommissionsLoaderInterface $commissionsLoader, PageFactory $resultPageFactory)
    {
        $this->commissionsLoader = $commissionsLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Commissions view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->commissionsLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
