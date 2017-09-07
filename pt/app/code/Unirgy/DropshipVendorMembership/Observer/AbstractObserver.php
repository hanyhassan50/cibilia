<?php

namespace Unirgy\DropshipVendorMembership\Observer;

use Magento\Framework\Data\Form\AbstractForm;
use Magento\Framework\Filter\Sprintf;
use Magento\Framework\View\Layout;

abstract class AbstractObserver
{
    /**
     * @var Layout
     */
    protected $_viewLayout;

    public function __construct(Layout $viewLayout)
    {
        $this->_viewLayout = $viewLayout;

    }

    protected function _addMembershipOptions($form)
    {
        $coFieldset = $form->addFieldset('membership_options',
            [
                'legend'=>__('Membership Options'),
                'class'=>'fieldset-wide',
            ]);
        $this->addAdditionalElementType(
            'membership_options',
            '\Unirgy\DropshipVendorMembership\Block\Vendor\MembershipOptions'
        );
        $this->_addElementTypes($coFieldset);

        $coEl = $coFieldset->addField('_membership_options', 'membership_options', [
            'name'      => 'options',
            'label'     => __('Membership Options'),
            'value_filter' => new Sprintf('%s', 2),
            'is_top'=>true,
        ]);
        $coFieldset->setRenderer($this->_viewLayout->createBlock('Unirgy\DropshipVendorMembership\Block\Vendor\MembershipOptionsFieldset'));
        $this->_prepareFieldsetColumns($coFieldset);
    }
    protected function _prepareFieldsetColumns($fieldset)
    {
        $elements = $fieldset->getElements()->getIterator();
        reset($elements);
        $bottomElements = $topElements = $lcElements = $rcElements = [];
        while($element=current($elements)) {
            if ($element->getIsBottom()) {
                $bottomElements[] = $element->getId();
            } elseif ($element->getIsTop()) {
                $topElements[] = $element->getId();
            } elseif ($element->getIsRight()) {
                $rcElements[] = $element->getId();
            } else {
                $lcElements[] = $element->getId();
            }
            next($elements);
        }
        $fieldset->setTopColumn($topElements);
        $fieldset->setBottomColumn($bottomElements);
        $fieldset->setLeftColumn($lcElements);
        $fieldset->setRightColumn($rcElements);
        reset($elements);
        return $this;
    }
    protected $_additionalElementTypes = null;
    protected function _initAdditionalElementTypes()
    {
        if (is_null($this->_additionalElementTypes)) {
            $this->_additionalElementTypes = [
            ];
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