<?php

namespace Cibilia\Commission\Model;

/**
 * Commission Model
 *
 * @method \Cibilia\Commission\Model\Resource\Page _getResource()
 * @method \Cibilia\Commission\Model\Resource\Page getResource()
 */
class Commissions extends \Magento\Framework\Model\AbstractModel
{
    
	 /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */

    protected $inlineTranslation;
    protected $_transportBuilder;
    protected $_storeManager;
    protected $_messageManager;
    protected $_logger;
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectmanager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_objectManager = $objectmanager;
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_messageManager = $messageManager;

    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cibilia\Commission\Model\ResourceModel\Commissions');
    }

    public function genrateCibilianOrders()
    {
        //$date = date('Y-m-d H:i:s');
        $lastUpdateDate = date('Y-m-d H:i:s', strtotime('-2 day'));

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $completedOrders = $this->_getCompletedOrders();
		
		$arrOrdersCollection = $objectManager->create('Magento\Sales\Model\Order')->getCollection();
        
        /*$arrOrdersCollection->addAttributeToFilter('updated_at', array('to'=>$lastUpdateDate))
        ->addAttributeToFilter('status', array('eq' => 'complete'));*/

		$arrOrdersCollection->addAttributeToFilter('status', array('eq' => 'complete'));        

        if(count($completedOrders) > 0){
        	$arrOrdersCollection->addFieldToFilter(
                'entity_id',
                ['nin' => $completedOrders]);
        }
        
		if(count($arrOrdersCollection) > 0){

        	$arrCibiliansTotals = array();
        
	        $globalRate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/cibi_global_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	        $globalType1Rate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_type1_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	        $globalType2Rate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_type2_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	        $globalCostRate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_cost_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	        $multiShippingRate = 15;


	        foreach ($arrOrdersCollection as $order) {
	            /*if($order->getId() != 14){
	                continue;
	            }*/
	            $shippingCost = 0;
	            $multiShipCost = 0;
	            $isMultiVendor = false;

	            if($this->_checkMultiVendor($order)){
	            	$isMultiVendor = true;
	            }
	            if(!$isMultiVendor && !$order->getIsVirtual()){
	            	$shippingCost = $order->getShippingInclTax() / $order->getTotalItemCount();
	            }
	            
	            $arrCibilians = array();
	            
	            foreach ($order->getAllVisibleItems() as $itemId => $item) {
	            	if(!$isMultiVendor && ($shippingCost > 0)){
	            		if($shippingCost > $item->getRowTotal()){
	            			continue;
	            		}
	            	}
	                $isGlobalCost = 0;
	            
	                $arrRates = array();
	                $ObjProduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
	                
	                if($ObjProduct->getUdropshipVendor()){


	                    $objVendor = $objectManager->get('Unirgy\Dropship\Model\Vendor')->load($ObjProduct->getUdropshipVendor());

	                    if($objVendor && $objVendor->getId()){

	                        $cibilianId = $this->_getCibilianId($objVendor->getEmail());
	                        
	                        if(!$cibilianId){
	                            continue;
	                        }
	                        if($ObjProduct->getCostOfSale()){
	                            $productCost = $ObjProduct->getCostOfSale();
	                        }else{
	                            $productCost = $globalCostRate;
	                            $isGlobalCost = 1;
	                        }
	                        $cibiliaCommision = ($globalRate / 100) * $item->getRowTotalInclTax();

	                        if($isMultiVendor){
	                        	$multiShipCost = ($multiShippingRate / 100) * $item->getRowTotal();
	                        	$costOfSale = ($productCost / 100) * ($item->getRowTotalInclTax() + $multiShipCost);
	                        }else{
	                        	$costOfSale = ($productCost / 100) * ($item->getRowTotalInclTax() + $shippingCost);
	                        }
	                        
	                        $pendingCommission = $cibiliaCommision - $costOfSale;
	                        $actualCoimmission = 0;

	                        if($objVendor->getVendorType() == '1'){
	                            if($ObjProduct->getTypeoneCommission()){
	                            	
	                            	$actualCoimmission = ($ObjProduct->getTypeoneCommission() / 100) * $pendingCommission;
	                                $vendorTypeRate = $ObjProduct->getTypeoneCommission();
	                                $vendorTypeGolbalRate = 0;
	                        
	                            }else{
	                            	$actualCoimmission = ($globalType1Rate / 100) * $pendingCommission;
	                                $vendorTypeRate = $globalType1Rate;
	                                $vendorTypeGolbalRate = 1;
	                            }
	                             
	                        }else{
	                            if($ObjProduct->getTypetwoCommission()){
	                            	$actualCoimmission = ($ObjProduct->getTypetwoCommission() / 100) * $pendingCommission;
	                                $vendorTypeRate = $ObjProduct->getTypetwoCommission();
	                                $vendorTypeGolbalRate = 0;
	                        
	                            }else{
	                            	$actualCoimmission = ($globalType2Rate / 100) * $pendingCommission;
	                                $vendorTypeRate = $globalType2Rate;
	                                $vendorTypeGolbalRate = 1;
	                            }
	                        }

	                        $arrTotalRates = array(
	                                'cibi_rate' => $globalRate,
	                                'vtype_rate' => array('rate' => $vendorTypeRate,'isGlobal' => $vendorTypeGolbalRate),
	                                'cost_rate' =>  array('rate' => $productCost,'isGlobal' => $isGlobalCost)
	                            );

	                        if(array_key_exists($order->getId().'_'.$cibilianId, $arrCibiliansTotals) ){
	                            
	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_commission'] = $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_commission'] + $actualCoimmission;
	                            
	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_amount'] = $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_amount'] + $item->getRowTotalInclTax();
	                            
	                            $arrOldRates = json_decode($arrCibiliansTotals[$order->getId().'_'.$cibilianId]['cibilia_commision_rate'],true);
	                            
	                            $arrOldRates[$item->getId()] = array(
	                                'pid' => $ObjProduct->getId(),
	                                'rates' => $arrTotalRates
	                            );
	                            
	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['cibilia_commision_rate'] = json_encode($arrOldRates);

	                        }else{

	                            $arrRates[$item->getId()] = array(
	                                'pid' => $ObjProduct->getId(),
	                                'rates' => $arrTotalRates
	                            );

	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId] = array(
	                                'cibilian_id' => $cibilianId,
	                                'order_id' => $order->getId(),
	                                'total_amount' => $item->getRowTotalInclTax(),
	                                'total_commission' => $actualCoimmission,
	                                'cibilia_commision_rate' => json_encode($arrRates)
	                            );

	                        }
	                    }
	                }
	                 
	            }
	        }
	        foreach ($arrCibiliansTotals as $cibilianOrder) {
	            try {
		            
		            $modelCommission = $objectManager->create('Cibilia\Commission\Model\Commissions');

					$modelCommission->setData($cibilianOrder);

					$modelCommission->setCreatedAt(date('Y-m-d H:i:s'));
					
					$modelCommission->setUpdatedAt(date('Y-m-d H:i:s'));

					$modelCommission->setStatus(2);

					$modelCommission->save();

					$this->_updateCibilianSummary($modelCommission);

				}catch (\Exception $e) {
		           // $this->_logger->debug($e->getMessage());
		        }
	        }
        }

        
    }
    public function _getCibilianId($email){

        $cibilianId = 0;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendor = $objectManager->get('Unirgy\DropshipMicrosite\Model\Registration')->load($email,'email');

        if($objVendor && $objVendor->getId()){
            $cibilianId = $objVendor->getReferredBy(); 
        }
        return $cibilianId;
         
    }
    public function _updateCibilianSummary($commission){

        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objSummary = $objectManager->get('Cibilia\Summary\Model\Summary')->load($commission['cibilian_id'],'cibilian_id');

        if($objSummary && $objSummary->getId()){

        	$newTotalAmount =  $objSummary->getTotalAmount() + $commission['total_commission'];
        	$newPendingAmount =  $objSummary->getPendingAmount() + $commission['total_commission'];

        	try {

		        $objSummary->setTotalAmount($newTotalAmount);
		        $objSummary->setPendingAmount($newPendingAmount);
		        $objSummary->setUpdatedAt(date('Y-m-d H:i:s'));

				$objSummary->save();

			}catch (\Exception $e) {
	           // $this->_logger->debug($e->getMessage());
	        }

        }else{
        	try {

		        $modelSummary = $objectManager->create('Cibilia\Summary\Model\Summary');

		        $arrSummary = array(
		        	'cibilian_id' => $commission['cibilian_id'],
		        	'total_amount' => $commission['total_commission'],
		        	'paid_amount' => 0.00,
		        	'pending_amount' => $commission['total_commission'],
		        	'created_at' => date('Y-m-d H:i:s'),
		        	'updated_at' => date('Y-m-d H:i:s'),
		        	'status' => 2
		        );
		        
		        $modelSummary->setData($arrSummary);

				$modelSummary->save();

			}catch (\Exception $e) {
	           // $this->_logger->debug($e->getMessage());
	        }
        }
         
    }
    public function _getCompletedOrders(){

    	$arrOrderResult = array();

    	$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
        $orderResult = $connection->fetchAll("SELECT order_id FROM cibilia_commission");

        return $orderResult;
    }
    public function _checkMultiVendor($order){
    	$isVendors = false;
    	$arrVendors = array();
    	foreach ($order->getAllVisibleItems() as $itemId => $item) {
    		$ObjProduct = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
	        if($ObjProduct->getUdropshipVendor()){
	        	$arrVendors[$ObjProduct->getUdropshipVendor()] = $ObjProduct->getUdropshipVendor();	
	        }
    	}
    	if(count($arrVendors) > 1){
    		$isVendors = true;
    	}
    	
    	return $isVendors;
    }
    public function _genrateLockedCommission($oid){

    		$order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($oid);

    		if($order && $order->getId()) {
	        

	    		$arrCibiliansTotals = array();
	        
		        $globalRate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/cibi_global_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		        $globalType1Rate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_type1_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		        $globalType2Rate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_type2_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		        $globalCostRate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_cost_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
		        $multiShippingRate = 15;


	            /*if($order->getId() != 14){
	                continue;
	            }*/
	            $shippingCost = 0;
	            $multiShipCost = 0;
	            $isMultiVendor = false;

	            if($this->_checkMultiVendor($order)){
	            	$isMultiVendor = true;
	            }
	            if(!$isMultiVendor && !$order->getIsVirtual()){
	            	$shippingCost = $order->getShippingInclTax() / $order->getTotalItemCount();
	            }
	            
	            $arrCibilians = array();
	            
	            foreach ($order->getAllVisibleItems() as $itemId => $item) {
	            	if(!$isMultiVendor && ($shippingCost > 0)){
	            		if($shippingCost > $item->getRowTotal()){
	            			continue;
	            		}
	            	}
	                $isGlobalCost = 0;
	            
	                $arrRates = array();
	                $ObjProduct = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
	                
	                if($ObjProduct->getUdropshipVendor()){


	                    $objVendor = $this->_objectManager->get('Unirgy\Dropship\Model\Vendor')->load($ObjProduct->getUdropshipVendor());

	                    if($objVendor && $objVendor->getId()){

	                        $cibilianId = $this->_getCibilianId($objVendor->getEmail());
	                        
	                        if(!$cibilianId){
	                            continue;
	                        }
	                        if($ObjProduct->getCostOfSale()){
	                            $productCost = $ObjProduct->getCostOfSale();
	                        }else{
	                            $productCost = $globalCostRate;
	                            $isGlobalCost = 1;
	                        }
	                        $cibiliaCommision = ($globalRate / 100) * $item->getRowTotalInclTax();

	                        if($isMultiVendor){
	                        	$multiShipCost = ($multiShippingRate / 100) * $item->getRowTotal();
	                        	$costOfSale = ($productCost / 100) * ($item->getRowTotalInclTax() + $multiShipCost);
	                        }else{
	                        	$costOfSale = ($productCost / 100) * ($item->getRowTotalInclTax() + $shippingCost);
	                        }
	                        
	                        $pendingCommission = $cibiliaCommision - $costOfSale;
	                        $actualCoimmission = 0;

	                        if($objVendor->getVendorType() == '1'){
	                            if($ObjProduct->getTypeoneCommission()){
	                            	
	                            	$actualCoimmission = ($ObjProduct->getTypeoneCommission() / 100) * $pendingCommission;
	                                $vendorTypeRate = $ObjProduct->getTypeoneCommission();
	                                $vendorTypeGolbalRate = 0;
	                        
	                            }else{
	                            	$actualCoimmission = ($globalType1Rate / 100) * $pendingCommission;
	                                $vendorTypeRate = $globalType1Rate;
	                                $vendorTypeGolbalRate = 1;
	                            }
	                             
	                        }else{
	                            if($ObjProduct->getTypetwoCommission()){
	                            	$actualCoimmission = ($ObjProduct->getTypetwoCommission() / 100) * $pendingCommission;
	                                $vendorTypeRate = $ObjProduct->getTypetwoCommission();
	                                $vendorTypeGolbalRate = 0;
	                        
	                            }else{
	                            	$actualCoimmission = ($globalType2Rate / 100) * $pendingCommission;
	                                $vendorTypeRate = $globalType2Rate;
	                                $vendorTypeGolbalRate = 1;
	                            }
	                        }

	                        $arrTotalRates = array(
	                                'cibi_rate' => $globalRate,
	                                'vtype_rate' => array('rate' => $vendorTypeRate,'isGlobal' => $vendorTypeGolbalRate),
	                                'cost_rate' =>  array('rate' => $productCost,'isGlobal' => $isGlobalCost)
	                            );

	                        if(array_key_exists($order->getId().'_'.$cibilianId, $arrCibiliansTotals) ){
	                            
	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_commission'] = $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_commission'] + $actualCoimmission;
	                            
	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_amount'] = $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['total_amount'] + $item->getRowTotalInclTax();
	                            
	                            $arrOldRates = json_decode($arrCibiliansTotals[$order->getId().'_'.$cibilianId]['cibilia_commision_rate'],true);
	                            
	                            $arrOldRates[$item->getId()] = array(
	                                'pid' => $ObjProduct->getId(),
	                                'rates' => $arrTotalRates
	                            );
	                            
	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId]['cibilia_commision_rate'] = json_encode($arrOldRates);

	                        }else{

	                            $arrRates[$item->getId()] = array(
	                                'pid' => $ObjProduct->getId(),
	                                'rates' => $arrTotalRates
	                            );

	                            $arrCibiliansTotals[$order->getId().'_'.$cibilianId] = array(
	                                'cibilian_id' => $cibilianId,
	                                'order_id' => $order->getId(),
	                                'total_amount' => $item->getRowTotalInclTax(),
	                                'total_commission' => $actualCoimmission,
	                                'cibilia_commision_rate' => json_encode($arrRates)
	                            );

	                        }
	                    }
	                }
	                 
	            }
	        }
	        foreach ($arrCibiliansTotals as $cibilianOrder) {
	            try {
		            
		            $modelCommission = $this->_objectManager->create('Cibilia\Commission\Model\Commissions');

					$modelCommission->setData($cibilianOrder);

					$modelCommission->setCreatedAt(date('Y-m-d H:i:s'));
					
					$modelCommission->setUpdatedAt(date('Y-m-d H:i:s'));

					$modelCommission->setStatus(3);

					$modelCommission->save();

					//$this->_updateCibilianSummary($modelCommission);

				}catch (\Exception $e) {
		           // $this->_logger->debug($e->getMessage());
		        }
	        }

    }
    public function _updateLockedCommission(){

    	//$date = date('Y-m-d H:i:s');
       // $lastUpdateDate = date('Y-m-d H:i:s', strtotime('-2 day'));

        $lockedCommission = $this->_getLockedCommission();

        if(count($lockedCommission) > 0){

        	$lockedOrders = array();
        	foreach ($lockedCommission as $oid) {
        		$lockedOrders[$oid['order_id']] =  $oid['order_id'];
        	}
        	//$arrOrdersCollection = $this->_objectManager->create('Magento\Sales\Model\Order')->getCollection()->addFieldToFilter('entity_id',['in' => $completedOrders])->addAttributeToFilter('updated_at', array('to'=>$lastUpdateDate));
        	
        	$arrOrdersCollection = $this->_objectManager->create('Magento\Sales\Model\Order')->getCollection()
        	->addFieldToFilter('status', array('eq' => 'complete'))
        	->addFieldToFilter('entity_id',['in' => $lockedOrders]);

        }
		
	}
    public function _getLockedCommission(){

    	$connection = $this->_objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
        $orderResult = $connection->fetchAll("SELECT commission_id,order_id FROM cibilia_commission where status='3'");

        return $orderResult;
    }

}
