<?php
class adminUser
    extends \Magento\Framework\App\Http
    implements \Magento\Framework\AppInterface {

    public function launch()
    {

        $adminUser = $this->_objectManager->create('\Magento\User\Model\ResourceModel\User\Collection');
       
        //echo "<pre>"; print_r($adminUser->getData()); die;
        $adminUser->setData(array(
            'user_id' => 100,
            'username'  => 'sankhalainfo1234',
            'firstname' => 'Test',
            'lastname'    => 'User',
            'email'     => 'testingmagento1008@gmail.com',
            'password'  =>'info1234',
            'is_active' => 1
        ))->save();
        

        echo "User created successfully";
        return $this->_response;
    }

    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }

}