<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;

class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    protected $_blockGroup = 'Unirgy_DropshipVendorAskQuestion';
    public function __construct(Context $context, 
        Registry $frameworkRegistry, 
        QuestionFactory $modelQuestionFactory, 
        array $data = [])
    {
        $this->_coreRegistry = $frameworkRegistry;
        $this->_questionFactory = $modelQuestionFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_question';

        $this->updateButton('save', 'label', __('Save Question'));
        $this->updateButton('save', 'id', 'save_button');
        $this->updateButton('delete', 'label', __('Delete Question'));

        if( $this->getRequest()->getParam('vendorId', false) ) {
            $this->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('udropship/vendor/edit', ['id' => $this->getRequest()->getParam('vendorId', false)]) .'\')' );
        }

        if( $this->getRequest()->getParam('customerId', false) ) {
            $this->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('customer/index/edit', ['id' => $this->getRequest()->getParam('customerId', false)]) .'\')' );
        }

        if( $this->getRequest()->getParam('ret', false) == 'pending' ) {
            $this->updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/pending') .'\')' );
            $this->updateButton('delete', 'onclick', 'deleteConfirm(\'' . __('Are you sure you want to do this?') . '\', \'' . $this->getUrl('*/*/delete', [
                    $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                    'ret'           => 'pending',
                ]) .'\')' );
            $this->_coreRegistry->register('ret', 'pending');
        }

    }

    public function getHeaderText()
    {
        if( $this->_coreRegistry->registry('question_data') && $this->_coreRegistry->registry('question_data')->getId() ) {
            return __("Edit Question");
        } else {
            return __('New Question');
        }
    }
}
