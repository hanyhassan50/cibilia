<?php

namespace Unirgy\DropshipVendorMembership\Controller\Vendor;

use Magento\Captcha\Helper\Data as HelperData;
use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Model\Group;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Model\QuoteFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipMicrosite\Helper\Data as DropshipMicrositeHelperData;
use Unirgy\DropshipMicrosite\Model\RegistrationFactory;
use Unirgy\DropshipMicrosite\Model\Source as ModelSource;
use Unirgy\DropshipVendorMembership\Model\MembershipFactory;
use Unirgy\Dropship\Helper\Catalog;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;
use Unirgy\Dropship\Model\Source;

class RegisterPost extends AbstractVendor
{
    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var RegistrationFactory
     */
    protected $_registrationFactory;

    /**
     * @var QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var Catalog
     */
    protected $_helperCatalog;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    public function __construct(Context $context, 
        ScopeConfigInterface $scopeConfig, 
        DesignInterface $viewDesignInterface, 
        LayoutFactory $viewLayoutFactory, 
        HelperData $captchaHelperData, 
        DropshipHelperData $udropshipHelper, 
        DropshipMicrositeHelperData $micrositeHelper, 
        PageFactory $resultPageFactory, 
        StoreManagerInterface $modelStoreManagerInterface, 
        MembershipFactory $modelMembershipFactory, 
        RegistrationFactory $modelRegistrationFactory, 
        QuoteFactory $modelQuoteFactory, 
        Catalog $helperCatalog, 
        ProductFactory $modelProductFactory, 
        Registry $frameworkRegistry
    )
    {
        $this->_membershipFactory = $modelMembershipFactory;
        $this->_registrationFactory = $modelRegistrationFactory;
        $this->_quoteFactory = $modelQuoteFactory;
        $this->_helperCatalog = $helperCatalog;
        $this->_productFactory = $modelProductFactory;
        $this->_coreRegistry = $frameworkRegistry;

        parent::__construct($context, $scopeConfig, $viewDesignInterface, $viewLayoutFactory, $captchaHelperData, $udropshipHelper, $micrositeHelper, $resultPageFactory, $modelStoreManagerInterface);
    }

    public function execute()
    {
        $session = ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session');
        $hlp = $this->_msHlp;
		$data = $this->getRequest()->getParams();
		$is_ajax = $data["is_ajax"];

        try {
            $data = $this->getRequest()->getParams();
			$is_ajax = $data["is_ajax"];
			
            $membership = $this->_membershipFactory->create()->load(@$data['udmember']['membership']);

            if (!$membership->getId()) {
                throw new \Exception(__('Unknown membership'));
            }

            $session->setRegistrationFormData($data);
            $this->checkCaptcha();

            $reg = $this->_registrationFactory->create()->setData($data);
            $reg->setData('udmember_limit_products', $membership->getLimitProducts());
            $reg->setData('udmember_membership_code', $membership->getMembershipCode());
            $reg->setData('udmember_membership_title', $membership->getMembershipTitle());
            
            if($is_ajax == 1){
				$reg->setData('udmember_allow_microsite', 0);
			}else{
				$reg->setData('udmember_allow_microsite', $membership->getAllowMicrosite());
			}
            $reg->setData('udmember_billing_type', $membership->getBillingType());
            if (!in_array($membership->getBillingType(),['paypal'])) {
                $reg->setData('udmember_profile_sync_off', 1);
            }
            $reg->validate()->save();
			
            if (!$this->_scopeConfig->getValue('udropship/microsite/auto_approve', ScopeInterface::SCOPE_STORE)) {
                if($is_ajax != 1){
					$hlp->sendVendorSignupEmail($reg);
				}
            }
		    if($is_ajax != 1){
			    $hlp->sendVendorRegistration($reg);
			}
           
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
			  if($is_ajax == 1){
					echo "success";
					die;
			  }else{
                $this->messageManager->addSuccess(__('Thank you for application. As soon as your registration has been verified, you will receive an email confirmation'));
			  }
            }
            $session->unsRegistrationFormData();
            $this->_loginPostRedirect();

        } catch (\Exception $e) {
			if($is_ajax == 1){
				echo "error";
				die;
		    }else{	
				if (isset($vendor) && $vendor->getId()) $vendor->delete();
				$this->messageManager->addError($e->getMessage());
				if ($this->getRequest()->getParam('quick')) {
					$this->_redirect('udropship/vendor/login');
				} else {
					$this->_redirect('*//*/register');
				}
				return;
			}
        }
    }
}
