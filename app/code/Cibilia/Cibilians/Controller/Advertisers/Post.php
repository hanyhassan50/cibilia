<?php
namespace Cibilia\Cibilians\Controller\Advertisers; 

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\ScopeInterface;
use Unirgy\DropshipMicrosite\Model\Source as ModelSource;
use Unirgy\Dropship\Model\Source;
use Unirgy\DropshipMicrosite\Controller\Vendor\AbstractVendor;

class Post extends AbstractVendor
{
    /**
     * Show Contact Us page
     *
     * @return void
     */

    public function execute()
    {
	    $data = $this->getRequest()->getParams();

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $customerSession = $objectManager->create('Magento\Customer\Model\Session');

        $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        $hlp = $this->_msHlp;
		$is_ajax = $data["is_ajax"];
		
        try {
            //$data = $this->getRequest()->getParams();

            $data['url_key'] = $this->_createUrlKey(strtolower($data['url_key']));
            $data['password'] = 'vendor123';

            $membership = $this->_hlp->createObj('\Unirgy\DropshipVendorMembership\Model\Membership')->load(3);

            if (!$membership->getId()) {
                throw new \Exception(__('Unknown membership'));
            }

            $session->setRegistrationFormData($data);
            
            $this->checkCaptcha();
            
            /** @var \Unirgy\DropshipMicrosite\Model\Registration $reg */
            $reg = $this->_hlp->createObj('\Unirgy\DropshipMicrosite\Model\Registration');
            $reg->setData($data);
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
            $reg->save();
                   
            if (!$this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
                // $hlp->sendVendorSignupEmail($reg);
            }

		    if($is_ajax != 1){
			    $hlp->sendVendorRegistration($reg);
			}

            $session->unsRegistrationFormData();
            $customerSession->unsRefererFormData();



            if ($this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
                $vendor = $reg->toVendor();
                $vendor->setStatus(Source::VENDOR_STATUS_INACTIVE);
                if ($this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)==ModelSource::AUTO_APPROVE_YES_ACTIVE
                ) {
                    $vendor->setStatus(Source::VENDOR_STATUS_ACTIVE);
                }
                if($is_ajax == 1){
					$vendor->setStatus(Source::VENDOR_STATUS_DISABLED);
				}
                $_FILES = [];
				if (!$this->_scopeConfig->isSetFlag('udropship/microsite/skip_confirmation', ScopeInterface::SCOPE_STORE)) {
					if($is_ajax != 1){
						$vendor->setSendConfirmationEmail(1);
					}
                    $vendor->save();
                    
                    if($is_ajax == 1){
						echo "success";
						die;
					}else{
						$this->messageManager->addSuccess(__('Thank you for application. Instructions were sent to your email to confirm it'));
					}
                } else {
                    $vendor->save();
                    if($is_ajax == 1){
						echo "success";
						die;
					}else{
						ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->loginById($vendor->getId());
						if (!$this->_getVendorSession()->getBeforeAuthUrl()) {
							$this->_getVendorSession()->setBeforeAuthUrl($this->_url->getUrl('udropship'));
						}
					}
                }
            } else {
			  if(!empty($_FILES)){
				  $objectManager->create('Cibilia\Idproofs\Model\Idproof')->saveImage($reg);
			  }
			  if($is_ajax == 1){
					echo "success";
					die;
			  }else{
                $this->messageManager->addSuccess(__('Thank you for application. As soon as your registration has been verified, you will receive an email confirmation'));
			  }
            }
            $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendAdvertiserNotify($reg);

        } catch (\Exception $e) {
			if($is_ajax == 1){
				echo "error";
				die;
		    }else{	
				$this->messageManager->addError($e->getMessage());
				if ($this->getRequest()->getParam('quick')) {
					$this->_redirect('udropship/vendor/login');
				} else {
					echo $e->getMessage();
					return $this->_redirect('*/*/register');
				}
				return;
			}
        }
        $post = $this->getRequest()->getParams();

        $post['vendor_attn'] = $post['vendor_name'].' '.$post['owner_name'];
		$currenttime = date('Y-m-d H:i:s');
        $model = $this->_objectManager->create('Cibilia\Cibilians\Model\Advertisers');
        $model->setData('store_id', $post['store_id']);
        $model->setData('referred_by', $customerSession->getCustomerId());
        $model->setData('company_name', $post['vendor_name']);
        $model->setData('website', $post['company_website']);
        $model->setData('contact_person', $post['vendor_attn']);
        $model->setData('email_id', $post['email']);
        $model->setData('mobile', $post['telephone']);
        $model->setData('token', 'abcd');
        $model->setData('status', 1);
        $model->setData('referral_type', 1);
        $model->setData('created_date', $currenttime);
        $model->setData('updated_date', $currenttime);
        $model->setData('published_date', '');

        $model->save();
        
        if($is_ajax == 1){
			echo "success";
			die;
	    }else{
			$this->messageManager->addSuccess(__('Your request has been submitted successfully.'));    
			return $this->_redirect('*/*/');
		}
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
       $finalUrl = '';
       $urlString = str_replace(' ', '-',trim($string)); // Replaces all spaces with hyphens.
       $finalUrl = preg_replace('/[^A-Za-z0-9\-]/', '', $urlString); // Removes special chars.
       
       //$regUrl = $this->_checkUrlExist($finalUrl);
       return $finalUrl;

    }
    public function _checkUrlExist($name)
    {
        
        $urlString = str_replace(' ', '-',trim(strtolower($name))) ; // Replaces all spaces with hyphens.
        $finalUrl = preg_replace('/[^A-Za-z0-9\-]/', '', $urlString); // Removes special chars.

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objVendorReg = $objectManager->create('Unirgy\DropshipMicrosite\Model\Registration')->load($finalUrl,'url_key');
        if($objVendorReg && $objVendorReg->getId()){
            $this->messageManager->addError(__('Url key exists for given Company Name.'));
            return true;        
        }
        return false; 
    }   
}
