<?php

namespace Cibilia\Cibilians\Setup;

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

        $table = $installer->getConnection()->newTable(
            $installer->getTable('cibilian_referrals')
        )->addColumn(
            'referral_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true, 'nullable' => false, 'primary' => true),
            'Referral ID'
		 )->addColumn(
            'referred_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('nullable' => false),
            'Referred By'	
        )->addColumn(
            'company_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Company Name'
        )->addColumn(
            'website',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Website'
        )->addColumn(
            'contact_person',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Contact Person'
        )->addColumn(
            'email_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Email'	
        )->addColumn(
            'mobile',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => false),
            'Mobile'
        )->addColumn(
            'token',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            array('nullable' => true),
            'Token'			
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            array(),
            'Status'
        )->addColumn(
            'referral_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            array(),
            'Referral Type'			
        )->addColumn(
            'created_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            array(),
            'Creation Date'
        )->addColumn(
            'updated_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            array(),
            'Modification Date'
        )->addColumn(
            'published_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            array(),
            'Published on'			
        )->setComment(
            'Referral Requests'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
