<?php

require __DIR__ . '/app/bootstrap.php';

class CibiliaAttributes extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface {

    public function launch()
    {
        
       $attribute = $this->objectManager->create('Magento\Eav\Model\Entity\Attribute')->getCollection();

        $attributesArrays = array();
        foreach($attribute as $attr){

            if(!empty($attr->getFrontendLabel())){
                $attributesArrays[] = array('value' => $attr->getAttributeId(), 'label'=> $attr->getFrontendLabel());
        }

        echo "<pre>";
        print_r($attributesArrays);
        die;
        
        return $this->_response;
    }
    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
 
 
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$app = $bootstrap->createApplication('CibiliaAttributes');

$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$bootstrap->run($app);