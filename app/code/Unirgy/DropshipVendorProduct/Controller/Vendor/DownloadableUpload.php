<?php

namespace Unirgy\DropshipVendorProduct\Controller\Vendor;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Registry;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Unirgy\DropshipVendorProduct\Helper\Data as DropshipVendorProductHelperData;
use Unirgy\Dropship\Controller\Vendor\AbstractVendor as VendorAbstractVendor;
use Unirgy\Dropship\Helper\Data as HelperData;

class DownloadableUpload extends AbstractVendor
{
    /**
     * @var \Magento\Downloadable\Model\Link
     */
    protected $_link;

    /**
     * @var \Magento\Downloadable\Model\Sample
     */
    protected $_sample;

    /**
     * Downloadable file helper.
     *
     * @var \Magento\Downloadable\Helper\File
     */
    protected $_fileHelper;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    public function __construct(
        \Magento\Downloadable\Model\Link $link,
        \Magento\Downloadable\Model\Sample $sample,
        \Magento\Downloadable\Helper\File $fileHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        DropshipVendorProductHelperData $helperData,
        Context $context,
        ScopeConfigInterface $scopeConfig,
        DesignInterface $viewDesignInterface,
        StoreManagerInterface $storeManager,
        LayoutFactory $viewLayoutFactory,
        Registry $registry,
        ForwardFactory $resultForwardFactory,
        HelperData $helper,
        PageFactory $resultPageFactory,
        RawFactory $resultRawFactory,
        Header $httpHeader,
        \Unirgy\Dropship\Helper\Catalog $helperCatalog,
        \Unirgy\DropshipVendorProduct\Model\ProductFactory $modelProductFactory,
        \Magento\MediaStorage\Helper\File\Storage\Database $storageDatabase
    ) {
        parent::__construct($helperData,$context,$scopeConfig,$viewDesignInterface,$storeManager,$viewLayoutFactory,$registry,$resultForwardFactory,$helper,$resultPageFactory,$resultRawFactory,$httpHeader,$helperCatalog,$modelProductFactory,$storageDatabase);
        $this->_link = $link;
        $this->_sample = $sample;
        $this->_fileHelper = $fileHelper;
        $this->uploaderFactory = $uploaderFactory;
    }
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $tmpPath = '';
        if ($type == 'samples') {
            $tmpPath = $this->_sample->getBaseTmpPath();
        } elseif ($type == 'links') {
            $tmpPath = $this->_link->getBaseTmpPath();
        } elseif ($type == 'link_samples') {
            $tmpPath = $this->_link->getBaseSampleTmpPath();
        }

        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $type]);

            $result = $this->_fileHelper->uploadFromTmp($tmpPath, $uploader);

            if (!$result) {
                throw new \Exception('File can not be moved from temporary folder to the destination folder.');
            }

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
            $result['path'] = str_replace('\\', '/', $result['path']);

            if (isset($result['file'])) {
                $relativePath = rtrim($tmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->_storageDatabase->saveFile($relativePath);
            }

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
