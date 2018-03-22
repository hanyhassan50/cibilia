<?php

namespace StageBit\CustomCode\Model;

/**
 * Class CibilianDeniedEmail
 *
 * @package StageBit\CustomCode\Model
 */
class CibilianDeniedEmail
{
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
     * @var \Cibilia\Idproofs\Model\Idproof
     */
    protected $_idproof;

    /**
     * CibilianDeniedEmail constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Cibilia\Idproofs\Model\Idproof $idproofModel
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Cibilia\Idproofs\Model\Idproof $idproofModel
    )
    {
        $this->_scopeConfig =   $scopeConfig;
        $this->storeManager =   $storeManager;
        $this->inlineTranslation =  $state;
        $this->_transportBuilder =   $transportBuilder;
        $this->_idproof = $idproofModel;
    }


    /**
     * @param $cibilia
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sentIdentityNotVerifiedEmailToCibilia($cibilia){

        $senderInfo = array(
            'name' => $this->_scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->_scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        );

        $templateOptions = array(
            'area' =>  \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        );
        $templateVars = array(
            'cibilia' =>  $cibilia
        );

        $to = array(
            'email' => $cibilia->getEmail()
        );

        $storeId = $this->_idproof->getCustomerStoreId($cibilia);

        $this->inlineTranslation->suspend();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($this->_idproof->getEmailtemplate('custom_cibilia/cibilian_idproof_denied/template',$storeId))
            ->setTemplateOptions($templateOptions)
            ->setTemplateVars($templateVars)
            ->setFrom($senderInfo)
            ->addTo($to)
            ->getTransport();

        $transport->sendMessage();

        $this->inlineTranslation->resume();
    }
}