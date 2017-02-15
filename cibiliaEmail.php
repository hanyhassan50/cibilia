<?php

require __DIR__ . '/app/bootstrap.php';

class CibiliaEmail extends \Magento\Framework\App\Http implements \Magento\Framework\AppInterface {

    public function launch()
    {
        
        $arrayVendors = array();

        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load(91);

        foreach ($order->getAllVisibleItems() as $itemId => $item) {

            $ObjProduct = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
            
            if($ObjProduct->getUdropshipVendor()){

                
                $objVendor = $this->_objectManager->get('Unirgy\Dropship\Model\Vendor')->load($ObjProduct->getUdropshipVendor());

                if($objVendor && $objVendor->getId()){

                    $arrayVendors[$objVendor->getId()]['vendor']['id'] = $objVendor->getId();
                    $arrayVendors[$objVendor->getId()]['vendor']['email'] = $objVendor->getEmail();

                    $cibilianId = $this->_getCibilianId($objVendor->getEmail());
                    
                    if($cibilianId){

                        $arrayVendors[$objVendor->getId()]['cibilian']['id'] = $cibilianId;
                    }
                }
            }
             
        }
        if(count($arrayVendors)){
            
            $objEmail = $this->_objectManager->create('Cibilia\Idproofs\Model\Idproof');
            
            echo "<pre>";
            foreach ($arrayVendors as $vendor) {

                $objEmail->_sendOrderNotifyToVendor($order,$vendor['vendor']['email']);
            
                if($vendor['cibilian']['id']){
                    $objCustomer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($vendor['cibilian']['id']);
                    if($objCustomer && $objCustomer->getId()){
                        $objEmail->_sendOrderNotifyToVendor($order,$objCustomer->getEmail());
                        echo $objCustomer->getEmail();
                    }
                }
                print_r($vendor);
            } 
        }
        
        return $this->_response;
    }
    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
    public function _getCibilianId($email){

        $cibilianId = 0;
        //$this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendor = $this->_objectManager->get('Unirgy\DropshipMicrosite\Model\Registration')->load($email,'email');

        if($objVendor && $objVendor->getId()){
            $cibilianId = $objVendor->getReferredBy(); 
        }
        return $cibilianId;
         
    }
 
 
}
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$app = $bootstrap->createApplication('CibiliaEmail');

$obj = $bootstrap->getObjectManager();
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$bootstrap->run($app);