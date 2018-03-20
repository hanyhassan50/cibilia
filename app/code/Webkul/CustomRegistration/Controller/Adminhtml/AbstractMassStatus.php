<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\CustomRegistration\Controller\Adminhtml;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\CustomRegistration\Model\ResourceModel\Customfields\CollectionFactory;

/**
 * Class AbstractMassStatus
 */
class AbstractMassStatus extends \Magento\Backend\App\Action
{
    /**
     * Item status
     *
     * @var bool
     */
    protected $status = true;
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    protected $_attributeFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        CollectionFactory $collectionFactory,
        \Magento\Customer\Model\AttributeFactory $attributeFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_attributeFactory = $attributeFactory;
        parent::__construct($context);
    }
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization
                        ->isAllowed(
                            'Webkul_CustomRegistration::customregistration'
                        );
    }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $attributeModel = $this->_attributeFactory->create();
        $count = 0;
        foreach ($collection as $item) {
            $id = $item->getEntityId();
            $item->setStatus($this->status);
            $attributeId = $item->getAttributeId();
            $attributeModel->load($attributeId);
            $requiredCheck = $attributeModel->getFrontendClass();
            $require = explode(' ', $requiredCheck);
            /**
             * if dependable attribute presents.
             */
            if (in_array('dependable_field_'.$attributeModel->getAttributeCode(), $require)) {
                $childAttributeModel = $this->_objectManager->get('Webkul\CustomRegistration\Model\Customfields');
                $childAttributeId = $id + 1;
                $childAttributeModel->load($childAttributeId);
                $childAttributeModel->setStatus($this->status);
                $childAttributeModel->save();
            }
            $item->save();
            $count++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $count));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
