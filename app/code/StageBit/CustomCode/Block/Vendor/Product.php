<?php

namespace StageBit\CustomCode\Block\Vendor;

use Unirgy\DropshipVendorProduct\Block\Vendor\Product as VendorProduct;

class Product extends VendorProduct
{
    public function getForm()
    {
        if (null === $this->_form) {
            $prod = $this->getProduct();

            $hideFields = $this->_prodHlp->getHideEditFields();

            /** @var \Magento\Framework\Data\FormFactory $formFactory */
            $formFactory = $this->_hlp->getObj('\Magento\Framework\Data\FormFactory');
            $this->_form = $formFactory->create();
            $this->_form->setDataObject($prod);

            $values = $prod->getData();

            if ($prod->getId() && $this->_hlp->getStockItem($prod)) {
                $values = array_merge($values, ['stock_data'=>$this->_hlp->getStockItem($prod)->getData()]);
            }
            if (($udFormData = $this->_hlp->session()->getUdprodFormData(true))
                && is_array($udFormData)
            ) {
                unset($udFormData['media_gallery']);
                $values = array_merge($values, $udFormData);
            }

            $mvData = [];
            $v = $this->getVendor();
            if (!empty($values['udmulti'])) {
                $mvData = $values['udmulti'];
            } else {
                if ($this->_hlp->isUdmultiActive()) {
                    $this->_hlp->getObj('Unirgy\DropshipMulti\Helper\Data')->attachMultivendorData([$prod], false, true);
                    $mvData = $prod->getAllMultiVendorData($v->getId());
                    $mvData = !empty($mvData) ? $mvData : [];
                }
            }

            $cId = $prod->getCategoryIds();
            if (empty($cId) && !$this->_prodHlp->getUseTplProdCategoryBySetId($prod)) {
                $cId = $this->_prodHlp->getDefaultCategoryBySetId($prod);
            }
            $values['product_categories'] = @implode(',', (array)$cId);

            $wId = $prod->getWebsiteIds();
            if (empty($wId) && !$this->_prodHlp->getUseTplProdWebsiteBySetId($prod)) {
                $wId = $this->_prodHlp->getDefaultWebsiteBySetId($prod);
            }
            $values['product_websites'] = @implode(',', (array)$wId);

            $fsIdx = 0;
            $skipInputType = ['media_image'];
            if ('configurable' == $prod->getTypeId()) {
                $skipInputType[] = 'gallery';
            }
            $fieldsetsConfig = $this->_hlp->getScopeConfig('udprod/form/fieldsets');
            if (!is_array($fieldsetsConfig)) {
                $fieldsetsConfig = $this->_hlp->unserialize($fieldsetsConfig);
            }

            $_attributes = $prod->getAttributes();
            $hideStatus = false;
            if(!$prod->getId() || $this->_hlp->session()->getCreatedBy() == 2) {
                $hideStatus = true;
            }
            $attributes = [];
            foreach ($_attributes as $_attr) {
                if($hideStatus && $_attr->getAttributeCode() == 'status'){
                    continue;
                }
                $attributes[$_attr->getAttributeCode()] = $_attr;
            }
            $includedFields = [];
            $emptyForm = true;
            if (is_array($fieldsetsConfig)) {
                foreach ($fieldsetsConfig as $fsConfig) {
                    if (is_array($fsConfig)) {
                        $fields = [];

                        foreach (['top_columns','bottom_columns','left_columns','right_columns'] as $colKey) {
                            if (isset($fsConfig[$colKey]) && is_array($fsConfig[$colKey])) {
                                $requiredFields = (array)@$fsConfig['required_fields'];
                                foreach ($fsConfig[$colKey] as $fieldCode) {
                                    if (!$this->_isFieldApplicable($prod, $fieldCode, $fsConfig)) continue;
                                    $field = [];
                                    if (strpos($fieldCode, 'product.') === 0
                                        && !in_array(substr($fieldCode, 8), $hideFields)
                                        && isset($attributes[substr($fieldCode, 8)])
                                        && $this->isMyProduct()
                                    ) {
                                        if (($field = $this->_getAttributeField($attributes[substr($fieldCode, 8)]))) {
                                            $field['product_attribute'] = $attributes[substr($fieldCode, 8)];
                                        }
                                    } elseif (strpos($fieldCode, 'udmulti.') === 0) {
                                        $field = $this->_getUdmultiField(substr($fieldCode, 8), $mvData);
                                    } elseif (strpos($fieldCode, 'stock_data.') === 0) {
                                        $field = $this->_getStockItemField(substr($fieldCode, 11), $values);
                                    } elseif (strpos($fieldCode, 'system.') === 0) {
                                        $field = $this->_getSystemField(substr($fieldCode, 7), $values);
                                    }
                                    if (!empty($field) && !in_array($field['type'], $skipInputType)) {
                                        switch ($colKey) {
                                            case 'top_columns':
                                                $field['is_top'] = true;
                                                break;
                                            case 'bottom_columns':
                                                $field['is_bottom'] = true;
                                                break;
                                            case 'right_columns':
                                                $field['is_right'] = true;
                                                break;
                                            default:
                                                $field['is_left'] = true;
                                                break;
                                        }
                                        if (in_array($fieldCode, $requiredFields)) {
                                            $field['required'] = true;
                                        } else {
                                            $field['required'] = false;
                                            if (!empty($field['class'])) {
                                                $field['class'] = str_replace('required-entry', '', $field['class']);
                                            }
                                        }
                                        if (in_array($field['name'], $includedFields)) continue;
                                        $includedFields[] = $field['name'];
                                        $fields[] = $field;
                                    }
                                }
                            }}

                        if (!empty($fields)) {
                            $fsIdx++;
                            $fieldset = $this->_form->addFieldset('group_fields'.$fsIdx,
                                [
                                    'legend'=>$fsConfig['title'],
                                    'class'=>'fieldset-wide',
                                ]);
                            $this->_addElementTypes($fieldset);
                            foreach ($fields as $field) {
                                if (!empty($field['input_renderer']) && !$this->_hasCustomInputType($field['type'])) {
                                    $fieldset->addType($field['type'], $field['input_renderer']);
                                }
                                $formField = $fieldset->addField($field['id'], $field['type'], $field);
                                if (!empty($field['renderer'])) {
                                    $formField->setRenderer($field['renderer']);
                                }
                            }
                            $this->_prepareFieldsetColumns($fieldset);
                            $emptyForm = false;
                        }
                    }}}

            if (!$prod->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            if (!$emptyForm) {
                if ('configurable' == $prod->getTypeId()) {
                    $this->_addConfigurableSettings($prod, $values);
                }
                if (1 || 'configurable' != $prod->getTypeId()
                    || $this->_hlp->getScopeFlag('udprod/general/cfg_show_media_gallery')
                ) {
                    $cfgHideEditFields = explode(',', $this->_hlp->getScopeConfig('udropship/microsite/hide_product_attributes'));
                    if (isset($attributes['media_gallery'])
                        && !in_array('media_gallery', $cfgHideEditFields)
                        && $this->isMyProduct()
                    ) {
                        $attribute = $attributes['media_gallery'];
                        if ($attribute && (!$attribute->hasIsVisible() || $attribute->getIsVisible())
                            && ($inputType = $attribute->getFrontend()->getInputType())
                        ) {
                            $fieldset = $this->_form->addFieldset('group_fields_images',
                                [
                                    'legend'=>__('Images'),
                                    'class'=>'fieldset-wide',
                                ]);
                            $this->_addElementTypes($fieldset);
                            $fieldType      = $inputType;
                            $rendererClass  = $attribute->getFrontend()->getInputRendererClass();
                            if (!empty($rendererClass)) {
                                $fieldType  = $inputType . '_' . $attribute->getAttributeCode();
                                $fieldset->addType($fieldType, $rendererClass);
                            }

                            $mediaField = $fieldset->addField($attribute->getAttributeCode(), $fieldType,
                                [
                                    'name'      => $attribute->getAttributeCode(),
                                    'label'     => $attribute->getFrontend()->getLabel(),
                                    'class'     => $attribute->getFrontend()->getClass(),
                                    'required'  => $attribute->getIsRequired(),
                                    'note'      => $attribute->getNote(),
                                    'is_top'    => true
                                ]
                            )
                                ->setExplicitSection(true)
                                ->setEntityAttribute($attribute);
                            $this->_prepareFieldsetColumns($fieldset);
                        }
                    }
                }
                if ($this->_hlp->getScopeFlag('udprod/general/allow_custom_options')) {
                    $this->_addCustomOptions($prod, $values);
                }
                if ('downloadable' == $prod->getTypeId()) {
                    $this->_addDownloadableOptions($prod, $values);
                }
                if ('grouped' == $prod->getTypeId()) {
                    //$this->_addGroupedAssocProducts($prod, $values);
                }
            }

            if(isset($values['description'])){
                $values['description'] = strip_tags($values['description']);
            }
            $this->_form->addValues($values);

            $this->_form->setFieldNameSuffix('product');
        }
        return $this->_form;
    }
}
