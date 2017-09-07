<?php

namespace Cibilia\Dropshipextend\Block;

use \Magento\Store\Model\StoreManagerInterface;

class Siteswitch 
			extends \Magento\Framework\View\Element\Template
{
    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function __construct(
        
        \Magento\Framework\View\Element\Template\Context $context,
        StoreManagerInterface $storeManager,
         array $data = []
		) 
    {
		  	$this->_storeManager = $storeManager;
  		  	
  		   parent::__construct($context, $data);
	}

	public function getWebsitesData() 
	{
		$currentWebSite = $this->_storeManager->getWebsite()->getId();
	    $_websites = $this->_storeManager->getWebsites();
	    $_websiteData = array();
	    $isVisible = true;
	    foreach($_websites as $website){
	        foreach($website->getStores() as $store){
	            $wedsiteId = $website->getId();
	            $storeObj = $this->_storeManager->getStore($store);
	            $name = $website->getName();
	            $url = $storeObj->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
	            if($url != "http://tst.foodmakers.cibilia.com/")
	            {
	            	 array_push($_websiteData, array('name' => $name,'url' => $url));
	            }else{
					if($currentWebSite == $wedsiteId){
						$isVisible = false;
					}
	            }
	           
	        }
	    }
	    return array($_websiteData,$isVisible);
	}


}

?>