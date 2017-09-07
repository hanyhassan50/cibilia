<?php

namespace Cibilia\CibilianRedemption\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\CibilianRedemption\Controller\AbstractController\CibilianRedemptionLoaderInterface
     */
    protected $cibilianredemptionLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CibilianRedemptionLoaderInterface $cibilianredemptionLoader, PageFactory $resultPageFactory)
    {
        $this->cibilianredemptionLoader = $cibilianredemptionLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * CibilianRedemption view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->cibilianredemptionLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
