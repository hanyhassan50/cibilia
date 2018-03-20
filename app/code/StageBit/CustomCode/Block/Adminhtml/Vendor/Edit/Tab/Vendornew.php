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
 * @package    \Unirgy\Dropship
 * @copyright  Copyright (c) 2015-2016 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace StageBit\CustomCode\Block\Adminhtml\Vendor\Edit\Tab;

use \Magento\Framework\Registry;
use \Unirgy\Dropship\Helper\Data as HelperData;

class Vendornew extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var HelperData
     */
    protected $_hlp;

    public function __construct(
        Registry $registry,
        HelperData $helperData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    )
    {
        $this->_registry = $registry;
        $this->_hlp = $helperData;

        parent::__construct($context, $registry, $formFactory, $data);
        $this->setDestElementId('vendor_newdata');
        //$this->setTemplate('udropship/vendor/form.phtml');
    }

    protected function _prepareForm()
    {
        $vendor = $this->_registry->registry('vendor_data');
        $hlp = $this->_hlp;
        $id = $this->getRequest()->getParam('id');
        $form = $this->_formFactory->create();
        $this->setForm($form);
        $arrVendorWork = $this->_hlp->createObj('\Cibilia\Idproofs\Model\Idproof')
            ->getVendorwork();

        $arrVendorCategories = $this->_hlp->createObj('\Cibilia\Idproofs\Model\Idproof')
            ->getVendorcat();

        $arrVendorType = $this->_hlp->createObj('\Cibilia\Idproofs\Model\Idproof')
            ->getVendortype();

        $category =  $this->_hlp->createObj('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory')->create();
        $category->addAttributeToFilter('level', 2);
        $category_data = $category->getData();


        $options_array = array();
        $options_array[] = array("value"=>'',"label"=>'Pick relevant categories:');
        foreach($category_data as $cat_data){
            $cat = $this->_hlp->createObj('\Magento\Catalog\Model\CategoryFactory')->create()->load($cat_data["entity_id"])->getData();
            $options_array[] = array("value"=>$cat["name"],"label"=>$cat["name"]);
        }
        $options = $options_array;



        $fieldset = $form->addFieldset('custom', array(
            'legend'=>__('Vendor New Data')
        ));

        $fieldset->addField('vendor_name', 'text', array(
            'name'      => 'vendor_name',
            'label'     => __('Company Name'),
            'class'     => 'required-entry',
            'required'  => true
        ));
        $fieldset->addField('owner_name', 'text', array(
            'name'      => 'owner_name',
            'label'     => __('Name and Surname of Owner/Responsible'),
            'class'     => 'required-entry',
            'required'  => true
        ));
        $fieldset->addField('your_role', 'select', array(
            'name'      => 'your_role',
            'label'     => __('Your role'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => array(
                '' => 'Please select',
                'owner'  => 'Owner',
                'vp_director' => 'VP-Director',
                'sales'  => 'Sales',
                'admin'  => 'Admin'
            )
        ));
        $fieldset->addField('company_address', 'text', array(
            'name'      => 'company_address',
            'label'     => __('Company Registered Address'),
            'class'     => 'required-entry',
            'required'  => true
        ));

        $fieldset->addField('email', 'text', array(
            'name'      => 'email',
            'label'     => __('Email Address'),
            'class'     => 'required-entry',
            'required'  => true
        ));

        $fieldset->addField('company_employee', 'select', array(
            'name'      => 'company_employee',
            'label'     => __('Question 1: How many people work in the company you wish to sign up?'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => array(
                '' => 'Please select',
                '1'  => '1',
                '2-5' => '2 - 5',
                '6-10'  => '6 - 10',
                '11-20'  => '11 - 20',
                '21-35'  => '21 - 35',
                '36-50' => '36 - 50',
                'more_than_50' => 'More than 50'
            )
        ));

        $fieldset->addField('company_type', 'select', array(
            'name'      => 'company_type',
            'label'     => __('Question 2: Is your company part of a group of companies?'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => array(
                '' => 'Please select',
                'yes'  => 'Yes',
                'no' => 'No'
            )
        ));

        $fieldset->addField('product_categories', 'multiselect', array(
            'name'      => 'product_categories',
            'label'     => __('In which categories your products fit in?'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => $options,
        ));

        $fieldset->addField('product_sell_place', 'multiselect', array(
            'name'      => 'product_sell_place',
            'label'     => __('Where do you currently sell your products?'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => array(
                array("value"=>'',"label" => 'Please select'),
                array("value"=>'Independent retailers',"label"  => 'Independent retailers'),
                array("value"=>'Farmers markets / festivals','label' => 'Farmers markets / festivals'),
                array("value"=>'Supermarkets','label' => 'Supermarkets'),
                array("value"=>'Your own website','label' => 'Your own website'),
                array("value"=>'Other','label' =>'Other')
            )
        ));

        $fieldset->addField('product_sell_place_other', 'text', array(
            'name'      => 'product_sell_place_other',
            'label'     => __('Other'),
        ));

        $fieldset->addField('best_time_to_call', 'select', array(
            'name'      => 'best_time_to_call',
            'label'     => __('When is the best time to call you'),
            'class'     => 'required-entry',
            'required'  => false,
            'values' => array(
                '' => 'Please select',
                'Morning'  => 'Morning',
                'Afternoon ' => 'Afternoon',
                'Evening' => 'Evening'
            )
        ));

        $fieldset->addField('vat_number', 'text', array(
            'name'      => 'vat_number',
            'label'     => __('VAT number'),
            'class'     => 'required-entry',
            'required'  => true
        ));

        $fieldset->addField('foundation_year', 'text', array(
            'name'      => 'foundation_year',
            'label'     => __('Year of foundation'),
            'class'     => 'required-entry',
            'required'  => true
        ));

        $fieldset->addField('company_fb_page', 'text', array(
            'name'      => 'company_fb_page',
            'label'     => __('Company Facebook page'),
        ));

        $fieldset->addField('company_website', 'text', array(
            'name'      => 'company_website',
            'label'     => __('Company Website'),
        ));

        $fieldset->addField('company_linkedin', 'text', array(
            'name'      => 'company_linkedin',
            'label'     => __('Company LinkedIn page'),
        ));

        $fieldset->addField('company_twitter', 'text', array(
            'name'      => 'company_twitter',
            'label'     => __('Company twitter'),
        ));

        $fieldset->addField('url_key', 'text', array(
            'name'      => 'url_key',
            'label'     => __('Preferred domain name'),
        ));

        $fieldset->addField('logo', 'image', array(
            'name'      => 'logo',
            'label'     => __('Logo Image'),
        ));

        $fieldset->addField('company_photos', 'image', array(
            'name'      => 'company_photos',
            'label'     => __('Company photos'),
        ));

        if ($vendor) {
            $form->setValues($vendor->getData());
        }

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Vendor New Data');
    }
    public function getTabTitle()
    {
        return __('Vendor New Data');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }

}
