<?php

namespace Cibilia\Summary\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Test extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Summary Index page
     *
     * @return void
     */
    public function execute()
    {
			die('iniiiiiii');
    }
}
