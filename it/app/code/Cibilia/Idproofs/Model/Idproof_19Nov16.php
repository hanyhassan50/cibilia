<?php
/**
 * Copyright © 2015 Cibilia. All rights reserved.
 */

namespace Cibilia\Idproofs\Model;


use Magento\Framework\Exception\IdproofException;

    /**
 * Idprooftab idproof model
 */
class Idproof extends \Magento\Framework\Model\AbstractModel
{

    
    const XML_PATH_ADVERTISER_EMAIL_TEMPLATE   = 'custom_cibilia/general/template';
    const XML_PATH_VENDOR_REVIEW_EMAIL_TEMPLATE   = 'custom_cibilia/vendor_review_email/template';
    const XML_PATH_VENDOR_TYPE_SELECT_EMAIL_TEMPLATE   = 'custom_cibilia/vendor_type_select_email/template';
    const XML_PATH_VENDOR_REG_EMAIL_TEMPLATE   = 'custom_cibilia/new_advertiser_email/template';
    const XML_PATH_VENDOR_TYPE_EMAIL_TEMPLATE   = 'custom_cibilia/vendor_type_email/template';
    const XML_PATH_CIBILIAN_WITHDRAW_EMAIL_TEMPLATE   = 'custom_cibilia/withdrawl_request_email/template';


    
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

    public function __construct(
        \Magento\Framework\Model\Context $context,
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
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_messageManager = $messageManager;
    }

    /**
     * @return void
     */
    public function _construct()
    {
        $this->_init('Cibilia\Idproofs\Model\ResourceModel\Idproof');
    }
     public function _sendNotifyEmail($customerId){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);

        $emailTemplateVariables = array(
            'fname' => $objCustomer->getFirstname(),
            'lname' => $objCustomer->getLastname(),
            'email' => $objCustomer->getEmail(),
        );

        $senderInfo = array(
            'name' => $objCustomer->getFirstname(),
            'email' => $objCustomer->getEmail()
        );

