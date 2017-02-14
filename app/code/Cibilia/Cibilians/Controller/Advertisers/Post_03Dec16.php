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
            $data['url_key'] = str_replace(' ', '', $data['vendor_name']);
			$data['vendor_attn'] = $data['vendor_fname'].' '.$data['vendor_lname'];
            $data['vendor_categories'] = implode(',', $data['vendor_categories']);
            $data['vendor_website'] = $data['website'];

           /* echo "<pre>";
            print_r($data);
            die;*/
            $session->setRegistrationFormData($data);
            $this->checkCaptcha();
            /** @var \Unirgy\DropshipMicrosite\Model\Registration $reg */
            $reg = $this->_hlp->createObj('\Unirgy\DropshipMicrosite\Model\Registration')
                ->setData($data)
                ->validate()
                ->save();
            if (!$this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
                // $hlp->sendVendorSignupEmail($reg);
            }
            $hlp->sendVendorRegistration($reg);
            $session->unsRegistrationFormData();
            if ($this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
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
            } else {
                $this->messageManager->addSuccess(__('Thank you for application. As soon as your registration has been verified, you will receive an email confirmation'));
            }
            $objectManager->create('Cibilia\Idproofs\Model\Idproof')->_sendAdvertiserNotify($reg);

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            if ($this->getRequest()->getParam('quick')) {
                return $this->_redirect('udropship/vendor/login');
            } else {
			echo $e->getMessage();
			// print_r($post);die;
                return $this->_redirect('*/*/');
            }
        }
        // return $this->_loginPostRedirect();
		
	
	
        $post = $this->getRequest()->getPostValue();
		// echo "post:";
		$post['vendor_attn'] = $post['vendor_fname'].' '.$post['vendor_lname'];
		// print_r($post);die;
        $currenttime = date('Y-m-d H:i:s');
        $model = $this->_objectManager->create('Cibilia\Cibilians\Model\Advertisers');
        $model->setData('referred_by', $customerSession->getCustomerId());
        $model->setData('company_name', $post['vendor_name']);
        $model->setData('website', $post['website']);
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
        $this->messageManager->addSuccess(__('Your request has been submitted successfully.'));    
        $this->_redirect('*/*/');
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
}
