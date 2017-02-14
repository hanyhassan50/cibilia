<?php
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Field id
     */
    const ID_FIELD = 'entity_id';

    const REDIRECT_URL = '*/*/';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Webkul\CustomRegistration\Model\ResourceModel\Customfields\Collection';

    /**
     * Page model
     *
     * @var string
     */
    protected $model = 'Webkul\CustomRegistration\Model\Customfields';
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
     /**
     * @param Action\Context $context
     * @param Product\Builder $productBuilder
     */
    private $_attributeSetFactory;

    protected $_attributeFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Customer\Model\AttributeFactory $attributeFactory
    ) {
        $this->_attributeSetFactory = $attributeSetFactory;
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
        return $this->_authorization->isAllowed('Webkul_CustomRegistration::customregistration');
    }
    public function execute()
    {
        $selected = $this->getRequest()->getParam('selected');
        $excluded = $this->getRequest()->getParam('excluded');
        try {
            if (isset($excluded)) {
                if (!empty($excluded) && $excluded != 'false') {
                    $this->excludedDelete($excluded);
                } else {
                    $this->deleteAll();
                }
            } elseif (!empty($selected)) {
                $this->selectedDelete($selected);
            } else {
                $this->messageManager->addError(__('Please select item(s).'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath(static::REDIRECT_URL);
    }

    /**
     * Delete all
     *
     * @return void
     * @throws \Exception
     */
    protected function deleteAll()
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedDelete(array $excluded)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedDelete(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setSuccessMessage($this->delete($collection));
    }

    /**
     * Delete collection items
     *
     * @param AbstractCollection $collection
     * @return int
     */
    protected function delete(AbstractCollection $collection)
    {
        $count = 0;
        $attributeModel = $this->_attributeFactory->create();
        foreach ($collection->getAllIds() as $id) {
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            $attributeId = $model->getAttributeId();
            $attributeModel->load($attributeId);
            $attributeModel->delete();
            $model->delete();
            ++$count;
        }

        return $count;
    }

    /**
     * Set error messages
     *
     * @param int $count
     * @return void
     */
    protected function setSuccessMessage($count)
    {
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $count));
    }
}