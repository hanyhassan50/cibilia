<?php

namespace Cibilia\Idproofs\Setup;


use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface {
	
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		
        $setup->startSetup();
        
        $tableName = $setup->getTable('udropship_vendor_registration');
         
        if (version_compare($context->getVersion(), '7.0.0') < 0) {
              if ($setup->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
				$connection->addColumn(
                    $tableName,
                    'referred_by',
                    ['type' => Table::TYPE_INTEGER, 'nullable' => false, 'default' => 0, 'comment' => 'referred_by']
                );
            }
        }
        
        $setup->endSetup(); 
    }
}
