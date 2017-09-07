<?php
namespace Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Factory class for @see
 * \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute
 */
class AttributeFactory
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
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Magento\\ConfigurableProduct\\Model\\Product\\Type\\Configurable\\Attribute')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
