<?php

namespace StageBit\CustomCode\Plugin\Unirgy\Helper\Vendor;


class Data
{
    public function afterGetCurrentVendor(
        \Unirgy\DropshipMicrosite\Helper\Data $subject,
        $result
    ){
        if($result) {
            foreach ($result->getData() as $key => $value) {
                if (is_array($value)) {
                    $result->setData($key, implode(',', $value));
                }
            }
            return $result;
        }
    }
}