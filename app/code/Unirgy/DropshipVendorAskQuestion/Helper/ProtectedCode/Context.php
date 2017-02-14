<?php

namespace Unirgy\DropshipVendorAskQuestion\Helper\ProtectedCode;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Unirgy\DropshipVendorAskQuestion\Helper\Data as HelperData;
use Unirgy\Dropship\Helper\Data as DropshipHelperData;
use Unirgy\Dropship\Helper\ProtectedCode as HelperProtectedCode;

class Context
{
    /**
     * @var HelperData
     */
    public $_qaHlp;

    /**
     * @var DropshipHelperData
     */
    public $_hlp;

    /**
     * @var ScopeConfigInterface
     */
    public $_scopeConfig;

    public $inlineTranslation;
    public $_transportBuilder;

    public function __construct(
        \Unirgy\Dropship\Model\EmailTransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Unirgy\DropshipVendorAskQuestion\Helper\Data $helperData,
        \Unirgy\Dropship\Helper\Data $dropshipHelperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $configScopeConfigInterface)
    {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_qaHlp = $helperData;
        $this->_hlp = $dropshipHelperData;
        $this->_scopeConfig = $configScopeConfigInterface;
    }
}