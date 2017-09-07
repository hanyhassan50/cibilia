<?php
/**
 * Adminhtml summary list block
 *
 */
namespace Cibilia\Summary\Block\Adminhtml;

class Summary extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_summary';
        $this->_blockGroup = 'Cibilia_Summary';
        $this->_headerText = __('Summary');
        $this->_addButtonLabel = __('Add New Summary');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_Summary::save')) {
            $this->buttonList->update('add', 'label', __('Add New Summary'));
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
