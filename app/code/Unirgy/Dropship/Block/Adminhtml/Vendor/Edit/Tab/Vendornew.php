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

namespace Unirgy\Dropship\Block\Adminhtml\Vendor\Edit\Tab;

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

        //$vendorsWorks = $arrVendorWork->toOptionArray();
        //$vendorsCats = $arrVendorCategories->toOptionArray();

        
        $fieldset = $form->addFieldset('custom', array(
            'legend'=>__('Vendor New Data')
        ));

        $fieldset->addField('vendor_type', 'select', array(
            'name'      => 'vendor_type',
            'label'     => __('Vendor Type'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => $arrVendorType,
        ));

        $fieldset->addField('vendor_website', 'text', array(
            'name'      => 'vendor_website',
            'label'     => __('Vendor Website'),
        ));

        $fieldset->addField('vendor_fbpage', 'text', array(
            'name'      => 'vendor_fbpage',
            'label'     => __('Facebook Page'),
        ));
        
        $fieldset->addField('vendor_work', 'select', array(
            'name'      => 'vendor_work',
            'label'     => __('Are you referring'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => $arrVendorWork,
        ));

         $fieldset->addField('vendor_categories', 'multiselect', array(
            'name'      => 'vendor_categories',
            'label'     => __('Choose the categories which the company you are referring.'),
            'class'     => 'required-entry',
            'required'  => true,
            'values' => $arrVendorCategories,
        ));
        
        $fieldset->addField('vendor_about', 'textarea', array(
            'name'      => 'vendor_about',
            'label'     => __('What do you know about this company ?'),
            'class'     => 'required-entry',
            'required'  => true,
            'style'     => 'height:50px',
        ));

        $fieldset->addField('vendor_letter', 'textarea', array(
            'name'      => 'vendor_letter',
            'label'     => __('Write a short motivation letter that will be sent along with your application form to the contact person you are about to contact with this form.'),
            'class'     => 'required-entry',
            'required'  => true,
            'style'     => 'height:50px',
        ));

        $fieldset->addField('vendor_represent', 'textarea', array(
            'name'      => 'vendor_represent',
            'label'     => __('How would you present this company publicly ?'),
            'class'     => 'required-entry',
            'required'  => true,
            'style'     => 'height:50px',
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
