<?php
namespace Cibilia\Cibilians\Controller\Advertisers; 
 
class Editp extends \Magento\Framework\App\Action\Action {
   
	public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->getBlock('newadvertisers_type2');
        $this->_view->renderLayout();
    }
}