        $receiverInfo = array(
            'email' => $this->getRecieverEmail()
        );


        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_ADVERTISER_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            $this->_messageManager->addSuccess(
                __('Your request has been submitted.')
            );
        
        } catch (\Exception $e) {

            $this->inlineTranslation->resume();
            $this->_messageManager->addError(
                __('Cannot submit your request.')
            );
        }
    }
    public function _sendVendorTypeSelectEmail($vendor){

        /*echo "<pre>";
        print_r($vendor->getData());
        die;*/

        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);*/

        $emailTemplateVariables = array(
            'vname' => $vendor->getVendorName(),
            'vid' => base64_encode($vendor->getVendorId()),
        );

        $senderInfo = array(
            'name' => $this->getSalesName(),
            'email' => $this->getSalesEmail()
        );

        $receiverInfo = array(
            'email' => $vendor->getEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_VENDOR_TYPE_SELECT_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            /*$this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );*/
        
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->_messageManager->addError(
                __('Cannot sent invitation email.')
            );
        }
    }
    public function getSalesEmail()
    {
        return $this->_scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSalesName()
    {
        return $this->_scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getRecieverEmail()
    {
        return $this->_scopeConfig->getValue('custom_cibilia/general/recev_email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getRecieverName()
    {
        return $this->_scopeConfig->getValue('custom_cibilia/general/recev_name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getEmailtemplate($templatepath)
    {
        return $this->_scopeConfig->getValue($templatepath,\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getVendorcat()
    {
        $arrVendorcat = array(
            '1' => 'All',
            '2' => 'Beer',
            '3' => 'Wine',
            '4' => 'Olive Oil',
            '5' => 'Honey',
            '6' => 'Pasta',
            '7' => 'Cheese',
            '8' => 'Herbs, Spices, Condiments',
            '9' => 'Preserved Fruits and vegetables',
            '10' => 'Dried Fruits, Seed, Legumes',
            '111' => 'Bakery Products',
            '12' => 'Grains and Flours',
            '13' => 'Cured Meat and Cured Fish',
            '14' => 'Tea and Coffee',
        );       
        $return = array();
        foreach($arrVendorcat as $key => $value){
            $return[] = array('value' => $key, 'label'=> $value);
        }
        return $return;
    }
    public function getVendorwork()
    {
         $arrVendorwork = array(
            '1' => 'A. Company that produces its own products',
            '2' => 'B. Company that trades  gourmet food products bought from third-parties suppliers',
            '3' => 'C. Company that does both A and B'
        );
        $return = array();
        foreach($arrVendorwork as $key => $value){
            $return[] = array('value' => $key, 'label'=> $value);
        }
        
        return $return;
    }
    public function getVendortype()
    {
         $arrVendortype = array(
            '0' => 'No Type Selected',
            '1' => 'Yes, let your Cibilian Add your products information on your behalf',
            '2' => 'No, I will upload the products by myself'
        );
        $return = array();
        foreach($arrVendortype as $key => $value){
            $return[] = array('value' => $key, 'label'=> $value);
        }
        
        return $return;
    }
    /*public function _sendVendorNotifyEmail($vendor){

        $arrVendorName = explode(" ",$vendor->getVendorAttn(),2);
        $strVendorFname = $arrVendorName[0];
        $strVendorLname = $arrVendorName[1];

        $emailTemplateVariables = array(
            'fname' => $strVendorFname,
            'lname' => $strVendorLname,
            'letter' => $vendor->getVendorLetter(),
            'vname' => $vendor->getVendorName(),
            'vid' => $vendor->getVendorId(),
        );

        $senderInfo = array(
            'name' => $this->getSalesName(),
            'email' => $this->getSalesEmail()
        );

        $receiverInfo = array(
            'email' => $vendor->getEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_VENDOR_REVIEW_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            $this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );
        
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->_messageManager->addError(
               __('Information Review email not sent.')
            );
        }
    }*/
    public function _sendVendorNotifyEmail($vendor){

        $arrVendorName = explode(" ",$vendor->getVendorAttn(),2);
        $strVendorFname = $arrVendorName[0];
        $strVendorLname = $arrVendorName[1];

        $emailTemplateVariables = array(
            'fname' => $strVendorFname,
            'lname' => $strVendorLname,
            'letter' => $vendor->getVendorLetter(),
            'cibilian_name' => $this->_getCibilian($vendor->getEmail()),
            'vid' => $vendor->getVendorId(),
        );

        $senderInfo = array(
            'name' => $this->getSalesName(),
            'email' => $this->getSalesEmail()
        );

        $receiverInfo = array(
            'email' => $vendor->getEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_VENDOR_REVIEW_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            $this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );
        
        } catch (\Exception $e) {
            /*$this->inlineTranslation->resume();
            $this->_messageManager->addError(
               __('Information Review email not sent.')
            );*/
            $this->_sendVendorNotifyEmailAgain($vendor);
        }
    }
    public function _sendVendorNotifyEmailAgain($vendor){

        $arrVendorName = explode(" ",$vendor->getVendorAttn(),2);
        $strVendorFname = $arrVendorName[0];
        $strVendorLname = $arrVendorName[1];

        $emailTemplateVariables = array(
            'fname' => $strVendorFname,
            'lname' => $strVendorLname,
            'letter' => $vendor->getVendorLetter(),
            'cibilian_name' => $this->_getCibilian($vendor->getEmail()),
            'vid' => $vendor->getVendorId(),
        );

        $senderInfo = array(
            'name' => $this->getSalesName(),
            'email' => $this->getSalesEmail()
        );

        $receiverInfo = array(
            'email' => $vendor->getEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_VENDOR_REVIEW_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            $this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );
        
        } catch (\Exception $e) {
            
            $this->inlineTranslation->resume();
        }
    }
    public function _sendAdvertiserNotify($vendor){

        /*echo "<pre>";
        print_r($vendor->getData());
        die;*/

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /*$objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);*/
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');
        

        $emailTemplateVariables = array(
            'rname' => $customerSession->getCustomer()->getName(),
            'cname' => $vendor->getData("vendor_name"),
            'cemail' => $vendor->getData("email")
        );

        $senderInfo = array(
            'name' => $customerSession->getCustomer()->getName(),
            'email' => $customerSession->getCustomer()->getEmail()
        );

        $receiverInfo = array(
            'email' => $this->getRecieverEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_VENDOR_REG_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
           /* $this->_messageManager->addSuccess(
                __('Admintrator has been notify for your advertiser request.')
            );*/
        
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
           /* $this->_messageManager->addError(
                __('Cannot submit your request.')
            );*/
        }
    }
    public function _sendVendorTypeEmail($vendor){

        /*echo "<pre>";
        print_r($vendor->getData());
        die;*/

        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);*/

        $arrType = array('1' => 'Type 1','2' => 'Type 2');

        $emailTemplateVariables = array(
            'vname' => $vendor->getVendorName(),
            'vtype' => $arrType[$vendor->getVendorType()]
        );

        $senderInfo = array(
            'name' => $vendor->getVendorAttn(),
            'email' => $vendor->getEmail()
        );

        $receiverInfo = array(
            'email' => $this->getRecieverEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_VENDOR_TYPE_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            /*$this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );*/
        
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->_messageManager->addError(
                __('Cannot notify admin for your vendor type updation.')
            );
        }
    }
    public function _getCibilian($email){

        $strCibilian = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
        
        $result = $connection->fetchAll("SELECT referred_by FROM cibilian_referrals where email_id='".$email."'");

        $customerId = $result[0]['referred_by'];

        $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($customerId);
        if($objCustomer->getId()){
            $strCibilian = $objCustomer->getName();
        }
        
        return $strCibilian;
    }
    public function getIsSelectType($id){

        $isSelectType = false;
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
        
        $result = $connection->fetchAll("SELECT is_type_selected FROM udropship_vendor where vendor_id='".$id."'");

        $vendorSelect = $result[0]['is_type_selected'];

        if($vendorSelect == '1'){
            $isSelectType = true;    
        }
        return $isSelectType;
    }

    public function genrateCibilianOrders()
    {
        $date = date('Y-m-d H:i:s');
        $lastUpdateDate = date('Y-m-d H:i:s', strtotime('-2 day'));

        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $arrOrdersCollection = $objectManager->create('Magento\Sales\Model\Order')->getCollection();
        //$arrOrdersCollection->addAttributeToFilter('updated_at', array('to'=>$lastUpdateDate))
        //->addAttributeToFilter('status', array('eq' => 'complete'));

        $arrCibiliansTotals = array();
        
        $globalRate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/cibi_global_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $globalType1Rate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_type1_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $globalType2Rate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_type2_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $globalCostRate = $this->_scopeConfig->getValue('custom_cibilia/commission_config/global_cost_rate',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        


        foreach ($arrOrdersCollection as $order) {
            /*if($order->getId() != 14){
                continue;
            }
            */
            $arrCibilians = array();
            
            foreach ($order->getAllVisibleItems() as $itemId => $item) {
                $isGlobalCost = 0;
            
                $arrRates = array();
                $ObjProduct = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
                if($ObjProduct->getUdropshipVendor()){


                    $objVendor = $objectManager->get('Unirgy\Dropship\Model\Vendor')->load($ObjProduct->getUdropshipVendor());

                    if($objVendor && $objVendor->getId()){

                        $cibilianId = $this->_getCibilianId($objVendor->getEmail());
                        
                        if(!$cibilianId){
                            continue;
                        }
                        if($ObjProduct->getVendorCost()){
                            $productCost = 4;
                        }else{
                            $productCost = $globalCostRate;
                            $isGlobalCost = 1;
                        }
                        $cibiliaCommision = ($globalRate / 100) * $item->getRowTotalInclTax();
                        $costOfSale = ($productCost / 100) * $item->getRowTotalInclTax();
                        $pendingCommission = $cibiliaCommision - $costOfSale;
                        $actualCoimmission = 0;

                        if($objVendor->getVendorType() == '1'){
                            if($ObjProduct->getType1Commission()){
                                $actualCoimmission = ($ObjProduct->getType1Commission() / 100) * $pendingCommission;
                                $vendorTypeRate = $ObjProduct->getType1Commission();
                                $vendorTypeGolbalRate = 0;
                        
                            }else{

                                $actualCoimmission = ($globalType1Rate / 100) * $pendingCommission;
                                $vendorTypeRate = $globalType1Rate;
                                $vendorTypeGolbalRate = 1;
                            }
                             
                        }else{
                            if($ObjProduct->getType2Commission()){
                                $actualCoimmission = ($ObjProduct->getType2Commission() / 100) * $pendingCommission;
                                $vendorTypeRate = $ObjProduct->getType2Commission();
                                $vendorTypeGolbalRate = 0;
                        
                            }else{
                                $actualCoimmission = ($globalType2Rate / 100) * $pendingCommission;
                                $vendorTypeRate = $ObjProduct->getType2Commission();
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
        echo "<pre>";
        print_r($arrCibiliansTotals);
        echo "</pre>";
        
        foreach ($arrCibiliansTotals as $cibilianOrder) {
            $modelCommission = $objectManager->create('Cibilia\Commission\Model\Commissions');
            print_r($modelCommission);
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
    public function _sendWithdrawRequestEmail($cibilian){

        /*echo "<pre>";
        print_r($vendor->getData());
        die;*/



        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objCustomer = $objectManager->create('Magento\Customer\Model\Customer')->load($cibilian->getCibilianId());
        $requestAmount = $objectManager->create('Magento\Framework\Pricing\Helper\Data')->currency($cibilian->getAmount(),true,false);

        

        $emailTemplateVariables = array(
            'cibiname' => $objCustomer->getName(),
            'amount' => $requestAmount
        );

        $senderInfo = array(
            'name' => $objCustomer->getName(),
            'email' => $objCustomer->getEmail()
        );

        $receiverInfo = array(
            'email' => $this->getRecieverEmail()
        );

        $this->inlineTranslation->suspend();
        try {


            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($emailTemplateVariables);


            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->getEmailtemplate(self::XML_PATH_CIBILIAN_WITHDRAW_EMAIL_TEMPLATE))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            
            /*$this->_messageManager->addSuccess(
                __('Information Review Email has been sent to Vendor.')
            );*/
        
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            /*$this->_messageManager->addError(
                __('Cannot notify admin for your vendor type updation.')
            );*/
        }
    }
}