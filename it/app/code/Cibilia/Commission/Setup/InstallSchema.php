<?php


namespace Cibilia\Commission\Setup;

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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table cibilia_cibiliancommission
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cibilia_commission')
		)->addColumn(
			'commission_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'cibilian_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['unsigned' => true,'nullable' => false],
			'Cibilian Id'
		)->addColumn(
			'order_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['unsigned' => true,'nullable' => false],
			'Order Increment Id'
		)->addColumn(
			'total_amount',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			[12,4],
			['nullable' => true],
			'Total Order Amout'
		)->addColumn(
			'total_commission',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			[12,4],
			['nullable' => true],
			'Total Order Commission'
		)->addColumn(
			'cibilia_commision_rate',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			[12,4],
			['nullable' => true],
			'Commission Rate'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['unsigned' => true,'nullable' => false],
			'Status'
		)->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Created At'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Updated At'
        )->setComment('Cibilian Commission Table')
        ->setOption('type', 'InnoDB')
        ->setOption('charset', 'utf8');
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}