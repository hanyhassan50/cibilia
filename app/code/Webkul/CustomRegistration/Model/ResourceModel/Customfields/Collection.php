<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\CustomRegistration\Model\ResourceModel\Customfields;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface     $entityFactory
     * @param \Psr\Log\LoggerInterface                                      $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface  $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                     $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                    $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null           $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null     $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Webkul\CustomRegistration\Model\Customfields',
            'Webkul\CustomRegistration\Model\ResourceModel\Customfields'
        );
        // $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }

    /**
    * Create all ids retrieving select with limitation
    *
    * @param int $limit
    * @param int $offset
    * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
    */
   protected function _getAllIdsSelect($limit = null, $offset = null)
   {
       $idsSelect = clone $this->getSelect();
       $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
       $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
       $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
       $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
       $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
       $idsSelect->limit($limit, $offset);
       return $idsSelect;
   }

}
