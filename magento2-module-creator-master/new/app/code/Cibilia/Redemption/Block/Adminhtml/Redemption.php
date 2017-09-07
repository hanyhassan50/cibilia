<?php
/**
 * Adminhtml redemption list block
 *
 */
namespace Cibilia\Redemption\Block\Adminhtml;

class Redemption extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_redemption';
        $this->_blockGroup = 'Cibilia_Redemption';
        $this->_headerText = __('Redemption');
        $this->_addButtonLabel = __('Add New Redemption');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_Redemption::save')) {
            $this->buttonList->update('add', 'label', __('Add New Redemption'));
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
