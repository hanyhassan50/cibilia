<?php

namespace Unirgy\DropshipVendorAskQuestion\Controller\Customer;



class NewAction extends AbstractCustomer
{
    public function execute()
    {
        $this->_forward('form');
    }
}
