<?php

require __DIR__ . '/app/bootstrap.php';

class deleteAttr extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface {

    public function launch()
    {
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributeId = 156; // Id of attribute
        $attributeModel = $objectManager->create('Magento\Customer\Model\Attribute')->load($attributeId);
        //echo "<pre>";
        //print_r($attributeModel->delete());
       // echo "<pre>";
        $attributeModel->delete();
        return $this->_response;
    }
    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
 
 
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$app = $bootstrap->createApplication('deleteAttr');

$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$bootstrap->run($app);