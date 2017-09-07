<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    Unirgy_DropshipTierCommission
 * @copyright  Copyright (c) 2008-2009 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipTierCommission\Block\Adminhtml\VendorEditTab\ComRates;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;
use Unirgy\Dropship\Helper\Data as HelperData;
use Unirgy\DropshipTierCommission\Model\Source;

class Form extends Generic
{
    /**
     * @var HelperData
     */
    protected $_helper;

    /**
     * @var Source
     */
    protected $_source;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        HelperData $helper,
        Source $modelSource,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_source = $modelSource;

        parent::__construct($context, $registry, $formFactory, $data);
        $this->setDestElementId('vendor_tiercom');
    }

    protected function _prepareForm()
    {
        $vendor = $this->_coreRegistry->registry('vendor_data');
        $hlp = $this->_helper;
        $id = $this->getRequest()->getParam('id');
        $form = $this->_formFactory->create();
        $this->setForm($form);

        $fieldset = $form->addFieldset('tiercom', [
            'legend' => __('Rates Definition')
        ]);

        $fieldset->addField('tiercom_fallback_lookup', 'select', [
            'name' => 'tiercom_fallback_lookup',
            'label' => __('Commission fallback lookup method'),
            'options' => $this->_source->setPath('tiercom_fallback_lookup')->toOptionHash(),
        ]);

        $fieldset->addType('tiercom_rates', 'Unirgy\DropshipTierCommission\Block\Adminhtml\VendorEditTab\ComRates\Form\Rates');

        $fieldset->addField('tiercom_rates', 'tiercom_rates', [
            'name' => 'tiercom_rates',
            'label' => __('Rates'),
        ]);

        $fieldset->addField('tiercom_fixed_calc_type', 'select', [
            'name' => 'tiercom_fixed_calc_type',
            'label' => __('Fixed Rates Calculation Type'),
            'options' => $this->_source->setPath('tiercom_fixed_calc_type')->toOptionHash(),
        ]);

        $fieldset->addField('commission_percent', 'text', [
            'name' => 'commission_percent',
            'label' => __('Default Commission Percent'),
            'after_element_html' => __('<br />Default value: %.2F. Leave empty to use default.',
                                       $this->_scopeConfig->getValue('udropship/tiercom/commission_percent',
                                                                                    ScopeInterface::SCOPE_STORE))
        ]);

        $fieldset->addField('transaction_fee', 'text', [
            'name' => 'transaction_fee',
            'label' => __('Fixed Flat Rate (per po) [old transaction fee]'),
            'after_element_html' => __('<br />Default value: %.2F. Leave empty to use default.',
                                       $this->_scopeConfig->getValue('udropship/tiercom/transaction_fee',
                                                                                    ScopeInterface::SCOPE_STORE))
        ]);

        $fieldset->addType('tiercom_fixed_rule', 'Unirgy\Dropship\Block\Adminhtml\Vendor\Helper\Form\DependSelect');

        $fieldset->addField('tiercom_fixed_rule', 'tiercom_fixed_rule', [
            'name' => 'tiercom_fixed_rule',
            'label' => __('Rule for Fixed Rates'),
            'options' => $this->_source->setPath('tiercom_fixed_rates')->toOptionHash(),
            'field_config' => [
                'hide_depend_fields' => [
                    'tiercom_fixed_rates' => '',
                ]
            ]
        ]);

        $fieldset->addType('tiercom_fixed_rates', 'Unirgy\DropshipTierCommission\Block\Adminhtml\VendorEditTab\ComRates\Form\FixedRates');

        $fieldset->addField('tiercom_fixed_rates', 'tiercom_fixed_rates', [
            'name' => 'tiercom_fixed_rates',
            'label' => __('Rule Based Fixed Rates'),
        ]);

        if ($vendor) {
            $form->setValues($vendor->getData());
        }

        return parent::_prepareForm();
    }

}
