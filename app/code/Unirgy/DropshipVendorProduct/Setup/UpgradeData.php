<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    \Unirgy\Dropship
 * @copyright  Copyright (c) 2015-2016 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\DropshipVendorProduct\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    const MEDIUMTEXT_SIZE=16777216;
    const TEXT_SIZE=65536;
    protected $categorySetupFactory;

    public function __construct(
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Catalog\Setup\CategorySetup $catalogSetup */
        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
        if (version_compare($context->getVersion(), '3.1.28', '<')) {
            foreach (['udprod_attributes_changed', 'udprod_cfg_simples_added', 'udprod_cfg_simples_removed'] as $attributeCode) {
                $catalogSetup->updateAttribute(
                    \Magento\Catalog\Model\Product::ENTITY,
                    $attributeCode,
                    'backend_model',
                    '\Unirgy\DropshipVendorProduct\Model\ProductAttributeBackend\Serialized'
                );
            }
        }
    }
}