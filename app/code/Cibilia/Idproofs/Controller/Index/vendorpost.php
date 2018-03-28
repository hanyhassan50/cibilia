<?php

namespace Cibilia\Idproofs\Controller\Index;

use Magento\Framework\App\Action\Context;

/**
 * Class Vendorpost
 *
 * @package Cibilia\Idproofs\Controller\Index
 */
class Vendorpost extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Unirgy\Dropship\Model\Vendor
     */
    protected $_vendor;

    /**
     * @var \Cibilia\Idproofs\Model\Idproof
     */
    protected $_idproof;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \StageBit\CustomCode\Helper\Data
     */
    protected $_stagebitHelper;

    /**
     * @var \StageBit\CustomCode\Model\VendorEmail
     */
    protected $_vendorApproveEmail;

    /**
     * Vendorpost constructor.
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Unirgy\Dropship\Model\Vendor $vendor
     * @param \Cibilia\Idproofs\Model\Idproof $idproof
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Context $context
     */
    public function __construct(
        \StageBit\CustomCode\Helper\Data $stagebitHelper,
        \Unirgy\Dropship\Model\Vendor $vendor,
        \Cibilia\Idproofs\Model\Idproof $idproof,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \StageBit\CustomCode\Model\VendorEmail $vendorEmail,
        Context $context
    )
    {
        $this->_stagebitHelper = $stagebitHelper;
        $this->_vendor  =   $vendor;
        $this->_idproof =   $idproof;
        $this->_storeManager    =   $storeManager;
        $this->_vendorApproveEmail = $vendorEmail;
        parent::__construct($context);
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->getPost()) {
            $r = $this->getRequest();
            try {
                $id = $r->getParam('id');
                $data = $r->getParams();

                if($_FILES['logo']) {
                    $data['logo']   =   $this->_stagebitHelper->_uploadVendorImage('logo', $id);
                    $data['logo'] = 'registration/'.$id.'/'.$_FILES['logo']['name'];
                }


                if($_FILES['company_photos']) {
                    $data['company_photos'] = $this->_stagebitHelper->_uploadVendorImage('company_photos', $id);
                    $data['company_photos'] = 'registration/'.$id.'/'.$_FILES['company_photos']['name'];
                }

                $data['vendor_attn'] = $data['owner_name'];
                $data['password_confirm'] = $data['password'];
                if (isset($data['vendor_categories'])) {
                    $data['vendor_categories'] = implode(',', $data['vendor_categories']);
                }

                $model = $this->_vendor->load($id);

                $model->addData($data);

                $model->save();

                $this->_vendorApproveEmail->_sentVendorApproveNotifyEmailToCibilia($model);

                $this->_idproof->_sendVendorTypeEmail($model);

                $this->messageManager->addSuccess(__('Vendor Information successfully saved.'));

                $model->setIsInfoReviewed(1);

                $model->setStatus('V');

                $model->save();

            } catch (\Exception $e) {

                $this->messageManager->addError($e->getMessage());
            }
        }
       return $resultRedirect->setPath($this->_storeManager->getStore()->getBaseUrl());
    }

}
