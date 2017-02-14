<?php
namespace Cibilia\Idproofs\Block;

class Vendorlogin extends \Magento\Framework\View\Element\Html\Link
{
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $isVendorLogin = $objectManager->create('Unirgy\Dropship\Model\Session')->isLoggedIn();
        
        if(!$isVendorLogin)
        {
            return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
        }
        return '';
    }
}