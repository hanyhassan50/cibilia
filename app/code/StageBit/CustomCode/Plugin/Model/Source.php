<?php

namespace StageBit\CustomCode\Plugin\Model;

/**
 * Class Source
 *
 * @package StageBit\CustomCode\Plugin\Model
 */
class Source
{
    const VENDOR_STATUS_REJECTED_BY_VENDOR = 'X';
    const VENDOR_STATUS_APPROVED_BY_VENDOR = 'V';
    const VENDOR_STATUS_PENDING = 'Z';
    const VENDOR_STATUS_WAITING_BY_VENDOR = 'W';

    /**
     * @param \Unirgy\Dropship\Model\Source $subject
     *
     * @param $result
     * @return mixed
     */
    public function afterToOptionHash(
        \Unirgy\Dropship\Model\Source $subject,
        $result
    )
    {
        if ($subject->getPath() == 'vendor_statuses') {
            $options = array(
                self::VENDOR_STATUS_PENDING => __('Pending'),
                self::VENDOR_STATUS_REJECTED_BY_VENDOR => __('Vendor rejected'),
                self::VENDOR_STATUS_APPROVED_BY_VENDOR => __('Vendor approved'),
                self::VENDOR_STATUS_WAITING_BY_VENDOR => __('Sent email for approval'),
            );
            $result = array_merge($options, $result);


        }

        return $result;
    }
}

