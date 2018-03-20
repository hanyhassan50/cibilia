<?php

namespace Unirgy\DropshipVendorProduct\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $_hlp;
    public function __construct(
        \Unirgy\Dropship\Helper\Data $udropshipHelper
    ) {
        $this->_hlp = $udropshipHelper;
    }
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();
        if (version_compare($context->getVersion(), '3.1.22', '<')) {

            $saTable = $setup->getTable('catalog_product_super_attribute');
            $connection->addColumn($saTable, 'identify_image', ['TYPE'=>Table::TYPE_SMALLINT,'nullable' => false,'COMMENT'=>'identify_image']);
        }

        $setup->endSetup();
    }
}
