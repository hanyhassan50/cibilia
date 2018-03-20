<?php

namespace Cibilia\Dropshipextend\Block;

use \Magento\Store\Model\StoreManagerInterface;

/**
 * Class Siteswitch
 * @package Cibilia\Dropshipextend\Block
 */
class Siteswitch extends \Magento\Framework\View\Element\Template
{
    protected $_locale;

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Siteswitch constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param array $data
     */
    public function __construct(

        \Magento\Framework\View\Element\Template\Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         array $data = []
		)
    {
        $this->_storeManager = $storeManager;
        $this->_locale  =   $scopeConfig;
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
	            $storeObj  = $this->_storeManager->getStore($store);
	            $storeId   = $this->_storeManager->getStore($store)->getId();
	            $storeCode = $this->_storeManager->getStore($store)->getCode();
	            $defaultStore = $this->_storeManager->getDefaultStoreView()->getCode();
	            $locale =   $this->_locale->getValue('general/locale/code',\Magento\Store\Model\ScopeInterface::SCOPE_STORE ,$storeId);
	            $locale =   str_replace('_','-', $locale);
	            $name = $website->getName();
	            $url = $storeObj->getBaseUrl();
	            if($url != "http://foodmakers.cibilia-dev.com/")
	            {
	                if($name == 'productores.cibilia.com' || $name == 'foodmakers.cibilia.com' || $name == 'produttori.cibilia.com' || $name == 'fabricantes.cibilia.com' || $name == 'lebensmittelhersteller.cibilia.com' || $name == 'fabricants.cibilia.com' ) {
	                    continue;
                    } else {
                        array_push($_websiteData, array('name' => $name, 'url' => $url, 'code' => $storeCode,'default_store' => $defaultStore, 'locale' => $locale ));
                    }
	            }
	            else{
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