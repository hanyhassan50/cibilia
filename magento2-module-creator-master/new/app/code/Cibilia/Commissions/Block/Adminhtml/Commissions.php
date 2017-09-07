<?php
/**
 * Adminhtml commissions list block
 *
 */
namespace Cibilia\Commissions\Block\Adminhtml;

class Commissions extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_commissions';
        $this->_blockGroup = 'Cibilia_Commissions';
        $this->_headerText = __('Commissions');
        $this->_addButtonLabel = __('Add New Commissions');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_Commissions::save')) {
            $this->buttonList->update('add', 'label', __('Add New Commissions'));
        } else {
            $this->buttonList->remove('add');
        }
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
