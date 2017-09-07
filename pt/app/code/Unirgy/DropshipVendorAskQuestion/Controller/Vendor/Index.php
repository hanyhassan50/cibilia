<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Vendor;



class Index extends AbstractVendor
{
    public function execute()
    {
        $this->_forward('questions');
    }
}
