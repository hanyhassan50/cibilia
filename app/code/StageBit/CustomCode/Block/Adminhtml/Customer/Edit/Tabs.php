<?php

namespace StageBit\CustomCode\Block\Adminhtml\Customer\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Backend\Block\Widget\Form;
use Magento\Customer\Model\CustomerFactory;
use Magento\Backend\Block\Widget\Form\Generic;

/**
 * Customer account form block.
 */
class Tabs extends Generic implements TabInterface
{
    /**
     * @var string
     */
    /*protected $_template = 'customfields/customer/button.phtml';*/

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_dob = null;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $_attributeCollection;

    protected $_eavEntity;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attributeCollection,
        \Magento\Eav\Model\Entity $eavEntity,
        CustomerFactory $customer,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_systemStore = $systemStore;
        $this->_attributeCollection = $attributeCollection;
        $this->_customer = $customer;
        $this->_eavEntity = $eavEntity;
        $this->_objectManager = $objectManager;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Identity Proof');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Identity Proof');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }

        return true;
    }

    /**
     * Tab class getter.
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content.
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call.
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
    public function _prepareForm()
    {
        if (!$this->canShowTab()) {
            return $this;
        }
        /**@var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('customfields_');
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Additional Information')]);

        $_customerData = $this->getCurrentCustomer()->toArray();

        foreach ($this->getCustomAttribute() as $record) {
            $_fieldValue = '';
            $optiondata = [];
            $usedInForms = $this->getUsedInForms($record->getId());
            $required =  false;
            $isRequiredArray = explode(' ', $record->getFrontendClass());
            if (in_array('required', $isRequiredArray)) {
                $required =  true;
            }
            $isShowOnEditPage = in_array('adminhtml_customer', $usedInForms);
            if (!empty($_customerData)) {
                foreach ($_customerData as $key => $value) {
                    if ($record->getAttributeCode() == $key) {
                        if ($record->getFrontendInput() == 'image') {
                            $_fieldValue = $value;
                        } elseif ($record->getFrontendInput() == 'date') {
                            $_fieldValue = $this->formatDate($value, \IntlDateFormatter::SHORT, false);
                        } elseif ($record->getFrontendInput() == 'boolean') {
                            $_fieldValue = $value;
                        } elseif ($record->getFrontendInput() == 'multiselect' || $record->getFrontendInput() == 'select') {
                            $_fieldValue = $value;
                            $optiondata = $record->getSource()->getAllOptions();
                            usort(
                                $optiondata,
                                function ($a, $b) {
                                    return $a['value'] <=> $b['value'];
                                }
                            );
                        } else {
                            $_fieldValue = $value;
                        }
                    }
                }
            }

            if ($record->getFrontendInput() == 'multiselect' || $record->getFrontendInput() == 'select') {
                $optiondata = $record->getSource()->getAllOptions();
                usort($optiondata, function ($a, $b) {
                    return $a['value'] <=> $b['value'];
                });
            }
            if ($isShowOnEditPage) {
                if ($record->getFrontendInput() == 'image') {
                    if($_fieldValue)
                        $imageUrl ='customfield/image'.$_fieldValue;
                    else
                        $imageUrl='';
                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'image',
                        [
                            'name' => $record->getAttributeCode(),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'required' => $required,
                            'value' => $imageUrl,
                            'note' => __('Allowed image types:').' '.$record->getNote()
                        ]
                    );
                    $fieldset->addField(
                        'saved_'.$record->getAttributeCode(),
                        'hidden',
                        [
                            'name' => 'customer['.$record->getAttributeCode().']',
                            'data-form-part' => $this->getData('target_form'),
                            'value' => $_fieldValue,
                        ]
                    );
                } elseif ($record->getFrontendInput() == 'text') {
                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'text',
                        [
                            'name' => 'customer['.$record->getAttributeCode().']',
                            'data-form-part' => $this->getData('target_form'),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'required' => $required,
                            'value' => $_fieldValue,
                        ]
                    );
                } elseif ($record->getFrontendInput() == "date") {
                    $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
                    $fieldset->addField(
                            $record->getAttributeCode(),
                        'date',
                        [
                            'name' => 'customer['.$record->getAttributeCode().']',
                            'data-form-part' => $this->getData('target_form'),
                                'class'     => 'custom_date_field',
                                'required' => $required,
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'value' => $_fieldValue,
                            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                            'date_format' => $dateFormat,
                        ]
                    );
                } elseif ($record->getFrontendInput() == 'multiselect') {
                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'multiselect',
                        [
                            'name' => $record->getAttributeCode(),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'required' => $required,
                            'values' => $optiondata,
                            'value' => $_fieldValue,
                        ]
                    );
                } elseif ($record->getFrontendInput() == 'boolean') {
                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'checkbox',
                        [
                            'name' => 'customer['.$record->getAttributeCode().']',
                            'data-form-part' => $this->getData('target_form'),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'onclick' => "",
                            'onchange' => "this.value = this.checked?1:0",
                            'value' => $_fieldValue,
                            'checked' => $_fieldValue?true:false
                        ]
                    );
                } elseif ($record->getFrontendInput() == 'select') {
                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'select',
                        [
                            'name' => 'customer['.$record->getAttributeCode().']',
                            'data-form-part' => $this->getData('target_form'),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'required' => $required,
                            'values' => $optiondata,
                            'value' => $_fieldValue,
                        ]
                    );
                } elseif ($record->getFrontendInput() == 'file') {
                    $url = $this->storeManager
                            ->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'customfield/file'.$_fieldValue;

                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'file',
                        [
                            'name' => $record->getAttributeCode(),
                            'data-form-part' => $this->getData('target_form'),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'required' => $_fieldValue == '' ? $required : false,
                            'note' => __('Allowed file types:').' '.$record->getNote(),
                            'after_element_html' => $_fieldValue != '' ?
                                '<a target="_blank" href="'.$url.'">'.__('Download').'</a>':'',
                        ]
                    );
                } else {
                    $fieldset->addField(
                        $record->getAttributeCode(),
                        'textarea',
                        [
                            'name' => 'customer['.$record->getAttributeCode().']',
                            'data-form-part' => $this->getData('target_form'),
                            'label' => __($record->getFrontendLabel()),
                            'title' => __($record->getFrontendLabel()),
                            'required' => $required,
                            'value' => $_fieldValue,
                        ]
                    );
                }
            }
        }
        $this->setForm($form);
        $form->setUseContainer(true);
        return parent::_prepareForm();
    }
    /**
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->canShowTab()) {
            $this->_prepareForm();

            return parent::_toHtml();
        } else {
            return '';
        }
    }
    /**
     * Prepare the layout.
     *
     * @return $this
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            'Webkul\CustomRegistration\Block\Adminhtml\Customer\Edit\Button'
        )->toHtml();
        return $html;
    }
    public function getCustomAttribute()
    {
        $typeId = $this->_eavEntity->setType('customer')->getTypeId();
        $customField = $this->_objectManager->create(
            'Webkul\CustomRegistration\Model\ResourceModel\Customfields\Collection'
        )->getTable('wk_customfields');

        $collection = $this->_attributeCollection->create()
            ->setEntityTypeFilter($typeId)
            ->addFilter('is_user_defined', 1)
            ->setOrder('sort_order', 'ASC');
        $collection->getSelect()
            ->join(
                ["ccp" => $customField],
                "ccp.attribute_id = main_table.attribute_id",
                ["status" => "status"]
            )->where("ccp.status = 1");
        return $collection;
    }
    public function getCurrentCustomer()
    {
        $customerId = $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        $customerData = $this->_customer->create()->load($customerId);

        return $customerData;
    }
    /**
     * [getUsedInForms description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getUsedInForms($id)
    {
        $attributeModel = $this->_objectManager->create('Magento\Customer\Model\Attribute');
        return $attributeModel->load($id)->getUsedInForms();
    }
}
