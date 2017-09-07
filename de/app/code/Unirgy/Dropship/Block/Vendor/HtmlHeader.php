<?php

namespace Unirgy\Dropship\Block\Vendor;

use \Magento\Framework\View\Element\Template;

class HtmlHeader extends Template
{
	protected $_request;
	
	public function __construct(
			\Magento\Backend\Block\Template\Context $context,
     		\Magento\Framework\App\Request\Http $request,
     		array $data = []
		){
			$this->_request = $request;
     		parent::__construct($context, $data);
	}

	public function getModuleName(){
		return $this->_request->getModuleName();
	}

	public function getControllerName(){
		return $this->_request->getControllerName();
	}	

	public function getActionName(){
		return $this->_request->getActionName();
	}

	public function getRouteName(){
		return $this->_request->getRouteName();
	}
	
}