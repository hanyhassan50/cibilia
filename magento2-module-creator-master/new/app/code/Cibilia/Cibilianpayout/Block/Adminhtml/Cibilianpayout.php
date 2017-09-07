<?php
/**
 * Adminhtml cibilianpayout list block
 *
 */
namespace Cibilia\Cibilianpayout\Block\Adminhtml;

class Cibilianpayout extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_cibilianpayout';
        $this->_blockGroup = 'Cibilia_Cibilianpayout';
        $this->_headerText = __('Cibilianpayout');
        $this->_addButtonLabel = __('Add New Cibilianpayout');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_Cibilianpayout::save')) {
            $this->buttonList->update('add', 'label', __('Add New Cibilianpayout'));
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
