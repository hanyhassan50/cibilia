<?php

namespace Unirgy\DropshipVendorPromotions\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Helper\Data as HelperData;
use Magento\CatalogRule\Block\Adminhtml\Promo\Widget\Chooser\Sku;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory;
use Magento\Framework\App\ObjectManager;

class PromoWidgetChooserSku extends Sku
{
    /**
     * @var Collection
     */
    protected $_productCollection;

    protected $_template = 'Unirgy_DropshipVendorPromotions::widget/grid/extended.phtml';

    public function __construct(Context $context, 
        HelperData $backendHelper, 
        CollectionFactory $eavAttSetCollection, 
        ProductCollectionFactory $cpCollection, 
        Type $catalogType, 
        Collection $productCollection, 
        array $data = [])
    {
        $this->_productCollection = $productCollection;

        parent::__construct($context, $backendHelper, $eavAttSetCollection, $cpCollection, $catalogType, $data);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_productCollection
            ->setStoreId(0)
            ->addAttributeToSelect('name', 'type_id', 'attribute_set_id')
            ->addAttributeToFilter('udropship_vendor', ObjectManager::getInstance()->get('Unirgy\Dropship\Model\Session')->getVendorId());

        $this->setCollection($collection);

        return Grid::_prepareCollection();
    }
    public function getGridUrl()
    {
        return $this->getUrl(
            'udpromo/*/chooser',
            ['_current' => true, 'current_grid_id' => $this->getId(), 'collapse' => null]
        );
    }
    protected function _prepareLayout()
    {
        $this->setChild(
            'export_button',
            $this->getLayout()->createBlock('Unirgy\DropshipVendorPromotions\Block\Widget\Button')->setData(
                [
                    'label' => __('Export'),
                    'onclick' => $this->getJsObjectName() . '.doExport()',
                    'class' => 'task',
                ]
            )
        );
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock('Unirgy\DropshipVendorPromotions\Block\Widget\Button')->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary'
                ]
            )->setDataAttribute(
                [
                    'action' => 'grid-filter-reset'
                ]
            )
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock('Unirgy\DropshipVendorPromotions\Block\Widget\Button')->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'task action-secondary',
                ]
            )->setDataAttribute(
                [
                    'action' => 'grid-filter-apply'
                ]
            )
        );
        return \Magento\Backend\Block\Widget\Grid::_prepareLayout();
    }
    protected function _prepareFilterButtons()
    {
        $this->setChild(
            'reset_filter_button',
            $this->getLayout()->createBlock(
                'Unirgy\DropshipVendorPromotions\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Reset Filter'),
                    'onclick' => $this->getJsObjectName() . '.resetFilter()',
                    'class' => 'action-reset action-tertiary'
                ]
            )->setDataAttribute(['action' => 'grid-filter-reset'])
        );
        $this->setChild(
            'search_button',
            $this->getLayout()->createBlock(
                'Unirgy\DropshipVendorPromotions\Block\Widget\Button'
            )->setData(
                [
                    'label' => __('Search'),
                    'onclick' => $this->getJsObjectName() . '.doFilter()',
                    'class' => 'action-secondary',
                ]
            )->setDataAttribute(['action' => 'grid-filter-apply'])
        );
    }
}