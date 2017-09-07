<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\ShipmentFactory;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as DropshipVendorAskQuestionHelperData;
use Unirgy\DropshipVendorAskQuestion\Model\Source;
use Unirgy\Dropship\Helper\Data as HelperData;

class Form extends Generic
{
    /**
     * @var HelperData
     */
    protected $_hlp;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Source
     */
    protected $_qaSrc;

    /**
     * @var DropshipVendorAskQuestionHelperData
     */
    protected $_qaHlp;

    public function __construct(Context $context, 
        Registry $registry, 
        FormFactory $formFactory, 
        HelperData $helperData, 
        CustomerFactory $modelCustomerFactory,
        Source $modelSource, 
        DropshipVendorAskQuestionHelperData $dropshipVendorAskQuestionHelperData, 
        array $data = [])
    {
        $this->_hlp = $helperData;
        $this->_customerFactory = $modelCustomerFactory;
        $this->_qaSrc = $modelSource;
        $this->_qaHlp = $dropshipVendorAskQuestionHelperData;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $question = $this->_coreRegistry->registry('question_data');
        $vendor = $this->_hlp->getVendor($question->getVendorId());
        $shipment = $this->_hlp->createObj('Magento\Sales\Model\Order\Shipment')->load($question->getShipmentId());
        $customer = $this->_customerFactory->create()->load($question->getCustomerId());
        $statuses = $this->_qaSrc
            ->setPath('statuses')
            ->toOptionArray();

        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'action'    => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id'), 'ret' => $this->_coreRegistry->registry('ret')]),
                'method' => 'post'
            ]]
        );

        $fieldset = $form->addFieldset('question_details', ['legend' => __('Question Details'), 'class' => 'fieldset-wide']);

        $fieldset->addField('vendor_name', 'note', [
            'label'     => __('Vendor'),
            'text'      => '<a href="' . $this->getUrl('udropship/vendor/edit', ['id' => $vendor->getId()]) . '" onclick="this.target=\'blank\'">' . $vendor->getVendorName() . '</a>'
        ]);

        if ($question->getShipmentId()) {
            $fieldset->addField('shipment', 'note', [
                'label'     => __('Shipment'),
                'text'      => sprintf('<a onclick="this.target=\'blank\'" href="%sshipment_id/%s/">#%s</a> for order <a onclick="this.target=\'blank\'" href="%sorder_id/%s/">#%s</a>', $this->getUrl('sales/shipment/view'), $question->getShipmentId(), $question->getShipmentIncrementId(), $this->getUrl('sales/order/view'), $question->getOrderId(), $question->getOrderIncrementId())
            ]);
        }

        if ($question->getProductId()) {
            $fieldset->addField('product', 'note', [
                'label'     => __('Product'),
                'text'      => '<a href="' . $this->getUrl('catalog/product/edit', ['id' => $question->getProductId()]) . '" onclick="this.target=\'blank\'"> SKU: ' . $question->getProductSku() . ' NAME: ' . $question->getProductName() . '</a>'
            ]);
        }

        if ($customer->getId()) {
            $customerText = __('<a href="%1" onclick="this.target=\'blank\'">%2 %3</a> <a href="mailto:%4">(%4)</a>',
                $this->getUrl('customer/index/edit', ['id' => $customer->getId(), 'active_tab'=>'udqa']),
                $this->escapeHtml($customer->getFirstname()),
                $this->escapeHtml($customer->getLastname()),
                $this->escapeHtml($customer->getEmail()));
        } else {
            if (is_null($question->getCustomerId())) {
                $customerText = __('Guest');
            } elseif ($question->getCustomerId() == 0) {
                $customerText = __('Administrator');
            }
        }

        $fieldset->addField('customer', 'note', [
            'label'     => __('Posted By'),
            'text'      => $customerText,
        ]);

        $fieldset->addField('customer_name', 'text', [
            'label'     => __('Name'),
            'required'  => true,
            'name'      => 'customer_name'
        ]);

        $visibility = $this->_qaSrc->setPath('visibility')->toOptionArray();
        $fieldset->addField('visibility', 'select', [
            'label'     => __('Visibility'),
            'required'  => true,
            'name'      => 'visibility',
            'values'    => $visibility,
        ]);

        $fieldset->addField('question_status', 'select', [
            'label'     => __('Question Status'),
            'required'  => true,
            'name'      => 'question_status',
            'values'    => $statuses,
        ]);

        $fieldset->addField('question_text', 'textarea', [
            'label'     => __('Question Text'),
            'required'  => true,
            'name'      => 'question_text',
            'style'     => 'height:24em;',
        ]);

        $fieldset->addField('answer_status', 'select', [
            'label'     => __('Answer Status'),
            'required'  => true,
            'name'      => 'answer_status',
            'values'    => $statuses,
        ]);

        $fieldset->addField('answer_text', 'textarea', [
            'label'     => __('Answer Text'),
            'name'      => 'answer_text',
            'style'     => 'height:24em;',
        ]);

        $form->setUseContainer(true);
        $form->setValues($question->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
