<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cibilia\Commission\Model\Entity\Attribute\Source;

class Payout extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Option values
     */
    const VALUE_EMPTY = 0;
    
    const VALUE_BANK = 1;

    const VALUE_PAYPAL = 2;

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('Please Select..'), 'value' => self::VALUE_EMPTY],
                ['label' => __('Bank Transfer'), 'value' => self::VALUE_BANK],
                ['label' => __('Paypal'), 'value' => self::VALUE_PAYPAL],
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
