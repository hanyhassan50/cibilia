<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Vendor;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;

class Questions extends AbstractVendor
{
    public function execute()
    {
        $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        $session->setUdqaLastQuestionsGridUrl($this->_url->getUrl('*/*/*', ['_current'=>true]));
        $this->_renderPage(null, 'udqa');
    }
}
