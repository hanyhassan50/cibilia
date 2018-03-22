<?php

namespace StageBit\CustomCode\Model;

/**
 * Class ProductEmail
 * @package StageBit\CustomCode\Model
 */
class ProductEmail
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $productData;

    /**
     * @var \Cibilia\Idproofs\Model\Idproof
     */
    protected $idproof;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_state;

    const XML_PATH_PRODUCT_SET_ONLINE_EMAIL_TEMPLATE   = 'custom_cibilia/product_set_online_email/template';

    /**
     * ProductEmail constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Catalog\Model\Product $product
     * @param \Cibilia\Idproofs\Model\Idproof $idproof
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\State $Appstate
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Catalog\Model\Product $product,
        \Cibilia\Idproofs\Model\Idproof $idproof,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\State $Appstate
     )
    {
        $this->storeManager         =   $storeManager;
        $this->_transportBuilder    =   $transportBuilder;
        $this->inlineTranslation    =   $state;
        $this->resource             =   $resourceConnection;
        $this->customer             =   $customer;
        $this->productData          =   $product;
        $this->idproof              =   $idproof;
        $this->urlInterface         =   $url;
        $this->_scopeConfig         =   $scopeConfig;
        $this->_state = $Appstate;
    }


    /**
     * @param $productID
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sentProductSetOnlineNotifyToCibliaVendorFromVendor($productID){

        $objProduct =   $this->productData->load($productID);
        $vendorData =   $this->idproof->_prepareVendorData($objProduct->getUdropshipVendor());

        $senderInfo = array(
            'name' => $this->_scopeConfig->getValue('trans_email/ident_sales/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->_scopeConfig->getValue('trans_email/ident_sales/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        );

        $baseUrl    =    $this->urlInterface->getBaseUrl();

        $templateOptions = array(
            'area' =>  \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeManager->getStore()->getId()
        );
        $templateVars = array(
            'product' =>  $objProduct,
            'baseurl' =>  $baseUrl
        );

        $to = array($vendorData['email'], $vendorData['cemail']);

        $storeId = $this->idproof->getVendorsStoreId($vendorData);

        $this->inlineTranslation->suspend();

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->idproof->getEmailtemplate(self::XML_PATH_PRODUCT_SET_ONLINE_EMAIL_TEMPLATE, $storeId))
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($senderInfo)
                ->addTo($to)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
    }
}