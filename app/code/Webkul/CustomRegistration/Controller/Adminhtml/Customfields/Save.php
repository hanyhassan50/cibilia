<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

use Magento\Backend\App\Action;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Model\Config;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\AttributeMetadataDataProviderFactory;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class Save extends \Magento\Backend\App\Action
{
   /**
    * @var \Magento\Eav\Setup\EavSetupFactory
    */
    protected $_eavSetupFactory;
    /**
     * @var \Webkul\CustomRegistration\Model\VendorAttributeFactory
     */
    protected $_customFieldFactory;
    /**
     * @var AttributeSetFactory
     */
    private $_attributeSetFactory;
    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;
    /**
     * @var \Magento\Customer\Model\AttributeFactory
     */
    protected $_attributeFactory;
    /**
     * @var AttributeMetadataDataProvider
     */
    private $_attributeMetaData;

    private $validationRule = [
        ' ' => 'None',
        'validate-number' => 'validate-number',
        'validate-digits' => 'validate-digits',
        'validate-alpha' => 'validate-alpha',
        'validate-email' => 'validate-email',
        'validate-url'  => 'validate-url',
        'validate-alphanum' => 'validate-alphanum',
        'validate-phoneStrict' => 'validate-phoneStrict',
    ];
    /**
     * @param Action\Context $context
     */
    /**
     *
     * @param Action\Context                                       $context
     * @param EavSetupFactory                                      $eavSetupFactory
     * @param AttributeSetFactory                                  $attributeSetFactory
     * @param \Magento\Customer\Model\AttributeFactory             $attributeFactory
     * @param \Webkul\CustomRegistration\Model\CustomfieldsFactory $customfieldsFactory
     * @param AttributeMetadataDataProviderFactory                 $attributeMetaData
     * @param Config                                               $eavConfig
     */
    public function __construct(
        Action\Context $context,
        EavSetupFactory $eavSetupFactory,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        \Webkul\CustomRegistration\Model\CustomfieldsFactory $customfieldsFactory,
        AttributeMetadataDataProviderFactory $attributeMetaData,
        Config $eavConfig
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_attributeSetFactory = $attributeSetFactory;
        $this->_customFieldFactory = $customfieldsFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_attributeMetaData = $attributeMetaData;
        $this->_eavConfig = $eavConfig;
        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_CustomRegistration::index');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        // echo "<pre>";
        // print_r($data);
        // die;
        $redirectBack = $this->getRequest()->getParam('back', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $customerEntity = $this->_eavConfig->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();
            /** @var $attributeSet AttributeSet */
            $attributeSet = $this->_attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
            $attributeCode = $this->getRequest()->getParam('attribute_code');
            $attributeId = $this->getRequest()->getParam('attribute_id');

            $model = $this->_attributeFactory->create();
            $dependableAttributeCode = '';
            if (isset($data['dependable_attribute_code'])) {
                $dependableAttributeCode = $this->getRequest()->getParam('dependable_attribute_code');
            }

            $customFieldModel = $this->_customFieldFactory->create();
            if (strlen($this->getRequest()->getParam('attribute_code')) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                if (!$validatorAttrCode->isValid($attributeCode)) {
                    $this->messageManager->addError(
                        __(
                            'Attribute name "%1" is invalid. Please use only letters (a-z), '.
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );

                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
            }
            if (strlen($dependableAttributeCode) > 0) {
                $validatorAttrCode = new \Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
                if (!$validatorAttrCode->isValid($dependableAttributeCode)) {
                    $this->messageManager->addError(
                        __(
                            ' Dependable Attribute name "%1" is invalid. Please use only letters (a-z), '.
                            'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                            $attributeCode
                        )
                    );

                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
            }
            if (strlen($this->getRequest()->getParam('attribute_code')) > 0) {
                if ($this->_attributeMetaData->create()->getAttribute('customer', $attributeCode)->getAttributeId()) {
                    $this->messageManager->addError(
                        __('Attribute code "%1" is already exists.', $attributeCode)
                    );

                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
            }
            $id = '';
            if ($attributeId) {
                $model->load($attributeId);
                if (!$model->getAttributeId()) {
                    $this->messageManager->addError(__('This attribute no longer exists.'));

                    return $resultRedirect->setPath('*/*/');
                } else {
                    /*
                 * Updating Custom Customer Attribute
                 */
                    $childAttributeOptions = [];
                    if (array_key_exists('depend_attribute_id', $data) && isset($data['option'])) {
                        $childAttributeOptions['option'] = $data['option'];
                        unset($data['option']);
                    }
                    $data['attribute_code']  = $model->getAttributeCode();
                    $data['is_user_defined'] = $model->getIsUserDefined();
                    $data['frontend_input']  = $model->getFrontendInput();
                    if(array_key_exists('frontend_class',$data)){
                        if($data['frontend_class']){
                           if(!in_array($data['frontend_class'],$this->validationRule)){
                             $data['frontend_class'] = $model->getFrontendClass();
                           }
                        }
                    }
                    else{
                      $data['frontend_class'] = $model->getFrontendClass();
                    }
                    $collection = $customFieldModel->
                            getCollection()->
                            addFieldToFilter('attribute_code', $data['attribute_code']);
                    foreach ($collection as $value) {
                        $id = $value->getEntityId();
                    }
                    $customFieldModel->load($id);

                    $saveAndContinueId = $customFieldModel->getEntityId();
                    /*
                     * required attribute and validation logic
                     */

                    if (isset($data['frontend_class']) && $data['frontend_class'] != '') {
                        if (strpos($model->getFrontendClass(), $data['frontend_class']) === false) {
                            $data['frontend_class'] = $data['frontend_class'].' '.$model->getFrontendClass();
                        } else {
                            $data['frontend_class'] = $data['frontend_class'].' '.
                            $this->updateFrontEndClass($model->getFrontendClass());
                        }
                    } elseif (isset($data['frontend_class']) && $data['frontend_class'] == '') {
                        $data['frontend_class'] = $data['frontend_class'].' '.
                            $this->updateFrontEndClass($model->getFrontendClass());
                    }
                    if (isset($data['is_required'])) {
                        if ($data['is_required'] == 1) {
                            if (strpos($model->getFrontendClass(), 'required') === false) {
                                $data['frontend_class'] = $model->getFrontendClass().' '.'required';
                            }
                            $data['is_required'] = 0;
                        } elseif ($data['is_required'] == 0) {
                            $data['frontend_class'] = str_replace('required', '', isset($data['frontend_class']) ? $data['frontend_class'] : '');
                        }
                    }
                    if (isset($data['allowed_extensions'])) {
                        $data['note'] = rtrim($data['allowed_extensions']);
                    }
                    if (isset($data['is_visible'])) {
                        $customFieldModel->setStatus((int) $data['is_visible']);
                    }
                    try{
                        $parentVisible = $data['is_visible'];
                        $data['is_visible'] = 0;
                        $model->addData($data);
                        $model->save();

                        if (isset($data['frontend_label'])) {
                            $customFieldModel->setAttributeLabel($data['frontend_label']);
                        }
                        $customFieldModel->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        return $resultRedirect->setPath('*/*/');
                    }

                    /*
                     * If Dpendable Attribute is Present
                     * UPDATING DEPENDABLE CHILD ATTRIBUTE
                     */
                    $dependableCheck = $model->getFrontendClass();
                    $isDependArray = explode(' ', $dependableCheck);
                    if (in_array('dependable_field_'.$model->getAttributeCode(), $isDependArray)) {
                        $childData = [];
                        foreach ($data as $key => $value) {
                            if (strpos($key, 'dependable_') !== false) {
                                $keyValue = explode('dependable_', $key);
                                $childData[$keyValue[1]] = $value;
                            }
                        }
                        if (isset($childAttributeOptions['option'])) {
                            $childData['option'] = $childAttributeOptions['option'];
                        }
                        $dependableModel = $this->_attributeFactory->create();
                        $dependAttributeId = $this->getRequest()->getParam('depend_attribute_id');

                        $dependableModel->load($dependAttributeId);

                        $childData['attribute_code'] = $dependableModel->getAttributeCode();
                        $childData['is_user_defined'] = $dependableModel->getIsUserDefined();
                        $childData['frontend_input'] = $dependableModel->getFrontendInput();

                        $collection = $customFieldModel->
                            getCollection()->
                            addFieldToFilter('attribute_code', $childData['attribute_code']);

                        foreach ($collection as $value) {
                            $id = $value->getEntityId();
                        }
                        $customFieldModel->load($id);

                         /*
                         * required attribute and validation logic
                         */
                        $childData['is_visible'] = (int) $parentVisible;

                        if (isset($childData['frontend_class']) && $childData['frontend_class'] != '') {
                            if (strpos($dependableModel->getFrontendClass(), $childData['frontend_class']) === false) {
                                $childData['frontend_class'] = $childData['frontend_class'].' '.
                                    $dependableModel->getFrontendClass();
                            } else {
                                $childData['frontend_class'] = $childData['frontend_class'].' '.$this->updateFrontEndClass($dependableModel->getFrontendClass());
                            }
                        } elseif (isset($childData['frontend_class']) && $childData['frontend_class'] == '') {
                            $childData['frontend_class'] = $childData['frontend_class'].' '.$this->updateFrontEndClass($dependableModel->getFrontendClass());
                        }
                        if (isset($childData['is_required'])) {
                            if ($childData['is_required'] == 1) {
                                if (strpos($dependableModel->getFrontendClass(), 'required') === false) {
                                    $childData['frontend_class'] = $dependableModel->getFrontendClass().' '.'required';
                                }
                                $childData['is_required'] = 0;
                            } elseif ($childData['is_required'] == 0) {
                                if(array_key_exists('frontend_class', $childData))
                                $childData['frontend_class'] = str_replace('required', '', $childData['frontend_class']);
                            }
                        }
                        if (isset($childData['allowed_extensions'])) {
                            $childData['note'] = rtrim($childData['allowed_extensions']);
                        }
                        try{
                            $dependableModel->addData($childData);
                            $dependableModel->save();
                            $customFieldModel->setStatus((int) $childData['is_visible']);
                            $customFieldModel->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());

                            return $resultRedirect->setPath('*/*/');
                        }
                    }
                    /*
                     * END OF DEPENDABLE CHILD ATTRIBUTE UPDATION
                     */
                }
            } else {
                /*
                * Creating Custom Customer Attribute
                */
                try{
                    $attribute = $this->_eavConfig->getAttribute('customer', $attributeCode);
                    $attribute->addData($this->getDefaultEntities(
                        $data['frontend_input'],
                        $attributeSetId,
                        $attributeGroupId,
                        0
                    ));
                    $attribute->save();
                    $attribute = $this->_eavConfig->getAttribute('customer', $attributeCode);

                    $customFieldModel->setAttributeCode($attributeCode);
                    $customFieldModel->setAttributeLabel($data['frontend_label']);
                    $customFieldModel->setAttributeId($attribute->getId());
                    $customFieldModel->setStatus((int) $data['is_visible']);
                    $customFieldModel->save();
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    return $resultRedirect->setPath('*/*/');
                }

                $saveAndContinueId = $customFieldModel->getId();

                /*
                 * Create Dependable child Attribute
                 */
                if ($data['frontend_input'] == 'dependable') {
                    try{
                        $inputType = explode('_', $data['dependable_frontend_input']);
                        $dependableInput = $inputType[1];
                        $attribute = $this->_eavConfig->getAttribute('customer', $dependableAttributeCode);
                        $attribute->addData($this->getDefaultEntities(
                            $dependableInput,
                            $attributeSetId,
                            $attributeGroupId,
                            1
                        ));
                        $attribute->save();
                        $attribute = $this->_eavConfig->getAttribute('customer', $dependableAttributeCode);
                        $customFieldModel = $this->_customFieldFactory->create();
                        $customFieldModel->setAttributeCode($dependableAttributeCode);
                        $customFieldModel->setAttributeLabel($data['dependable_frontend_label']);
                        $customFieldModel->setAttributeId($attribute->getId());
                        $customFieldModel->setHasParent(1);
                        $customFieldModel->setStatus((int) $data['is_visible']);
                        $customFieldModel->save();
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        return $resultRedirect->setPath('*/*/');
                    }
                }
            }
            if ($redirectBack) {
                $this->messageManager->addSuccess(__('You saved the custom attribute.'));
                return $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $saveAndContinueId, '_current' => true]
                );
            }
            $this->_getSession()->setFormData($data);
        }
        $this->messageManager->addSuccess(__('You saved the custom attribute.'));
        return $resultRedirect->setPath('*/*/');
    }
    /**
     * Retrieve default entities: customer custom attribute.
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities($field, $attributeSetId, $attributeGroupId, $isDependable)
    {
        $data = $this->getRequest()->getPostValue();
        $text = ''; //variable will be used if attribute is child.
        $code = ''; // use to define class for child attribute.

        $sortOrder = isset($data['sort_order']) ? $data['sort_order'] : '';
        if ($isDependable) {
            $text = 'dependable_';
            $code = 'child_'.$data['attribute_code'];
            $sortOrder = $sortOrder + 1;
        }

        $validation = '';
        if (isset($data['frontend_class']) && $data['frontend_class'] != '') {
            $validation = $this->getValidationRule($data['frontend_class']);
        }
        if (isset($data['dependable_validation_type']) && $data['dependable_validation_type'] != '') {
            $validation = $this->getValidationRule($data['dependable_validation_type']);
        }

        $required = '';
        if (!isset($data['is_required'])) {
            $data['is_required'] = 0;
        } elseif ($data['is_required'] == 1) {
            $required = 'required';
        }
        if (!isset($data['dependable_is_required'])) {
            $data['dependable_is_required'] = 0;
        } elseif ($data['dependable_is_required'] == 1) {
            $required = 'required';
        }
        $optionValues = '';
        $default = '';
        if (isset($data['option'])) {
            $optionValues = $data['option'];
        }
        $extensions = '';
        if (isset($data['allowed_extensions'])) {
            $extensions = rtrim($data['allowed_extensions']);
        } elseif (isset($data['dependableAllowedExtensions'])) {
            $extensions = trim($data['dependableAllowedExtensions']);
        }
        $entities = [
            'text' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'text',
                'frontend_class' => $validation.' '.$required.' '.$code,
                'sort_order' => $sortOrder,
                'is_visible' => 0,
                'is_system' => false,
                'is_user_defined' => true,
                'position' => $sortOrder,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',

            ],
            'textarea' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'textarea',
                'frontend_class' => $required.' '.$code,
                'sort_order' => $sortOrder,
                'is_visible' => 0,
                'is_system' => false,
                'is_user_defined' => true,
                'position' => $sortOrder,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
            ],
            'date' => [
                'frontend_type' => 'datetime',
                'backend_type' => 'datetime',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'date',
                'frontend_model' => 'Magento\Eav\Model\Entity\Attribute\Frontend\Datetime',
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\Datetime',
                'frontend_class' => $required.' '.$code,
                'sort_order' => $sortOrder,
                'is_visible' => 0,
                'is_system' => false,
                'is_user_defined' => true,
                'input_filter' => 'date',
                'validate_rules' => '{"input_validation":"date"}',
                'position' => $sortOrder,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
            ],
            'select' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'select',
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'frontend_class' => $required.' '.$code,
                'sort_order' => $sortOrder,
                'is_visible' => 0,
                'is_system' => false,
                'is_user_defined' => true,
                'position' => $sortOrder,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
                'option' => $optionValues,
                ],
            'multiselect' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'multiselect',
                'frontend_class' => $required.' '.$code,
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'sort_order' => $sortOrder,
                'is_visible' => 0,
                'is_system' => false,
                'is_user_defined' => true,
                'position' => $sortOrder,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
                'option' => $optionValues,
            ],
            'boolean' => [
                'frontend_type' => 'int',
                'backend_type' => 'int',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'boolean',
                'backend_model' => 'Magento\Customer\Model\Attribute\Backend\Data\Boolean',
                'position' => $sortOrder,
                'frontend_class' => $required.' '.$code,
                'is_visible' => 0,
                'is_user_defined' => true,
                'is_system' => false,
                'is_user_defined' => true,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
            ],
            'image' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'image',
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend',
                'sort_order' => $sortOrder,
                'position' => $sortOrder,
                'frontend_class' => $required.' '.$code,
                'is_visible' => 0,
                'is_user_defined' => true,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'is_system' => false,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
                'note' => $extensions,
            ],
            'file' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'frontend_label' => $data[$text.'frontend_label'],
                'frontend_input' => 'file',
                'backend_model' => 'Magento\Eav\Model\Entity\Attribute\Backend\Increment',
                'sort_order' => $sortOrder,
                'position' => $sortOrder,
                'frontend_class' => $required.' '.$code,
                'is_visible' => 0,
                'is_user_defined' => true,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'is_system' => false,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
                'note' => $extensions,
            ],
            'dependable' => [
                'frontend_type' => 'varchar',
                'backend_type' => 'varchar',
                'frontend_label' => $data['frontend_label'],
                'frontend_input' => 'select',
                'source_model' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                'frontend_class' => 'dependable_field_'.$data['attribute_code'],
                'sort_order' => $sortOrder,
                'is_visible' => 0,
                'is_system' => false,
                'is_user_defined' => true,
                'position' => $sortOrder,
                'attribute_set_id' => $attributeSetId,
                'attribute_group_id' => $attributeGroupId,
                'used_in_forms' => isset($data['used_in_forms']) ? $data['used_in_forms'] : '',
                'option' => ['value' => $this->getOptionData('Yes,No')],
                ],

        ];

        return $entities[$field];
    }
    /**
     * Create Options for select and multiselect type attribute.
     *
     * @param array $data
     *
     * @return array $options
     */
    public function getOptionData($data)
    {
        $data = rtrim($data, ',');
        $optionValue = explode(',', rtrim($data));
        foreach ($optionValue as $key => $value) {
            $value = trim($value);
            $options['field_'.$value][0] = $value;
        }

        return $options;
    }
    /**
     * ger validation class
     * @param  string $key
     * @return string
     */
    public function getValidationRule($key)
    {
        $key = rtrim($key);
        return $this->validationRule[$key];
    }

    public function updateFrontEndClass($frontendClass)
    {
        $classArray = $this->validationRule;
        $attributeClass = explode(' ', $frontendClass);
        if (count($attributeClass)) {
            foreach ($attributeClass as $key => $value) {
                if (in_array($value, $classArray)) {
                    unset($attributeClass[$key]);
                }
            }
        }
        $updatedClass = implode(' ', $attributeClass);
        return $updatedClass;
    }
}
