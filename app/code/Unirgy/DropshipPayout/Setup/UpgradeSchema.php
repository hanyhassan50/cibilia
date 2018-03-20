<?php

namespace Unirgy\DropshipPayout\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    const LONGTEXT_SIZE=4294967295;
    const MEDIUMTEXT_SIZE=16777216;
    const TEXT_SIZE=65536;

    protected $_hlp;
    protected $_moduleManager;
    public function __construct(
        \Unirgy\Dropship\Helper\Data $udropshipHelper,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->_hlp = $udropshipHelper;
        $this->_moduleManager = $moduleManager;
    }
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $installer = $setup;
        $connection = $setup->getConnection();
        if (version_compare($context->getVersion(), '3.1.7', '<')) {
            $payoutTable = $setup->getTable('udropship_payout');
            $connection->addColumn($payoutTable, 'total_refund', ['TYPE'=>Table::TYPE_DECIMAL,'nullable' => false,'LENGTH'=>'12,4','COMMENT'=>'total_refund']);
            $connection->addColumn($payoutTable, 'total_reversed', ['TYPE'=>Table::TYPE_DECIMAL,'nullable' => false,'LENGTH'=>'12,4','COMMENT'=>'total_reversed']);
            $connection->addColumn($payoutTable, 'refund_due', ['TYPE'=>Table::TYPE_DECIMAL,'nullable' => false,'LENGTH'=>'12,4','COMMENT'=>'refund_due']);

            $refundRowTable = $setup->getTable('udropship_payout_refund_row');
            $table = $connection->newTable($refundRowTable)
                ->addColumn('row_id', Table::TYPE_INTEGER, 10, [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ])
                ->addColumn('payout_id', Table::TYPE_INTEGER, 10, ['unsigned' => true,'nullable' => false])
                ->addColumn('refund_id', Table::TYPE_INTEGER, 10, ['unsigned' => true,'nullable' => false])
                ->addColumn('order_id', Table::TYPE_INTEGER, 10, ['unsigned' => true,'nullable' => false])
                ->addColumn('po_id', Table::TYPE_INTEGER, 10, ['unsigned' => true,'nullable' => false])
                ->addColumn('po_type', Table::TYPE_TEXT, 32, ['nullable' => false, 'default'=>'shipment'])
                ->addColumn('refund_increment_id', Table::TYPE_TEXT, 50, ['nullable' => false])
                ->addColumn('order_increment_id', Table::TYPE_TEXT, 50, ['nullable' => false])
                ->addColumn('po_increment_id', Table::TYPE_TEXT, 50, ['nullable' => false])
                ->addColumn('refund_created_at', Table::TYPE_DATETIME, null, ['nullable' => false])
                ->addColumn('order_created_at', Table::TYPE_DATETIME, null, ['nullable' => false])
                ->addColumn('po_created_at', Table::TYPE_DATETIME, null, ['nullable' => false])
                ->addColumn('total_refund', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('total_payment', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('total_invoice', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('subtotal', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('shipping', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('discount', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('tax', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('hidden_tax', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('handling', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('trans_fee', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('com_amount', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('adj_amount', Table::TYPE_DECIMAL, [12,4], ['nullable' => false])
                ->addColumn('has_error', Table::TYPE_SMALLINT, null, ['nullable' => false])
                ->addColumn('error_info', Table::TYPE_TEXT, self::TEXT_SIZE, ['nullable' => false])
                ->addColumn('row_json', Table::TYPE_TEXT, self::TEXT_SIZE, ['nullable' => false])
                ->addColumn('reversed', Table::TYPE_SMALLINT, null, ['nullable' => false])
                ->addIndex(
                    $setup->getIdxName(
                        $refundRowTable,
                        ['refund_id','po_id','po_type','payout_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['refund_id','po_id','po_type','payout_id'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $setup->getIdxName($refundRowTable, ['payout_id']),
                    ['payout_id']
                )
                ->addForeignKey(
                    $setup->getFkName($refundRowTable, 'payout_id', 'udropship_payout', 'payout_id'),
                    'payout_id',
                    $setup->getTable('udropship_payout'),
                    'payout_id',
                    Table::ACTION_CASCADE
                )
                ->setComment('Vendor Payout Refund Row Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $connection->createTable($table);
            if ((bool)$this->_moduleManager->isEnabled('Unirgy_DropshipTierCommission')) {
                $connection->dropIndex($refundRowTable, $installer->getIdxName(
                    $refundRowTable,
                    ['refund_id','po_id','po_type','payout_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ));
                $connection->addColumn($refundRowTable, 'po_item_id', ['TYPE' => Table::TYPE_INTEGER, 'nullable' => true, 'unsigned' => true, 'COMMENT' => 'po_item_id']);
                $connection->addColumn($refundRowTable, 'refund_item_id', ['TYPE' => Table::TYPE_INTEGER, 'nullable' => true, 'unsigned' => true, 'COMMENT' => 'refund_item_id']);
                $connection->addColumn($refundRowTable, 'sku', ['TYPE'=>Table::TYPE_TEXT,'LENGTH'=>128,'nullable' => true,'COMMENT'=>'sku']);
                $connection->addColumn($refundRowTable, 'simple_sku', ['TYPE'=>Table::TYPE_TEXT,'LENGTH'=>128,'nullable' => true,'COMMENT'=>'simple_sku']);
                $connection->addColumn($refundRowTable, 'vendor_sku', ['TYPE'=>Table::TYPE_TEXT,'LENGTH'=>128,'nullable' => true,'COMMENT'=>'vendor_sku']);
                $connection->addColumn($refundRowTable, 'vendor_simple_sku', ['TYPE'=>Table::TYPE_TEXT,'LENGTH'=>128,'nullable' => true,'COMMENT'=>'vendor_simple_sku']);
                $connection->addColumn($refundRowTable, 'product', ['TYPE'=>Table::TYPE_TEXT,'LENGTH'=>255,'nullable' => true,'COMMENT'=>'product']);
                $connection->addIndex(
                    $refundRowTable,
                    $installer->getIdxName(
                        $refundRowTable,
                        ['refund_id','po_id','po_type','payout_id','po_item_id'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['refund_id','po_id','po_type','payout_id','po_item_id'],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                );
            }
        }

        $setup->endSetup();
    }
}
