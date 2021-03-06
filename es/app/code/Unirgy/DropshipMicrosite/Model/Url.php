<?php

namespace Unirgy\DropshipMicrosite\Model;

use Magento\Store\Model\StoreManagerInterface;

class Url extends \Magento\Framework\Url
{
    public function getUrl($routePath = null, $routeParams = null)
    {
        $forceVendorUrl = !empty($routeParams['_current']);
        $storeId = isset($routeParams['_scope']) ? $routeParams['_scope'] : null;
        if ($forceVendorUrl) {
            $origScope = $this->_getScope();
            if ($storeId) $this->setScope($storeId);
            $this->_getScope()->useVendorUrl(true);
            $this->setScope($origScope);
        }
        $url = parent::getUrl($routePath, $routeParams);
        if ($forceVendorUrl) {
            $origScope = $this->_getScope();
            if ($storeId) $this->setScope($storeId);
            $this->_getScope()->resetUseVendorUrl();
            $this->setScope($origScope);
        }
        return $url;
    }
}