<?php
/**
 * Adminhtml cibiliancommission list block
 *
 */
namespace Cibilia\CibilianCommission\Block\Adminhtml;

class CibilianCommission extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_cibiliancommission';
        $this->_blockGroup = 'Cibilia_CibilianCommission';
        $this->_headerText = __('CibilianCommission');
        $this->_addButtonLabel = __('Add New CibilianCommission');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_CibilianCommission::save')) {
            $this->buttonList->update('add', 'label', __('Add New CibilianCommission'));
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
