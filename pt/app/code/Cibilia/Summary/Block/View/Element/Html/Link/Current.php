<?php
namespace Cibilia\Summary\Block\View\Element\Html\Link;


class Current extends \Magento\Framework\View\Element\Html\Link\Current{

    

    public function toHtml(){
	
		$blockObj = $this->getLayout()->createBlock('Webkul\CustomRegistration\Block\Customfields');
		$customerDataArr = $blockObj->getCurrentCustomer()->toArray();
		if($customerDataArr['approval_status'] == 9){
            return parent::toHtml();
        }	
        return '';
    }  
}