<?php
namespace StageBit\CustomCode\Controller\Vendor;

use Unirgy\DropshipMicrositePro\Controller\Vendor\AbstractVendor;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\DesignInterface;

/**
 * Class CheckEmailUnique
 *
 * @package StageBit\CustomCode\Controller\Vendor
 */
class CheckEmailUnique extends AbstractVendor
{
    /**
     * @var \StageBit\CustomCode\Helper\Data
     */
    protected $_helper;

    /**
     * CheckEmailUnique constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param DesignInterface $viewDesignInterface
     * @param RawFactory $resultRawFactory
     * @param EncoderInterface $jsonEncoder
     * @param \Unirgy\DropshipMicrosite\Helper\Data $micrositeHelper
     * @param \Unirgy\DropshipMicrositePro\Helper\Data $micrositeProHelper
     * @param \Unirgy\Dropship\Helper\Data $udropshipHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \StageBit\CustomCode\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        DesignInterface $viewDesignInterface,
        RawFactory $resultRawFactory,
        EncoderInterface $jsonEncoder,
        \Unirgy\DropshipMicrosite\Helper\Data $micrositeHelper,
        \Unirgy\DropshipMicrositePro\Helper\Data $micrositeProHelper,
        \Unirgy\Dropship\Helper\Data $udropshipHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \StageBit\CustomCode\Helper\Data $helper
    )
    {
        parent::__construct($context, $scopeConfig, $viewDesignInterface, $resultRawFactory, $jsonEncoder, $micrositeHelper, $micrositeProHelper, $udropshipHelper, $resultPageFactory);
        $this->_helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        if (empty($email)) {
            return $this->returnResult([
                'error'=>true,
                'success'=>false,
                'message'=>'Empty email'
            ]);
        } else {
            if (!$this->_helper->checkEmailUnique($email)) {
                return $this->returnResult([
                    'error'=>true,
                    'success'=>false,
                    'message'=>'Email is used'
                ]);
            } else {
                return $this->returnResult([
                    'error'=>false,
                    'success'=>true,
                    'message'=>'Email is not used'
                ]);
            }
        }
    }
}
