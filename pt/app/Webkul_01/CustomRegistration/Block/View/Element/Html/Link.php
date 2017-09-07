<?php

namespace Webkul\CustomRegistration\Block\View\Element\Html\Link;

use Magento\Customer\Model\Session as CustomerSession;
use Webkul\CustomRegistration\Block as Block;

class Current extents \Magento\Framework\View\Element\Html\Link\Current{

    protected $_customerSession;
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        CustomerSession $CustomerSession
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_defaultPath = $defaultPath;
        $this->_customerSession = $CustomerSession;
    }


    public function toHtml(){
		$customerData = $Block->attributeCollectionFilter();
		echo "<pre>";print_r($customerData);die;
          // if($this->_customerSession->getCustomer()->getYourAttribute()){
               // return parent::toHtml();
          // }
          return '';
    }  
}