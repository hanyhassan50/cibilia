<?php

namespace StageBit\CustomCode\Block\Customer;

/**
 * Class HomeRegister
 *
 * @package StageBit\CustomCode\Block\Customer
 */
class HomeRegister extends \Magento\Customer\Block\Form\Register
{
    /**
     * @var \MSP\ReCaptcha\Block\Frontend\ReCaptcha
     */
    protected $_recaptcha;

    /**
     * HomeRegister constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \MSP\ReCaptcha\Block\Frontend\ReCaptcha $reCaptcha
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \MSP\ReCaptcha\Block\Frontend\ReCaptcha $reCaptcha,
        array $data = [])
    {
        $this->_recaptcha = $reCaptcha;
        parent::__construct($context, $directoryHelper, $jsonEncoder, $configCacheType, $regionCollectionFactory, $countryCollectionFactory, $moduleManager, $customerSession, $customerUrl, $data);
    }

    /**
     * @return string
     */
    public function getCaptchaLayout(){
        return $this->_recaptcha->getJsLayout();
    }
}