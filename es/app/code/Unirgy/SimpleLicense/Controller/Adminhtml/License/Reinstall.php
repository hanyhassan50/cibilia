<?php

namespace Unirgy\SimpleLicense\Controller\Adminhtml\License;

use Unirgy\SimpleLicense\Controller\Adminhtml\AbstractLicense;
use Unirgy\SimpleLicense\Controller\Adminhtml\License;
use Unirgy\SimpleLicense\Model\ResourceModel\Setup;


class Reinstall extends AbstractLicense
{
    public function execute()
    {
        throw new \Exception(__METHOD__ . " not implemented");
        try {
            /* @var $installer Setup */
            $installer = new Setup('usimplelic_setup');
            $installer->reinstall();
            $this->_getSession()->addSuccess(__("Module DB files reinstalled"));
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        $this->_controllerResultRedirectFactory->create()->setPath('*/*/');
    }
}
