<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Commission\Model\Entity\Attribute\Source;

class Created extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Option values
     */
    const VALUE_ADMIN = 0;
    
    const VALUE_VENDOR = 1;

    const VALUE_CIBILIAN = 2;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Admin'), 'value' => self::VALUE_ADMIN],
                ['label' => __('Vendor'), 'value' => self::VALUE_VENDOR],
                ['label' => __('Cibilian'), 'value' => self::VALUE_CIBILIAN],
            ];
        }
        return $this->_options;
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
