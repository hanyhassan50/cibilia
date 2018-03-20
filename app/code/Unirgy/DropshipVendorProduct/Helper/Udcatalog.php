<?php

namespace Unirgy\DropshipVendorProduct\Helper;

use Magento\Catalog\Model\Product;
use Unirgy\Dropship\Helper\Catalog;

class Udcatalog extends Catalog
{
    /**
     * @return \Unirgy\DropshipVendorProduct\Helper\Data
     */
    protected function _prodHlp()
    {
        return $this->_hlp->getObj('Unirgy\DropshipVendorProduct\Helper\Data');
    }

    public function getIdentifyImageAttributes($prod, $isNew)
    {
        return $this->_prodHlp()->getIdentifyImageAttributes($prod, $isNew);
    }
    public function createCfgAttr($cfgProd, $cfgAttrId, $pos)
    {
        $cfgPid = $cfgProd;
        $identifyImage = 0;
        if ($cfgProd instanceof Product) {
            $cfgPid = $cfgProd->getId();
            $identifyImage = 0;
            foreach ($this->getIdentifyImageAttributes($cfgProd, true) as $cfgAttr) {
                if ($cfgAttr->getId() == $cfgAttrId) {
                    $identifyImage = 1;
                    break;
                }
            }
        }
        $res = $this->_hlp->rHlp();
        $write = $res->getConnection();
        $superAttrTable = $res->getTableName('catalog_product_super_attribute');
        $superLabelTable = $res->getTableName('catalog_product_super_attribute_label');

        $exists = $write->fetchRow("select sa.*, sal.value_id, sal.value label from {$superAttrTable} sa
            inner join {$superLabelTable} sal on sal.product_super_attribute_id=sa.product_super_attribute_id
            where sa.product_id={$cfgPid} and sa.attribute_id={$cfgAttrId} and sal.store_id=0");
        if (!$exists) {
            $write->insert($superAttrTable, [
                'product_id' => $cfgPid,
                'attribute_id' => $cfgAttrId,
                'position' => $pos,
                'identify_image' => $identifyImage
            ]);
            $saId = $write->lastInsertId($superAttrTable);
            $write->insert($superLabelTable, [
                'product_super_attribute_id' => $saId,
                'store_id' => 0,
                'use_default' => 1,
                'value' => '',
            ]);
        }

        return $this;
    }
}