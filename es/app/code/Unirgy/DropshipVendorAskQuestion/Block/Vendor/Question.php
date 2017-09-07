<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Vendor;

use Magento\Backend\Model\Url;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\Form\AbstractForm;
use Magento\Framework\Date;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\LayoutFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\DropshipVendorAskQuestion\Model\Source;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;

class Question extends Template
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var Source
     */
    protected $_qaSource;

    /**
     * @var HelperData
     */
    protected $_qaHlp;

    /**
     * @var DropshipHelperData
     */
    protected $_hlp;

    /**
     * @var Url
     */
    protected $_backendUrl;

    public function __construct(
        \Magento\Framework\Data\FormFactory $formFactory,
        Context $context,
        QuestionFactory $modelQuestionFactory,
        Source $modelSource,
        HelperData $helperData, 
        DropshipHelperData $dropshipHelperData, 
        Url $modelUrl, 
        array $data = [])
    {
        $this->_questionFactory = $modelQuestionFactory;
        $this->_qaSource = $modelSource;
        $this->_qaHlp = $helperData;
        $this->_hlp = $dropshipHelperData;
        $this->_backendUrl = $modelUrl;
        $this->_formFactory = $formFactory;

        parent::__construct($context, $data);
    }

    protected $_form;
    protected $_question;

    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        Form::setFieldsetRenderer(
            $this->getLayout()->createBlock('Unirgy\DropshipVendorAskQuestion\Block\Vendor\Question\Renderer\Fieldset')
        );
        Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock('Unirgy\DropshipVendorAskQuestion\Block\Vendor\Question\Renderer\FieldsetElement')
        );

        return $this;
    }
    public function getForm()
    {
        if (null === $this->_form) {
            $question = $this->getQuestion();
            $this->_form = $this->_formFactory->create();
            $this->_form->setDataObject($question);
            $values = $question->getData();

            if (($udFormData = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->getUdqaData(true))
                && is_array($udFormData)
            ) {
                $values = array_merge($values, $udFormData);
            }

            $this->_addDetailsFieldset($question, $values);

            $this->_form->addValues($values);

            $this->_form->setFieldNameSuffix('question');
        }
        return $this->_form;
    }
    public function getQuestion()
    {
        if (null === $this->_question) {
            $this->_question = $this->_questionFactory->create()->load(
                $this->_request->getParam('id')
            );
        }
        return $this->_question;
    }
    protected function _addDetailsFieldset($question, &$values)
    {
        $fieldset = $this->_form->addFieldset('details',
            [
                'legend'=>__('Question Details'),
                'class'=>'fieldset-wide',
        ]);
        $this->_addElementTypes($fieldset);

        $data = new DataObject($values);

        $fieldset->addField('question_date', 'note', [
            'name' => 'question_date',
            'label' => __('QUESTION DATE'),
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
            'text' => $this->formatDate($data->getQuestionDate()),
            'is_wide'=>true,
            'is_top'=>true,
            'disabled'=>'disabled'
        ]);

        $fieldset->addField('customer_name', 'note', [
            'name' => 'customer_name',
            'label' => __('CUSTOMER NAME'),
            'text' => $data->getCustomerName(),
            'is_wide'=>true,
            'is_top'=>true,
        ]);

        if ($data->getShipmentId()) {
            $fieldset->addField('shipment_id', 'note', [
                'name' => 'shipment_id',
                'label' => __('SHIPMENT'),
                'text' => '<a href="'.$this->getShipmentUrl($data).'">'.__('#%1 for order #%2', $data->getShipmentIncrementId(), $data->getOrderIncrementId()).'</a>',
                'is_wide'=>true,
                'is_top'=>true,
            ]);
        }

        if ($data->getProductId()) {
            $fieldset->addField('product_id', 'note', [
                'name' => 'product_id',
                'label' => __('PRODUCT'),
                'text' => '<a href="'.$this->getProductUrl($data).'">SKU: '.$data->getProductSku().' '.$data->getProductName().'</a>',
                'is_wide'=>true,
                'is_top'=>true,
            ]);
        }

        /*
        $fieldset->addField('customer_email', 'text', array(
            'name' => 'customer_email',
            'label' => __('Customer email'),
            'disabled'=>'disabled'
        ));
        */

        $fieldset->addField('vendor_id', 'hidden', [
            'name' => 'vendor_id',
            'label' => __('Vendor'),
            'is_wide'=>true,
            'is_hidden'=>true
        ]);

        $fieldset->addField('question_text', 'note', [
            'name' => 'question_text',
            'label' => __('Question Text'),
            'required' => true,
            'is_wide'=>true,
            'is_bottom'=>true,
            'text' => $data->getQuestionText()
        ]);

        $visibility = $this->_qaSource->setPath('visibility')->toOptionArray();
        $fieldset->addField('visibility', 'select', [
            'label'     => __('Visibility'),
            'required'  => true,
            'name'      => 'visibility',
            'values'    => $visibility,
        ]);

        $fieldset->addField('answer_text', 'textarea', [
            'name' => 'answer_text',
            'label' => __('Answer Text'),
            'title' => __('Answer Text'),
            'wysiwyg' => false,
            'required' => true,
            'is_wide'=>true,
            'is_bottom'=>true,
        ]);
        
        $this->_prepareFieldsetColumns($fieldset);
    }

    public function getShipmentUrl($question)
    {
        return $this->_urlBuilder->getUrl('udropship/vendor/', ['_query'=>'filter_order_id_from='.$question->getOrderIncrementId().'&filter_order_id_to='.$question->getOrderIncrementId()]);
    }
    public function getProductUrl($question)
    {
        if ($this->_hlp->isModuleActive('Unirgy_DropshipVendorProduct')) {
            return $this->_urlBuilder->getUrl('udprod/vendor/products', ['_query'=>'filter_sku='.$question->getProductSku()]);
        } elseif ($this->_hlp->isModuleActive('umicrosite')
            && ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->getVendor()->getShowProductsMenuItem()
        ) {
            $params = [];
            $hlp = $this->_backendUrl;
            if ($hlp->useSecretKey()) {
                $params[Url::SECRET_KEY_PARAM_NAME] = $hlp->getSecretKey();
            }
            $params['id'] = $question->getProductId();
            return $hlp->getUrl('adminhtml/catalog_product/edit', $params);
        } else {
            return $this->_urlBuilder->getUrl('udropship/vendor/product', ['_query'=>'filter_sku='.$question->getProductSku()]);
        }
    }

    protected function _prepareFieldsetColumns($fieldset)
    {
        $elements = $fieldset->getElements()->getIterator();
        reset($elements);
        $fullCnt = count($elements);
        $wideElementsBottom = $wideElements = $lcElements = $rcElements = [];
        while($element=current($elements)) {
            if ($element->getIsWide()) {
                if ($element->getIsBottom()) {
                    $wideElementsBottom[] = $element->getId();
                } else {
                    $wideElements[] = $element->getId();
                }
                $fullCnt--;
            }
            next($elements);
        }
        $halfCnt = ceil($fullCnt/2);
        reset($elements);
        $i=0; while ($element=current($elements)) {
            if (!$element->getIsWide()) {
                $lcElements[] = $element->getId();
                $i++;
            }
            next($elements);
            if ($i>=$halfCnt) break;
        }
        while ($element=current($elements)) {
            if (!$element->getIsWide()) {
                $rcElements[] = $element->getId();
            }
            next($elements);
        }
        $fieldset->setWideColumnTop($wideElements);
        $fieldset->setWideColumnBottom($wideElementsBottom);
        $fieldset->setLeftColumn($lcElements);
        $fieldset->setRightColumn($rcElements);
        reset($elements);
        return $this;
    }
    protected $_additionalElementTypes = null;
    protected function _initAdditionalElementTypes()
    {
        if (is_null($this->_additionalElementTypes)) {
            $result = [];

            $response = new DataObject();
            $response->setTypes([]);
            $this->_eventManager->dispatch('udqa_question_edit_element_types', ['response'=>$response]);

            foreach ($response->getTypes() as $typeName=>$typeClass) {
                $result[$typeName] = $typeClass;
            }
            $this->_additionalElementTypes = $result;
        }
        return $this;
    }

    protected function _getAdditionalElementTypes()
    {
        $this->_initAdditionalElementTypes();
        return $this->_additionalElementTypes;
    }
    public function addAdditionalElementType($code, $class)
    {
        $this->_initAdditionalElementTypes();
        $this->_additionalElementTypes[$code] = $class;
        return $this;
    }

    protected function _addElementTypes(AbstractForm $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }
}