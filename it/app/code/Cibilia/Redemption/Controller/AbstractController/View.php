<?php

namespace Cibilia\Redemption\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\Redemption\Controller\AbstractController\RedemptionLoaderInterface
     */
    protected $redemptionLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, RedemptionLoaderInterface $redemptionLoader, PageFactory $resultPageFactory)
    {
        $this->redemptionLoader = $redemptionLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Redemption view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->redemptionLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
