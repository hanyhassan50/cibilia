<?php
/**
 * Copyright © 2015 Cibilia. All rights reserved.
 */

namespace Cibilia\Idproofs\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'idproofs_idproof'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('idproofs_idproof')
        )
		->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'idproofs_idproof'
        )
		->addColumn(
            'identityproof',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Identityproof'
        )
		/*{{CedAddTableColumn}}}*/
		
		
        ->setComment(
            'Cibilia Idproofs idproofs_idproof'
        );
		
		$installer->getConnection()->createTable($table);
		/*{{CedAddTable}}*/

        $installer->endSetup();

    }
}
