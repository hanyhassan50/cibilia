<?php
namespace Cibilia\Idproofs\Block\Adminhtml\Commission\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_idproof_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Idproof Information'));
    }
}