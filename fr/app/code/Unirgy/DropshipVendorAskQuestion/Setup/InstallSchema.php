<?php

namespace Unirgy\DropshipVendorAskQuestion\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    const MEDIUMTEXT_SIZE=16777216;
    const TEXT_SIZE=65536;
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        // TODO: Implement install() method.
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        $questionTable = $installer->getTable('udqa_question');
        $table = $connection->newTable($questionTable)
            ->addColumn('question_id', Table::TYPE_INTEGER, 10, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true
            ])
            ->addColumn('question_status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>1])
            ->addColumn('answer_status', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>1])
            ->addColumn('product_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true])
            ->addColumn('shipment_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true])
            ->addColumn('customer_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true])
            ->addColumn('vendor_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true])
            ->addColumn('question_date', Table::TYPE_DATETIME, null, ['nullable' => false, 'default'=>'0000-00-00 00:00:00'])
            ->addColumn('answer_date', Table::TYPE_DATETIME, null, ['nullable' => false, 'default'=>'0000-00-00 00:00:00'])
            ->addColumn('customer_name', Table::TYPE_TEXT, 255, ['nullable' => false, 'default'=>''])
            ->addColumn('customer_email', Table::TYPE_TEXT, 255, ['nullable' => false, 'default'=>''])
            ->addColumn('question_text', Table::TYPE_TEXT, self::TEXT_SIZE, ['nullable' => true])
            ->addColumn('answer_text', Table::TYPE_TEXT, self::TEXT_SIZE, ['nullable' => true])
            ->addColumn('visibility', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>0])
            ->addColumn('is_customer_notified', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>0])
            ->addColumn('is_vendor_notified', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>0])
            ->addColumn('is_admin_question_notified', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>0])
            ->addColumn('is_admin_answer_notified', Table::TYPE_SMALLINT, null, ['nullable' => false, 'default'=>0])
            ->addIndex(
                $installer->getIdxName(
                    $questionTable,
                    ['question_status'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['question_status']
            )
            ->addIndex(
                $installer->getIdxName(
                    $questionTable,
                    ['answer_status'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['answer_status']
            )
            ->addIndex(
                $installer->getIdxName(
                    $questionTable,
                    ['customer_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName(
                    $questionTable,
                    ['vendor_id'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['vendor_id']
            )
            ->addIndex(
                $installer->getIdxName(
                    $questionTable,
                    ['answer_date','question_date'],
                    AdapterInterface::INDEX_TYPE_INDEX
                ),
                ['answer_date','question_date']
            )
            ->setComment('Vendor Questions Table')
            ->setOption('type', 'InnoDB')
            ->setOption('charset', 'utf8');
        $connection->createTable($table);

        $installer->endSetup();
    }
}