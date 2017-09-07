<?php
/**
 * Adminhtml cibiliansummary list block
 *
 */
namespace Cibilia\CibilianSummary\Block\Adminhtml;

class CibilianSummary extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_cibiliansummary';
        $this->_blockGroup = 'Cibilia_CibilianSummary';
        $this->_headerText = __('CibilianSummary');
        $this->_addButtonLabel = __('Add New CibilianSummary');
        parent::_construct();
        if ($this->_isAllowedAction('Cibilia_CibilianSummary::save')) {
            $this->buttonList->update('add', 'label', __('Add New CibilianSummary'));
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
