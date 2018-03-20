<?php

namespace Stagebit\CustomCode\Plugin\Unirgy\Helper;

/**
 * Class Data
 *
 * @package Stagebit\CustomCode\Plugin\Unirgy\Helper
 */
class Data
{
    /**
     * @var \Unirgy\Dropship\Model\Session
     */
    protected $_session;

    /**
     * @var \Cibilia\Idproofs\Model\Idproof
     */
    protected $_idproof;

    /**
     * Data constructor.
     *
     * @param \Unirgy\Dropship\Model\Session $session
     * @param \Cibilia\Idproofs\Model\Idproof $idproof
     */
    public function __construct(
        \Unirgy\Dropship\Model\Session $session,
        \Cibilia\Idproofs\Model\Idproof $idproof,
        \Magento\Catalog\Model\Product\Action $productAction
    )
    {
        $this->_session =   $session;
        $this->_idproof =   $idproof;
        $this->_productAction = $productAction;
    }

    /**
     * @param \Unirgy\DropshipVendorProduct\Helper\Data $subject
     * @param $product
     */
     public function beforeProcessAfterSave(
         \Unirgy\DropshipVendorProduct\Helper\Data $subject,
         $product
     ){
         $_session  = $this->_session;

         if($_session->getCreatedBy()) {
             $createdBy = $_session->getCreatedBy();
         } else {
             $createdBy = 1;
         }

         /*
          * #sent product approved email to vendor when cibilian create new product
          */
         if($product->isObjectNew() && $createdBy == 2){
             $this->_idproof->_sendProductNotifyToVendorFromAdmin($product->getId());
         }

         /*
          * #sent product approved email to admin when vendor create new product
          */
         if($product->isObjectNew() && $createdBy == 1){
             $this->_idproof->_sendProductNotifyAdminFromVendor($product->getId());
         }

         /**
          * #sent confirmation to admin from vendor that product is approve
          */
         if($createdBy == 1 && $product->getAttributeText('is_approved') == 'No'){
             $this->_productAction->updateAttributes([$product->getId()],['is_approved' => 1], \Magento\Store\Model\Store::DEFAULT_STORE_ID);
             $this->_idproof->_sendProductNotifyAdminFromCibilian($product->getId());
         }

         return [$product];
     }
}