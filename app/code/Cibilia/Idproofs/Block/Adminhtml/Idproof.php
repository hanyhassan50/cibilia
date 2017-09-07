<?php
namespace Cibilia\Idproofs\Block\Adminhtml;
class Idproof extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_idproof';/*block grid.php directory*/
        $this->_blockGroup = 'Cibilia_Idproofs';
        $this->_headerText = __('Manage Identity Proof');
        $this->_addButtonLabel = __('Add New Entry'); 
        parent::_construct();
        $this->removeButton('add');
    }
}
