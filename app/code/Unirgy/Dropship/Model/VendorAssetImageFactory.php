<?php

namespace {
    include_once __DIR__ . '/VendorAssetImageInc.php';
    include_once __DIR__ . '/VendorAssetImageFactoryInc.php';
}

namespace Unirgy\Dropship\Model {

    /**
     * Factory class for @see \Unirgy\Dropship\Model\VendorAssetImage
     */
    class VendorAssetImageFactory extends \Magento\Catalog\Model\View\Asset\ImageFactory
    {
        /**
         * Object Manager instance
         *
         * @var \Magento\Framework\ObjectManagerInterface
         */
        protected $_objectManager = null;

        /**
         * Instance name to create
         *
         * @var string
         */
        protected $_instanceName = null;

        /**
         * Factory constructor
         *
         * @param \Magento\Framework\ObjectManagerInterface $objectManager
         * @param string $instanceName
         */
        public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Unirgy\\Dropship\\Model\\VendorAssetImage')
        {
            $this->_objectManager = $objectManager;
            $this->_instanceName = $instanceName;
        }

        /**
         * Create class instance with specified parameters
         *
         * @param array $data
         * @return \Unirgy\Dropship\Model\VendorAssetImage
         */
        public function create(array $data = array())
        {
            return $this->_objectManager->create($this->_instanceName, $data);
        }
    }
}
