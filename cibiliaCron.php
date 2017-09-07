<?php

require __DIR__ . '/app/bootstrap.php';

class CibiliaCron extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface {

    public function launch()
    {
        
        $objCommission = $this->_objectManager->create('Cibilia\Commission\Model\Commissions');
        //$objCommission = $this->_objectManager->create('Cibilia\Idproofs\Model\Idproof');
        //$objCommission->genrateCibilianOrders();
        $objCommission->_updateLockedCommission();
        
        return $this->_response;
    }
    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
 
 
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$app = $bootstrap->createApplication('CibiliaCron');

$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$bootstrap->run($app);