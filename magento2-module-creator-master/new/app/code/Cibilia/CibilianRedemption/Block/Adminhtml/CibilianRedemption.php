<?php
/**
 * Adminhtml cibilianredemption list block
 *
 */
namespace Cibilia\CibilianRedemption\Block\Adminhtml;

class CibilianRedemption extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_cibilianredemption';
        $this->_blockGroup = 'Cibilia_CibilianRedemption';
        $this->_headerText = __('CibilianRedemption');
        $this->_addButtonLabel = __('Add New CibilianRedemption');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_CibilianRedemption::save')) {
            $this->buttonList->update('add', 'label', __('Add New CibilianRedemption'));
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
