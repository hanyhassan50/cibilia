<?php

namespace Webkul\CustomRegistration\Block\Adminhtml\Attribute\Edit;

abstract class AbstractMain extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Config\Model\Config\Source\YesnoFactory
     */
    protected $_yesnoFactory;

    /**
     * @var \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory
     */
    protected $_inputTypeFactory;
    /**
     * Attribute instance
     *
     * @var Attribute
     */
    protected $_attribute = null;

    /**
     * @var \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker
     */
    protected $propertyLocker;

    /**
     * Eav data
     *
     * @var \Magento\Eav\Helper\Data
     */
    protected $_eavData = null;

    /**
     * @param \Magento\Eav\Helper\Data $eavData
     * @param \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory
     * @param \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory
     * @param \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Helper\Data $eavData,
        \Magento\Config\Model\Config\Source\YesnoFactory $yesnoFactory,
        \Magento\Eav\Model\Adminhtml\System\Config\Source\InputtypeFactory $inputTypeFactory,
        \Magento\Eav\Block\Adminhtml\Attribute\PropertyLocker $propertyLocker,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->propertyLocker = $propertyLocker;
        $this->_eavData = $eavData;
        $this->_yesnoFactory = $yesnoFactory;
        $this->_inputTypeFactory = $inputTypeFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Return attribute object
     *
     * @return Attribute
     */
    public function getattributeObject()
    {
        if (null === $this->_attribute) {
            return $this->_coreRegistry->registry('entity_attribute');
        }
        return $this->_attribute;
    }

    /**
     * Set attribute object
     *
     * @param Attribute $attribute
     * @return $this
     * @codeCoverageIgnore
     */
    public function setattributeObject($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Preparing default form elements for editing attribute
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $attributeObj = $this->getattributeObject();
        $usedInForms = $attributeObj->getUsedInForms();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setHtmlIdPrefix('customfields_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Attribute Properties'), 'collapsable' => true]);

        if ($attributeObj->getAttributeId()) {
            $fieldset->addField('attribute_id', 'hidden', ['name' => 'attribute_id']);
        }

        $this->_addElementTypes($fieldset);

        $yesno = $this->_yesnoFactory->create()->toOptionArray();

        $label = $attributeObj->getFrontendLabel();
        $fieldset->addField(
            'attribute_label',
            'text',
            [
                'name' => 'frontend_label',
                'label' => __('Default label'),
                'title' => __('Default label'),
                'required' => true,
                'value' => is_array($label) ? $label[0] : $label
            ]
        );

        $validationClass = sprintf(
            'validate-code validate-length maximum-length-%d',
            \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
        );
        $fieldset->addField(
            'attribute_code',
            'text',
            [
                'name' => 'attribute_code',
                'label' => __('Attribute Code'),
                'title' => __('Attribute Code'),
                'note' => __(
                    'Make sure you don\'t use spaces or more than %1 symbols.',
                    \Magento\Eav\Model\Entity\Attribute::ATTRIBUTE_CODE_MAX_LENGTH
                ),
                'class' => $validationClass,
                'required' => true
            ]
        );

        $fieldset->addField(
            'frontend_input',
            'select',
            [
                'name' => 'frontend_input',
                'label' => __('Input Type for Store Owner'),
                'title' => __('Input Type for Store Owner'),
                'value' => 'text',
                'values' => $this->_inputTypeFactory->create()->toOptionArray()
            ]
        );

        $fieldset->addField(
            'is_required',
            'select',
            [
                'name' => 'is_required',
                'label' => __('Values Required'),
                'title' => __('Values Required'),
                'values' => $yesno
            ]
        );

        $fieldset->addField(
            'frontend_class',
            'select',
            [
                'name' => 'frontend_class',
                'label' => __('Input Validation for Store Owner'),
                'title' => __('Input Validation for Store Owner'),
                'values' => $this->_eavData->getFrontendClasses($attributeObj->getEntityType()->getEntityTypeCode())
            ]
        );
        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Input Field Sort Order'),
                'title' => __('Input Field Sort Order'),
                'required' => true
            ]
        );
        $attributeObj->setData('used_in_forms', $usedInForms);
        $fieldset->addField(
            'used_in_forms',
            'multiselect',
            [
                'name' => 'used_in_forms[]',
                'label' => __('Display Fields in Form'),
                'title' => __('Display Fields in Form'),
                'values' => [
                    ['label' => __('Admin Customer Form'), 'value' => 'adminhtml_customer'],
                    ['label' => __('Customer Account'), 'value' => 'customer_account_edit'],
                    ['label' => __('Registration Form'), 'value' => 'customer_account_create'],
                ],
                'required' => true
            ]
        );
        $fieldset->addField(
            'is_visible',
            'select',
            [
                'name' => 'is_visible',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $yesno
            ]
        );

        /*if ($attributeObj->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);
            if (!$attributeObj->getIsUserDefined()) {
                $form->getElement('is_unique')->setDisabled(1);
            }
        }*/

        $this->propertyLocker->lock($form);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Initialize form fileds values
     *
     * @return $this
     */
    protected function _initFormValues()
    {
        $this->_eventManager->dispatch(
            'adminhtml_block_eav_attribute_edit_form_init',
            ['form' => $this->getForm()]
        );
        $this->getForm()->addValues($this->getattributeObject()->getData());
        return parent::_initFormValues();
    }

    /**
     * Processing block html after rendering
     * Adding js block to the end of this block
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $jsScripts = $this->getLayout()->createBlock('Magento\Eav\Block\Adminhtml\Attribute\Edit\Js')->toHtml();
        return $html . $jsScripts;
    }
}
