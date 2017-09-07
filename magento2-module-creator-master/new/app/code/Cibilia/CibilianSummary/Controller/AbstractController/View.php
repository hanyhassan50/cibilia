<?php

namespace Cibilia\CibilianSummary\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\CibilianSummary\Controller\AbstractController\CibilianSummaryLoaderInterface
     */
    protected $cibiliansummaryLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CibilianSummaryLoaderInterface $cibiliansummaryLoader, PageFactory $resultPageFactory)
    {
        $this->cibiliansummaryLoader = $cibiliansummaryLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * CibilianSummary view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->cibiliansummaryLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
