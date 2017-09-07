<?php
namespace Webkul\CustomRegistration\Block\View\Element\Html\Link;

use Magento\Customer\Model\Session as CustomerSession;
// use Webkul\CustomRegistration\Block\ as Block;

class Current extends \Magento\Framework\View\Element\Html\Link\Current{

    

    public function toHtml(){
	
	$blockObj = $this->getLayout()->createBlock('Webkul\CustomRegistration\Block\Customfields');
		// $customerData = $Block->attributeCollectionFilter();
		
          // if($this->_customerSession->getCustomer()->getYourAttribute()){
		  // $customerData = $blockObj->attributeCollectionFilter();
		  $customerDataArr = $blockObj->getCurrentCustomer()->toArray();
		// echo "<pre>Status:";print_r($customerDataArr);echo "</pre>";
		  // echo parent::toHtml();die;
		  if($customerDataArr['approval_status'] == 9){	// show Advertisers menu if customer is approved (9 ~ 214)
               return parent::toHtml();
          }	
          return '';
    }  
}