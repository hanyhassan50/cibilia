<?php

namespace Cibilia\Dropshipextend\Controller\Attribute;

// use \Magento\Catalog\Model\ResourceModel\Eav\Attribute;


class Add extends \Magento\Framework\App\Action\Action {

	//protected $_eavSetupFactory;
	//protected $_storeManager;
	//protected $_attributeFactory;
	protected $resultJsonFactory;

	public function _constuct(
			\Magento\Framework\App\Action\Context $context,  
			\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory      
    		//\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
    		//\Magento\Store\Model\StoreManagerInterfaceFactory $storeManager,
    		//\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory

		){

		 	//$this->_eavSetupFactory = $eavSetupFactory;
    		//$this->_storeManager = $storeManager;
    		//$this->_attributeFactory = $attributeFactory;
    		$this->resultJsonFactory = $resultJsonFactory;
    		parent::__construct($context);
	}

	public function execute(){

		$post = $this->getRequest()->getPost();


		if(isset($post['attribute_code']) && strlen($post['data']['q']) > 2 ){

			
			$attribute_arr = array();
			$attribute_arr[] = $post['data']['q'];

			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

			// find all options
			{
				$eavConfigObj = $objectManager->create('\Magento\Eav\Model\Config');
				$attribute = $eavConfigObj->getAttribute('catalog_product', $post['attribute_code']);
				$options = $attribute->getSource()->getAllOptions();

				
				$matchingArray = array();
				foreach($options as $option){
					$pos = strpos(strtolower($option['label']), strtolower($post['data']['q']));
					if(empty($pos) and $pos === 0){
						$matchingArray[] = array('id' => $option['value'], 
											'text' => $option['label']);
					}
				}

				if(empty($matchingArray)){
					
					//create that option
					{
						$storeObj = $objectManager
										->create('\Magento\Store\Model\StoreManagerInterface');
						$allStores = $storeObj->getStores();

						$attributeObj = $objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
						$attributeInfo = $attributeObj->getCollection()
               						->addFieldToFilter('attribute_code',['eq'=>$post['attribute_code']])
               						->getFirstItem();
               			

						$option = array();
						$option['attribute_id'] = $attributeInfo->getAttributeId();
						foreach($attribute_arr as $key=>$value){
						    $option['value'][$value][0]=$value;
						    foreach($allStores as $store){
						        $option['value'][$value][$store->getId()] = $value;
						    }
						}

						$eavSetup = $objectManager->create('\Magento\Eav\Setup\EavSetup');
						$eavSetup->addAttributeOption($option);
					}
				}

				// return json in same format
				{
					// find matching array again
					{
						$eavConfigObj = $objectManager->create('\Magento\Eav\Model\Config');
						$attribute = $eavConfigObj->getAttribute('catalog_product', $post['attribute_code']);
						$options = $attribute->getSource()->getAllOptions();

						// TESTING
						
						

						
						$matchingArray = array();
						foreach($options as $option){

							


							if(!empty($option['label'])){

								//var_dump($option);
								//die;	

								$pos = strpos(strtolower($option['label']),strtolower($post['data']['q']));
								if(empty($pos) and $pos === 0){
									$matchingArray[] = array('id' => $option['value'], 
													'text' => $option['label']);
								}	
							}
							
						}
						
					}

					// $result = $this->resultJsonFactory->create();
					$result = $objectManager->create("\Magento\Framework\Controller\Result\Json");
       				$data = array(
								"q" => $post['data']['q'] , 
       							"results" => $matchingArray
       						);
      				return $result->setData($data);
				}

				/**
				fetch all options from attribute code

				$m = make array where it has matching records
				if $m is not empty then
					return json in same format
				else
					create that option
					and return json in same format
				*/

			}


			$storeObj = $objectManager->create('\Magento\Store\Model\StoreManagerInterface');
			$allStores = $storeObj->getStores();


			$attributeObj = $objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
			$attributeInfo= $attributeObj->getCollection()
               ->addFieldToFilter('attribute_code',['eq'=>$post['attribute_code']])
               ->getFirstItem();
			$attribute_id = $attributeInfo->getAttributeId();

			

			

			echo "<pre>";
			print_r($options);
			die;
			
		}

	}

}