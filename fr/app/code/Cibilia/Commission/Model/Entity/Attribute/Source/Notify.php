<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Commission\Model\Entity\Attribute\Source;

class Notify extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $objectManager;

    public function __construct( \Magento\Framework\ObjectManagerInterface $interface ) {
      
      $this->objectManager = $interface;
    
    }
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $attribute = $this->objectManager->create('Magento\Eav\Model\Entity\Attribute')->getCollection()->addFieldToFilter('entity_type_id',4)
        ->addFieldToFilter('is_user_defined',1);

        $attributesArrays = array();
        foreach($attribute as $attr){
            if(!empty($attr->getFrontendLabel())){
                $attributesArrays[] = array('value' => $attr->getAttributeId(), 'label'=> $attr->getFrontendLabel());
            }
        }
        return $attributesArrays;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = [];
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }
}
