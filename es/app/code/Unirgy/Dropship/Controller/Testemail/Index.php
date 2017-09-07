<?php
namespace Unirgy\Dropship\Controller\Testemail;

use \Magento\Framework\App\Action\Action;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;

// http://tst.cibilia.com/udropship/testemail/index

class Index extends Action
{

	protected $_storeManager;
	protected $_productFactory;
	protected $_prodHlp;

    protected $_hlp;
    protected $_errHlp;
    protected $_vendorNotifylowstock;
	
	public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $product,
        /*\Magento\Catalog\Helper\Product $productHelp,*/

         \Unirgy\DropshipVendorProduct\Helper\Data $vendorProductHelper,
         \Unirgy\Dropship\Helper\Data $udropshipHelper,
        \Unirgy\Dropship\Helper\Error $errorHelper,
        \Unirgy\Dropship\Model\Vendor\NotifyLowstock $vendorNotifylowstock

        ){
			$this->_storeManager = $storeManager;
			$this->_productFactory = $product;
			$this->_prodHlp = $vendorProductHelper;

            $this->_hlp = $udropshipHelper;
            $this->_errHlp = $errorHelper;
            $this->_vendorNotifylowstock = $vendorNotifylowstock;

			//$_productFactory;
			parent::__construct($context);		
	}

	public function execute()
    {


        $oldStoreId = $this->_storeManager->getStore()->getId();
        
        $this->_storeManager->setCurrentStore(0);
        $prods = $this->_productFactory->create()->getCollection()
            ->addAttributeToSelect(['sku', 'name', 'udropship_vendor', 'udprod_attributes_changed', 'udprod_cfg_simples_added', 'udprod_cfg_simples_removed'])
            ->addAttributeToFilter('status', \Unirgy\DropshipVendorProduct\Model\ProductStatus::STATUS_ENABLED)
            ->addAttributeToFilter('udprod_approved_notify', ['gt'=>0])
            ->addAttributeToFilter('udprod_approved_notified', 0);


        $this->prepareForNotification($prods);
        $prodByVendor = [];
        foreach ($prods as $prod) {
            if (($vId = $prod->getUdropshipVendor()) && ($v = $this->_hlp->getVendor($vId)) && $v->getId()) {
                $prodByVendor[$vId][$prod->getId()] = $prod;
            }
        }
        foreach ($prodByVendor as $vId=>$vProds) {
            $v = $this->_hlp->getVendor($vId);
            $this->_prodHlp->sendApprovedNotificationEmail($vProds, $v);
            $this->_prodHlp->sendApprovedAdminNotificationEmail($vProds, $v);
        }
        $this->_storeManager->setCurrentStore($oldStoreId);
    
    }


    public function prepareForNotification($prods)
    {
        foreach ($prods as $prod) {
            foreach ([
                 'udprod_attributes_changed',
                 'udprod_cfg_simples_added',
                 'udprod_cfg_simples_removed'] as $descrAttr
            ) {
                $descr = $prod->getData($descrAttr);
                if (!is_array($descr)) {
                    try {
                        $descr = unserialize($descr);
                    } catch (\Exception $e) {
                        $descr = [];
                    }
                }
                if (!is_array($descr)) {
                    $descr = [];
                }
                $prod->setData($descrAttr, $descr);
            }
        }
    }

}