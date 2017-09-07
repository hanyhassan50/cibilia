<?php
namespace Cibilia\Cibilians\Controller\Advertisers; 

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Unirgy\DropshipMicrosite\Model\Source as ModelSource;
use Unirgy\Dropship\Model\Source;
use Unirgy\DropshipMicrosite\Controller\Vendor\AbstractVendor;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Post extends AbstractVendor
{
    /**
     * Show Contact Us page
     *
     * @return void
     */
    // protected $_objectManager;
	// protected $_scopeConfig;
    // public function __construct(\Magento\Framework\App\Action\Context $context, ScopeConfigInterface $_scopeConfig) 
    // {
        // // $this->_objectManager = $objectManager;
        // parent::__construct($context,$_scopeConfig);    
    // }

    public function execute()
    {
	    $data = $this->getRequest()->getParams();
        if($this->_checkVendor($data['email'])){
            return $this->_redirect('*/*/');
        }
        //Get customer session
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customerSession = $objectManager->create('Magento\Customer\Model\Session');
		
		$session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        $hlp = $this->_msHlp;
        try {
            //$data = $this->getRequest()->getParams();
            $data['url_key'] = $this->_createUrlKey(strtolower($data['vendor_name']));
            //$data['url_key'] = str_replace(' ', '', $data['vendor_name']);
			$data['vendor_attn'] = $data['vendor_fname'].' '.$data['vendor_lname'];
            $data['vendor_categories'] = implode(',', $data['vendor_categories']);
            $data['vendor_website'] = $data['website'];

           /* echo "<pre>";
            print_r($data);
            die;*/
            echo "test";
            
            $membership = $this->_hlp->createObj('\Unirgy\DropshipVendorMembership\Model\Membership')->load(3);

            if (!$membership->getId()) {
                throw new \Exception(__('Unknown membership'));
            }

            $session->setRegistrationFormData($data);
            echo "test1";
            
            //$this->checkCaptcha();
            
            /** @var \Unirgy\DropshipMicrosite\Model\Registration $reg */
            $reg = $this->_hlp->createObj('\Unirgy\DropshipMicrosite\Model\Registration')
                ->setData($data);
		    $reg->setData('udmember_limit_products', $membership->getLimitProducts());
            $reg->setData('udmember_membership_code', $membership->getMembershipCode());
            $reg->setData('udmember_membership_title', $membership->getMembershipTitle());
            $reg->setData('udmember_allow_registration', $membership->getAllowRegistration());

            if($is_ajax == 1){
				$reg->setData('udmember_allow_microsite', 0);
			}else{
				$reg->setData('udmember_allow_microsite', $membership->getAllowMicrosite());
			}
            $reg->setData('udmember_billing_type', $membership->getBillingType());
            if (!in_array($membership->getBillingType(),['paypal'])) {
                $reg->setData('udmember_profile_sync_off', 1);
            }
            echo "validate before";
            //$reg->validate();
            echo "validate success";
            $reg->save();
                   
            if (!$this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
                // $hlp->sendVendorSignupEmail($reg);
            }
            $hlp->sendVendorRegistration($reg);
            $session->unsRegistrationFormData();
            echo "test2";
            if ($this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
				echo "test3";
                $vendor = $reg->toVendor();
                $vendor->setStatus(Source::VENDOR_STATUS_INACTIVE);
                if ($this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)==ModelSource::AUTO_APPROVE_YES_ACTIVE
                ) {
                    $vendor->setStatus(Source::VENDOR_STATUS_ACTIVE);
                }
                $_FILES = [];
                if (!$this->_scopeConfig->isSetFlag('udropship/microsite/skip_confirmation', ScopeInterface::SCOPE_STORE)) {
                    // $vendor->setSendConfirmationEmail(1);
                    $vendor->save();
                    $this->messageManager->addSuccess(__('Thank you for application. Instructions were sent to your email to confirm it'));
                } else {
                    $vendor->save();
                    ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->loginById($vendor->getId());
                    if (!$this->_getVendorSession()->getBeforeAuthUrl()) {
                        $this->_getVendorSession()->setBeforeAuthUrl($this->_url->getUrl('udropship'));
                    }
                }
                echo "test4";
            } else {
				echo "test5";
                $this->messageManager->addSuccess(__('Thank you for application. As soon as your registration has been verified, you will receive an email confirmation'));
            }
            $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendAdvertiserNotify($reg);
			echo "test6";
        } catch (\Exception $e) {
			echo "test7";
            $this->messageManager->addError($e->getMessage());
            if ($this->getRequest()->getParam('quick')) {
                return $this->_redirect('udropship/vendor/login');
            } else {
			echo $e->getMessage();
			// print_r($post);die;
                //return $this->_redirect('*/*/');
            }
        }
        // return $this->_loginPostRedirect();
    }
    
    
    public function _checkVendor($email)
    {
        $isVendorFlag = false;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendor = $objectManager->create('Unirgy\Dropship\Model\Vendor');
        /* @var $model Vendor */
        $objVendor->load($email,'email');
        if($objVendor && $objVendor->getId()){
             $this->messageManager->addError(__('Vendor already exists for given email.'));
             return true;   
        }else{
            $objVendorReg = $objectManager->create('Unirgy\DropshipMicrosite\Model\Registration');
            $objVendorReg->load($email,'email');
            if($objVendorReg && $objVendorReg->getId()){
                $this->messageManager->addError(__('Email already exists in vendor registeration.'));
                return true;
            }
        }
        return $isVendorFlag;
    }
    
    
    public function _createUrlKey($string) {
       $urlString = str_replace(' ', '-',trim($string)); // Replaces all spaces with hyphens.
       return preg_replace('/[^A-Za-z0-9\-]/', '', $urlString); // Removes special chars.
    }
}
