<?php

namespace StageBit\CustomCode\Model;

/**
 * Class VendorEmail
 *
 * @package StageBit\CustomCode\Model
 */
class VendorEmail
{
    /**
     * @var \Cibilia\Idproofs\Model\Idproof
     */
    protected $_idproof;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * VendorEmail constructor.
     *
     * @param \Cibilia\Idproofs\Model\Idproof $idproofModel
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
       \Cibilia\Idproofs\Model\Idproof $idproofModel,
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\Framework\Translate\Inline\StateInterface $state,
       \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    )
    {
        $this->_idproof = $idproofModel;
        $this->_scopeConfig =   $scopeConfig;
        $this->storeManager =   $storeManager;
        $this->inlineTranslation =  $state;
        $this->_transportBuilder    =   $transportBuilder;
    }

    /**
     * @param $vendor
     * @throws \Magento\Framework\Exception\MailException
     */
    public function _sentVendorApproveNotifyEmailToCibilia($vendor){
        $cibilia =  $this->_idproof->_prepareVendorData($vendor->getVendorId());

        $senderInfo = array(
            'name' => $this->_scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->_scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        );

        $templateOptions = array(
            'area' =>  \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        );
        $templateVars = array(
            'vendor' =>  $vendor
        );

        $to = array(
            'email' => $cibilia['cemail']
        );

        $storeId = $this->_idproof->getVendorsStoreId($vendor);

        $this->inlineTranslation->suspend();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_idproof->getEmailtemplate('custom_cibilia/vendor_approve_notifyto_cibilia/template',$storeId))
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($senderInfo)
            ->addTo($to)
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }

}