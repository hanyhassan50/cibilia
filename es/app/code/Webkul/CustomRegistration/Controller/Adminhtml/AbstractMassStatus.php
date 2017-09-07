<?php

/**
 * Webkul CustomRegistration AbstractMassStatus Controller
 *
 * @category    Webkul
 * @package     Webkul_CustomRegistration
 * @author      Webkul Software Private Limited
 *
 */
 
namespace Webkul\CustomRegistration\Controller\Adminhtml;

use Webkul\CustomRegistration\Controller\Adminhtml\AbstractMassDisplayEmail;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * Class AbstractMassStatus
 */
class AbstractMassStatus extends AbstractMassDisplayEmail
{
    /**
     * @var AttributeSetFactory
     */
    private $_attributeSetFactory;

    /**
     * @param Action\Context                           $context,
     * @param \Magento\Customer\Model\AttributeFactory $attributeFactory
     * @param AttributeSetFactory                      $attributeSetFactory
    */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Model\AttributeFactory $attributeFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->_attributeSetFactory = $attributeSetFactory;
        parent::__construct($context, $attributeFactory);
    }

    /**
     * Set status to all
     *
     * @return void
     * @throws \Exception
     */
    protected function setStatusAll()
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $this->setStatus($collection);
        $this->setSuccessMessage($this->setStatus($collection));
    }

    /**
     * Set status to all but the not selected
     *
     * @param array $excluded
     * @return void
     * @throws \Exception
     */
    protected function excludedSetStatus(array $excluded)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['nin' => $excluded]);
        $this->setStatus($collection);
        $this->setSuccessMessage($this->setStatus($collection));
    }

    /**
     * Set status to selected items
     *
     * @param array $selected
     * @return void
     * @throws \Exception
     */
    protected function selectedSetStatus(array $selected)
    {
        /** @var AbstractCollection $collection */
        $collection = $this->_objectManager->get($this->collection);
        $collection->addFieldToFilter(static::ID_FIELD, ['in' => $selected]);
        $this->setStatus($collection);
        $this->setSuccessMessage($this->setStatus($collection));
    }

    /**
     * Set status to collection items
     *
     * @param AbstractCollection $collection
     * @return void
     */
    protected function setStatus(AbstractCollection $collection)
    {
        $count = 0;
        $attributeModel = $this->_attributeFactory->create();
        foreach ($collection->getAllIds() as $id) {
            $count++;
            /** @var \Magento\Framework\Model\AbstractModel $model */
            $model = $this->_objectManager->get($this->model);
            $model->load($id);
            $model->setStatus($this->status);
            $attributeId = $model->getAttributeId();
            $attributeModel->load($attributeId);
            $requiredCheck = $attributeModel->getFrontendClass();
            $require = explode(' ', $requiredCheck);

            $model->save();
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
        }
        return $count;
    }
}
