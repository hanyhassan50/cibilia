<?php

namespace Cibilia\Cibilianpayout\Controller\AbstractController;

use Magento\Framework\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class View extends Action\Action
{
    /**
     * @var \Cibilia\Cibilianpayout\Controller\AbstractController\CibilianpayoutLoaderInterface
     */
    protected $cibilianpayoutLoader;
	
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param OrderLoaderInterface $orderLoader
	 * @param PageFactory $resultPageFactory
     */
    public function __construct(Action\Context $context, CibilianpayoutLoaderInterface $cibilianpayoutLoader, PageFactory $resultPageFactory)
    {
        $this->cibilianpayoutLoader = $cibilianpayoutLoader;
		$this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Cibilianpayout view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->cibilianpayoutLoader->load($this->_request, $this->_response)) {
            return;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
		return $resultPage;
    }
}
