<?php

namespace Unirgy\SimpleLicense\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class License
 * @method License setLastStatus(string $status)
 * @method License setLastError(string $error)
 * @method License setRetryNum(int $num)
 * @method License setSignature(string $signature)
 * @method License setAuxChecksum(string $checksum)
 * @method License setLicenseStatus(string $status)
 * @method string getLicenseKey()
 * @method string getAuxChecksum()
 * @method string getLicenseExpire()
 * @method string getLicenseStatus()
 * @method string getServerRestriction()
 * @method string getServerRestriction1()
 * @method string getServerRestriction2()
 * @method int getRetryNum()
 * @method array getProducts()
 * @package Unirgy\SimpleLicense\Model
 */
class License extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Unirgy\SimpleLicense\Model\ResourceModel\License');
    }
}
