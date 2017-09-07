<?php
namespace Cibilia\Customattr\Setup;

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
  
/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
     
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
     
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
     
    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
  
     
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
         
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
         
        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
         
        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
         
        $customerSetup->addAttribute(Customer::ENTITY, 'is_idproof_notified', [
            'type' => 'int',
            'label' => 'Notifed For Approval',
            'input' => 'select',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'default' => '0',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'position' => 1005,
            'system' => 0,
        ]);

       $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'is_idproof_notified')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],//you can use other forms also ['adminhtml_customer_address', 'customer_address_edit', 'customer_register_address']
        ]);
        
         
        $attribute->save();
    }
}