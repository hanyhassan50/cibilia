<?php

namespace Unirgy\Dropship\Model\ResourceModel\Indexer;

use \Magento\Eav\Model\Config;
use \Magento\Framework\Indexer\Table\StrategyInterface;
use \Magento\Framework\Model\ResourceModel\Db\Context;
use \Magento\Indexer\Model\ResourceModel\AbstractResource;

class VendorProductAssoc extends AbstractResource
{
    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var \Unirgy\Dropship\Helper\Data
     */
    protected $_hlp;

    public function __construct(
        Config $eavConfig,
        \Unirgy\Dropship\Helper\Data $helper,
        Context $context,
        StrategyInterface $tableStrategy
    )
    {
        $this->_eavConfig = $eavConfig;
        $this->_hlp = $helper;

        parent::__construct($context, $tableStrategy);
    }

    protected function _construct()
    {
        $this->_setResource('udropship');
    }

    public function isInstalled()
    {
        /** @var \Magento\Framework\App\DeploymentConfig $deployment */
        $deployment = $this->_hlp->getObj('Magento\Framework\App\DeploymentConfig');
        return $deployment->isAvailable();
    }

    public function reindexProducts($entityIds=null)
    {
        if (!$this->isInstalled()) return false;

        if (empty($entityIds)) return false;

        $conn = $this->getConnection();

        $vendorAttr = $this->_eavConfig->getAttribute('catalog_product', 'udropship_vendor');

        $conn->delete($this->getTable('udropship_vendor_product_assoc'), array('product_id in (?)' => $entityIds));

        $select = $conn->select()
            ->from(array('vid' => $vendorAttr->getBackend()->getTable()), array())
            ->join(
                array('v'=>$this->getTable('udropship_vendor')),
                'v.vendor_id=vid.value',
                array()
            )
            ->join(
                array('pid'=>$this->getTable('catalog_product_entity')),
                'pid.'.$this->_hlp->rowIdField().'=vid.'.$this->_hlp->rowIdField(),
                array()
            )
            ->where('vid.attribute_id=?', $vendorAttr->getId())
            ->where('vid.'.$this->_hlp->rowIdField().' in (?)', $entityIds);

        $select->columns(array(
            'vid.value', 'vid.'.$this->_hlp->rowIdField(),
            new \Zend_Db_Expr('1'),
            new \Zend_Db_Expr('0'),
        ));

        $insertSelect = sprintf("INSERT INTO %s (vendor_id,product_id,is_attribute,is_udmulti) %s "
            ." ON DUPLICATE KEY UPDATE is_attribute=values(is_attribute),is_udmulti=values(is_udmulti)",
            $this->getTable('udropship_vendor_product_assoc'), $select
        );
        $conn->query($insertSelect);

        $select = $conn->select()
            ->from(array('vid' => $this->getTable('udropship_vendor_product')), array())
            ->join(
                array('v'=>$this->getTable('udropship_vendor')),
                'v.vendor_id=vid.vendor_id',
                array()
            )
            ->join(
                array('pid'=>$this->getTable('catalog_product_entity')),
                'pid.entity_id=vid.product_id',
                array()
            )
            ->where('vid.product_id in (?)', $entityIds);

        $select->columns(array(
            'vid.vendor_id', 'vid.product_id',
            new \Zend_Db_Expr('0'),
            new \Zend_Db_Expr('1'),
        ));

        $insertSelect = sprintf("INSERT INTO %s (vendor_id,product_id,is_attribute,is_udmulti) %s "
            ." ON DUPLICATE KEY UPDATE is_attribute=is_attribute,is_udmulti=values(is_udmulti)",
            $this->getTable('udropship_vendor_product_assoc'), $select
        );
        $conn->query($insertSelect);

        return $this;
    }

    public function reindexVendors($entityIds=null)
    {
        if (!$this->isInstalled()) return false;

        if (empty($entityIds)) return false;

        $conn = $this->getConnection();

        $vendorAttr = $this->_eavConfig->getAttribute('catalog_product', 'udropship_vendor');

        $conn->delete($this->getTable('udropship_vendor_product_assoc'), array('vendor_id in (?)' => $entityIds));

        $select = $conn->select()
            ->from(array('vid' => $vendorAttr->getBackend()->getTable()), array())
            ->join(
                array('v'=>$this->getTable('udropship_vendor')),
                'v.vendor_id=vid.value',
                array()
            )
            ->where('vid.attribute_id=?', $vendorAttr->getId())
            ->where('vid.value in (?)', $entityIds);

        $select->columns(array(
            'vid.value', 'vid.'.$this->_hlp->rowIdField(),
            new \Zend_Db_Expr('1'),
            new \Zend_Db_Expr('0'),
        ));

        $insertSelect = sprintf("INSERT INTO %s (vendor_id,product_id,is_attribute,is_udmulti) %s "
            ." ON DUPLICATE KEY UPDATE is_attribute=values(is_attribute),is_udmulti=values(is_udmulti)",
            $this->getTable('udropship_vendor_product_assoc'), $select
        );
        $conn->query($insertSelect);

        $select = $conn->select()
            ->from(array('vid' => $this->getTable('udropship_vendor_product')), array())
            ->join(
                array('v'=>$this->getTable('udropship_vendor')),
                'v.vendor_id=vid.vendor_id',
                array()
            )
            ->join(
                array('pid'=>$this->getTable('catalog_product_entity')),
                'pid.entity_id=vid.product_id',
                array()
            )
            ->where('vid.vendor_id in (?)', $entityIds);

        $select->columns(array(
            'vid.vendor_id', 'vid.product_id',
            new \Zend_Db_Expr('0'),
            new \Zend_Db_Expr('1'),
        ));

        $insertSelect = sprintf("INSERT INTO %s (vendor_id,product_id,is_attribute,is_udmulti) %s "
            ." ON DUPLICATE KEY UPDATE is_attribute=is_attribute,is_udmulti=values(is_udmulti)",
            $this->getTable('udropship_vendor_product_assoc'), $select
        );
        $conn->query($insertSelect);

        return $this;
    }

    public function reindexAll()
    {
        if (!$this->isInstalled()) return false;
        $conn = $this->getConnection();
        $conn->query(sprintf('truncate %s', $this->getTable('udropship_vendor_product_assoc')));
        $vSelect = $conn->select()
            ->from(array('v' => $this->getTable('udropship_vendor')), array('vendor_id'));
        $vIds = $conn->fetchCol($vSelect);
        $this->reindexVendors($vIds);
        return $this;
    }

    public function disableTableKeys()
    {
        return $this;
    }

    public function enableTableKeys()
    {
        return $this;
    }

}