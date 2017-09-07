<?php


namespace Unirgy\DropshipVendorMembership\Model\SystemConfig\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Unirgy\DropshipVendorMembership\Model\MembershipFactory;

class Membership extends Value
{
    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;
    protected $_hlp;

    public function __construct(
        \Unirgy\Dropship\Helper\Data $udropshipHelper,
        MembershipFactory $modelMembershipFactory,
        Context $context,
        Registry $registry, 
        ScopeConfigInterface $config, 
        TypeListInterface $cacheTypeList, 
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null, 
        array $data = [])
    {
        $this->_hlp = $udropshipHelper;
        $this->_membershipFactory = $modelMembershipFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function setValue($value)
    {
        if (is_array($value)) {
            unset($value['$ROW']);
        }
        $this->setData('value', $value);
        return $this;
    }
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['$ROW']);
            $rHlp = $this->_hlp->rHlp();
            $conn = $rHlp->getConnection();
            $dtTable = $rHlp->getTable('udmember_membership');
            $fieldsData = $rHlp->myPrepareDataForTable($dtTable, [], true);
            $fields = array_keys($fieldsData);
            $existing = $rHlp->loadDbColumns($this->_membershipFactory->create(), true, $fields);
            $insert = [];
            foreach ($value as $v) {
                if (empty($v['membership_title'])) continue;
                if (!empty($v['membership_id'])) {
                    unset($existing[$v['membership_id']]);
                } else {
                    $v['membership_id'] = null;
                }
                $insert[] = $rHlp->myPrepareDataForTable($dtTable, $v, true);
            }
            if (!empty($insert)) {
                $rHlp->multiInsertOnDuplicate($dtTable, $insert);
            }
            if (!empty($existing)) {
                $conn->delete($dtTable, ['membership_id in (?)'=>array_keys($existing)]);
            }
        }
        $this->setValue('');
        return parent::beforeSave();
    }

    protected function _afterLoad()
    {
        $rHlp = $this->_hlp->rHlp();
        $conn = $rHlp->getConnection();
        $dtTable = $rHlp->getTable('udmember_membership');
        $fieldsData = $rHlp->myPrepareDataForTable('udmember_membership', [], true);
        $fields = array_keys($fieldsData);
        $existing = $rHlp->loadDbColumns($this->_membershipFactory->create(), true, $fields);
        usort($existing, [$this, 'sortBySortOrder']);
        $this->setValue($existing);
    }

    public function sortBySortOrder($a, $b)
    {
        if (@$a['sort_order']<@$b['sort_order']) {
            return -1;
        } elseif (@$a['sort_order']>@$b['sort_order']) {
            return 1;
        }
        return 0;
    }
}
