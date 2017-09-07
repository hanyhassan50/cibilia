<?php

namespace Cibilia\Summary\Controller;

class Test extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        
        echo "helo";
        die;    
    }
    protected function _isAllowed() {
        
        return true;
    }
    
}
