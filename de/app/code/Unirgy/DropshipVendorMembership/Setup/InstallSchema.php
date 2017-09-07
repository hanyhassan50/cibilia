<?php

namespace Unirgy\DropshipVendorMembership\Setup;

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

        $quoteTable = $installer->getTable('quote');
        $connection->addColumn($quoteTable, 'is_udmember', ['TYPE' => Table::TYPE_SMALLINT, 'nullable' => true, 'COMMENT' => 'is_udmember']);
        $orderTable = $installer->getTable('sales_order');
        $connection->addColumn($orderTable, 'is_udmember', ['TYPE' => Table::TYPE_SMALLINT, 'nullable' => true, 'COMMENT' => 'is_udmember']);

        $vendorTable = $installer->getTable('udropship_vendor');
        $connection->addColumn($vendorTable, 'udmember_limit_products', ['TYPE' => Table::TYPE_INTEGER, 'nullable' => true, 'COMMENT' => 'udmember_limit_products']);
        $connection->addColumn($vendorTable, 'udmember_profile_id', ['TYPE' => Table::TYPE_INTEGER, 'nullable' => true, 'COMMENT' => 'udmember_profile_id']);
        $connection->addColumn($vendorTable, 'udmember_profile_refid', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 32, 'nullable' => true, 'COMMENT' => 'udmember_profile_refid']);
        $connection->addColumn($vendorTable, 'udmember_membership_code', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 32, 'nullable' => true, 'COMMENT' => 'udmember_membership_code']);
        $connection->addColumn($vendorTable, 'udmember_membership_title', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 128, 'nullable' => true, 'COMMENT' => 'udmember_membership_title']);
        $connection->addColumn($vendorTable, 'udmember_billing_type', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 64, 'nullable' => true, 'COMMENT' => 'udmember_billing_type']);
        $connection->addColumn($vendorTable, 'udmember_history', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => self::MEDIUMTEXT_SIZE, 'nullable' => true, 'COMMENT' => 'udmember_history']);
        $connection->addColumn($vendorTable, 'udmember_profile_sync_off', ['TYPE' => Table::TYPE_SMALLINT, 'nullable' => true, 'COMMENT' => 'udmember_profile_sync_off']);
        $connection->addColumn($vendorTable, 'udmember_allow_microsite', ['TYPE' => Table::TYPE_SMALLINT, 'nullable' => true, 'default'=>1,'COMMENT' => 'udmember_allow_microsite']);

        $registrationTable = $installer->getTable('udropship_vendor_registration');
        $connection->addColumn($registrationTable, 'udmember_limit_products', ['TYPE' => Table::TYPE_INTEGER, 'nullable' => true, 'COMMENT' => 'udmember_limit_products']);
        $connection->addColumn($registrationTable, 'udmember_membership_code', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 32, 'nullable' => true, 'COMMENT' => 'udmember_membership_code']);
        $connection->addColumn($registrationTable, 'udmember_membership_title', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 128, 'nullable' => true, 'COMMENT' => 'udmember_membership_title']);
        $connection->addColumn($registrationTable, 'udmember_billing_type', ['TYPE' => Table::TYPE_TEXT, 'LENGTH' => 64, 'nullable' => true, 'COMMENT' => 'udmember_billing_type']);
        $connection->addColumn($registrationTable, 'udmember_profile_sync_off', ['TYPE' => Table::TYPE_SMALLINT, 'nullable' => true, 'COMMENT' => 'udmember_profile_sync_off']);
        $connection->addColumn($registrationTable, 'udmember_allow_microsite', ['TYPE' => Table::TYPE_SMALLINT, 'nullable' => true, 'default'=>1,'COMMENT' => 'udmember_allow_microsite']);


        $memberTable = $installer->getTable('udmember_membership');
        $table = $connection->newTable($memberTable)
            ->addColumn('membership_id', Table::TYPE_INTEGER, 10, [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true
            ])
            ->addColumn('membership_code', Table::TYPE_TEXT, 64, ['nullable' => true])
            ->addColumn('membership_title', Table::TYPE_TEXT, 128, ['nullable' => true])
            ->addColumn('membership_sku', Table::TYPE_TEXT, 128, ['nullable' => true])
            ->addColumn('billing_type', Table::TYPE_TEXT, 64, ['nullable' => true])
            ->addColumn('limit_products', Table::TYPE_INTEGER, null, ['nullable' => true])
            ->addColumn('allow_microsite', Table::TYPE_SMALLINT, null, ['nullable' => true])
            ->addColumn('allow_registration', Table::TYPE_SMALLINT, null, ['nullable' => true])
            ->addColumn('sort_order', Table::TYPE_SMALLINT, null, ['unsigned' => true,'nullable' => true])
            ->addIndex(
                $installer->getIdxName(
                    $memberTable,
                    ['membership_code'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['membership_code'],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            )
            ->setComment('Vendor Membership Table')
            ->setOption('type', 'InnoDB')
            ->setOption('charset', 'utf8');
        $connection->createTable($table);

        $installer->endSetup();
    }
}