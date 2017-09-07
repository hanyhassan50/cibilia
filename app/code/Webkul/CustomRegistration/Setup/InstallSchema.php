<?php 
namespace Webkul\CustomRegistration\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('wk_customfields'))
            ->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )
            ->addColumn(
                'attribute_code',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Attribute Code'
            )
            ->addColumn(
                'attribute_id',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'attribute id'
            )
            ->addColumn(
                'attribute_label',
                Table::TYPE_TEXT,
                null,
                ['nullable' => false, 'default' => ''],
                'Attribute Label'
            )
            ->addColumn(
                'show_in_order',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Show in order'
            )
            ->addColumn(
                'show_in_email',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Show in email'
            )
            ->addColumn(
                'status',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Status'
            )
            ->addColumn(
                'has_parent',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Parent Check'
            )
            ->setComment('Custom Registration Tabel');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }

}