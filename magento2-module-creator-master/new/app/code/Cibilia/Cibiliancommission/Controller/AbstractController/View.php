<?php

namespace Cibilia\CibilianCommission\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\CibilianCommission\Controller\AbstractController\CibilianCommissionLoaderInterface
     */
    protected $cibiliancommissionLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CibilianCommissionLoaderInterface $cibiliancommissionLoader, PageFactory $resultPageFactory)
    {
        $this->cibiliancommissionLoader = $cibiliancommissionLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * CibilianCommission view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->cibiliancommissionLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
