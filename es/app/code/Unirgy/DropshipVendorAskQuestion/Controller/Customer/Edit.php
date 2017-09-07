<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;



class Edit extends AbstractCustomer
{
    public function execute()
    {
        $this->_forward('form');
    }
}
