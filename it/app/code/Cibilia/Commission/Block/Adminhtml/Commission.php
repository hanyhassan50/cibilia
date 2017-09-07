<?php
/**
 * Adminhtml commission list block
 *
 */
namespace Cibilia\Commission\Block\Adminhtml;

class Commission extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_commission';
        $this->_blockGroup = 'Cibilia_Commission';
        $this->_headerText = __('Cibilian Commission');
        $this->_addButtonLabel = __('Add New Commission');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_Commission::save')) {
            $this->buttonList->update('add', 'label', __('Add New Commission'));
        } else {
            $this->buttonList->remove('add');
        }
        $this->removeButton('add');
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
