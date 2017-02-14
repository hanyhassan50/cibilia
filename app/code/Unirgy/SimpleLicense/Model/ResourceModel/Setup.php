<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pp
 * Date: 26.02.13
 * Time: 22:42
 *
 */

namespace Unirgy\SimpleLicense\Model\ResourceModel;

use \Magento\Framework\Module\Setup as ModuleSetup;


class Setup
    extends ModuleSetup
{
    public function reinstall()
    {
        $configVer = (string)$this->_moduleConfig->version;

        $this->_installResourceDb($configVer);
    }
}