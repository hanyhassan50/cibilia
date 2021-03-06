<?php

namespace Unirgy\DropshipShippingClass\Block\Adminhtml\Customer;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $_registry;

    public function __construct(Context $context,
        Registry $registry,
        array $data = [])
    {
        $this->_registry = $registry;

        $this->_objectId    = 'id';
        $this->_blockGroup  = 'Unirgy_DropshipShippingClass';
        $this->_controller  = 'adminhtml_customer';

        parent::__construct($context, $data);

        $this->buttonList->update('save', 'label', __('Save Customer Ship Class'));
        $this->buttonList->update('delete', 'label', __('Delete Customer Ship Class'));
        $this->setData('form_action_url', $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))));
    }

    public function getHeaderText()
    {
        if ($this->_registry->registry('udshipclass_customer')->getId()) {
            return __("Edit Customer Ship Class '%1'", $this->escapeHtml($this->_registry->registry('udshipclass_customer')->getClassName()));
        }
        else {
            return __('New Customer Ship Class');
        }
    }

}
