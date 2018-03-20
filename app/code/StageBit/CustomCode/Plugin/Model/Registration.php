<?php

namespace StageBit\CustomCode\Plugin\Model;

class Registration
{
    public function afterToVendor(
        \Unirgy\DropshipMicrosite\Model\Registration $subject,
        $result
    )
    {
        $result->unsetData('status');
        return $result;
    }
}