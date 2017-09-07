<?php

namespace Cibilia\Summary\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Vendor extends \Magento\Framework\App\Action\Action
{
	 protected $resultPageFactory;

     protected $customerFactory;
    
	 public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
	public function execute()
    {
        
        echo "hello";
        
        die('diiii');
    }
}
