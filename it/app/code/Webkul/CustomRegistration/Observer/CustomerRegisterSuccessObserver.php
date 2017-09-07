<?php
/**
 * Webkul Marketplace CustomerRegisterSuccessObserver Observer.
 *
 * @category    Webkul
 *
 * @author      Webkul Software
 */
namespace Webkul\CustomRegistration\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory;

class CustomerRegisterSuccessObserver implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_attributeCollection;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_eavEntity;


    protected $_logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $_customerRepository;
    /**
     * @var CustomerInterfaceFactory
     */
    protected $_customerDataFactory;
    /**
     * @var DataObjectHelper
     */
    protected $_dataObjectHelper;
    /**
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    protected $_customerMapper;

    /**
     * @param \Magento\Framework\Logger\Monolog       $logger
     * @param CollectionFactory                       $attributeCollection
     * @param CustomerInterfaceFactory                $customerDataFactory
     * @param DataObjectHelper                        $dataObjectHelper
     * @param \Magento\Eav\Model\Entity               $eavEntity
     * @param \Magento\Customer\Model\Customer\Mapper $customerMapper
     * @param CustomerRepositoryInterface             $customerRepository
     */
    public function __construct(
        \Magento\Framework\Logger\Monolog $logger,
        CollectionFactory $attributeCollection,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Eav\Model\Entity $eavEntity,
        \Magento\Customer\Model\Customer\Mapper $customerMapper,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->_customerRepository = $customerRepository;
        $this->_customerDataFactory = $customerDataFactory;
        $this->_customerMapper = $customerMapper;
        $this->_dataObjectHelper = $dataObjectHelper;
        $this->_logger = $logger;
        $this->_attributeCollection = $attributeCollection;
        $this->_eavEntity = $eavEntity;
    }

    /**
     * customer register event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer['account_controller'];
        $paramData = $data->getRequest();
        $customer = $observer->getCustomer();
        $customerId = $customer->getId();
        $typeId = $this->_eavEntity->setType('customer')->getTypeId();
        $collection = $this->_attributeCollection->create()
            ->setEntityTypeFilter($typeId)
            ->addVisibleFilter()
            ->addFilter('is_user_defined', 1)
            ->setOrder('sort_order', 'ASC');
        $customData = $paramData->getPostValue();

        $savedCustomerData = $this->_customerRepository->getById($customerId);
        $customer = $this->_customerDataFactory->create();
        $customData = array_merge(
            $this->_customerMapper->toFlatArray($savedCustomerData),
            $customData
        );
        $customData['id'] = $customerId;
        $customData['approval_status'] = 8;
        $this->_dataObjectHelper->populateWithArray(
            $customer,
            $customData,
            '\Magento\Customer\Api\Data\CustomerInterface'
        );
        $this->_customerRepository->save($customer);
    }
}
