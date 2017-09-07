<?php
namespace Cibilia\Idproofs\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        var_dump($this->getLinkAttributes());
        die;
        return '<li><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}