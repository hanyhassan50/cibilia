<?php

namespace Unirgy\DropshipVendorMembership\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipVendorMembership\Model\MembershipFactory;
use Unirgy\Dropship\Helper\Data as HelperData;

class UdropshipVendorSaveBefore extends AbstractObserver implements ObserverInterface
{
    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var HelperData
     */
    protected $_hlp;

    public function __construct(
        Layout $viewLayout,
        MembershipFactory $modelMembershipFactory,
        StoreManagerInterface $modelStoreManagerInterface, 
        HelperData $helperData)
    {
        $this->_membershipFactory = $modelMembershipFactory;
        $this->_storeManager = $modelStoreManagerInterface;
        $this->_hlp = $helperData;

        parent::__construct($viewLayout);
    }

    public function execute(Observer $observer)
    {
        $vendor = $observer->getVendor();
        if ($vendor->dataHasChangedFor('udmember_membership_code')
            && ($mCode = $vendor->getData('udmember_membership_code'))
        ) {
            $membership = false;
            $memberships = $this->_membershipFactory->create()->getCollection();
            foreach ($memberships as $m) {
                if ($m['membership_code']==$mCode) {
                    $membership = $m;
                    break;
                }
            }
            if ($membership) {
                if ($this->_hlp->isAdmin()) {
                    $vendor->setData('udmember_profile_sync_off', 1);
                }
                $vendor->setData('udmember_allow_microsite', $membership['allow_microsite']);
                $vendor->setData('udmember_billing_type', 'offline');
                $vendor->setData('udmember_limit_products', $membership['limit_products']);
                $vendor->setData('udmember_membership_title', $membership['membership_title']);
                if ($vendor->getOrigData('udmember_membership_code')) {
                    $history = $this->_hlp->unserialize($vendor->getData('udmember_history'));
                    $history[] = [
                        'profile_id'       => $vendor->getOrigData('udmember_profile_id'),
                        'profile_refid'    => $vendor->getOrigData('udmember_profile_refid'),
                        'membership_code'  => $vendor->getOrigData('udmember_membership_code'),
                        'membership_title' => $vendor->getOrigData('udmember_membership_title'),
                        'allow_microsite'  => $vendor->getOrigData('udmember_allow_microsite'),
                        'limit_products'   => $vendor->getOrigData('udmember_limit_products'),
                    ];
                    $vendor->setData('udmember_history', $this->_hlp->serialize($history));
                }
            } else {
                $vendor->setData('udmember_membership_code', $vendor->getOrigData('udmember_membership_code'));
            }
        }
    }
}
