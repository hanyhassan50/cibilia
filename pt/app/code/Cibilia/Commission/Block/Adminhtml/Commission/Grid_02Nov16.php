<?php
namespace Cibilia\Commission\Block\Adminhtml\Commission;

/**
 * Adminhtml Commission grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Cibilia\Commission\Model\ResourceModel\Commission\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Cibilia\Commission\Model\Commission
     */
    protected $_commission;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cibilia\Commission\Model\Commission $commissionPage
     * @param \Cibilia\Commission\Model\ResourceModel\Commission\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cibilia\Commission\Model\Commission $commission,
        \Cibilia\Commission\Model\ResourceModel\Commission\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_commission = $commission;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('commissionGrid');
        $this->setDefaultSort('commission_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Cibilia\Commission\Model\ResourceModel\Commission\Collection */
        $this->setCollection($collection);

        /*$date = date('Y-m-d H:i:s');
        $lastUpdateDate = date('Y-m-d H:i:s', strtotime('-2 day'));

        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        
        $arrOrdersCollection = $objectManager->create('Magento\Sales\Model\Order')->getCollection();
        //$arrOrdersCollection->addAttributeToFilter('updated_at', array('to'=>$lastUpdateDate))
        //->addAttributeToFilter('status', array('eq' => 'complete'));

        $arrCibiliansTotals = array();
        $arrCibiliansIds = array();
        
        foreach ($arrOrdersCollection as $order) {
            foreach ($order->getAllVisibleItems() as $itemId => $item) {
                $ObjProduct = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
                if($ObjProduct->getUdropshipVendor()){
                    if(in_array($ObjProduct->getUdropshipVendor(), $arrCibiliansIds)){
                        $oldPrice = $arrCibiliansTotals[$order->getId()][$ObjProduct->getUdropshipVendor()];
                        $arrCibiliansTotals[$order->getId()][$ObjProduct->getUdropshipVendor()] = $oldPrice + $item->getPrice();
                    }else{
                        $arrCibiliansTotals[$order->getId()][$ObjProduct->getUdropshipVendor()] = $item->getPrice();
                        $arrCibiliansIds[] = $ObjProduct->getUdropshipVendor();
                    }    
                }
            }
        }

        echo "<pre>";
        print_r($arrCibiliansTotals);
        echo "</pre>";*/
        
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('commission_id', [
            'header'    => __('ID'),
            'index'     => 'commission_id',
        ]);
        
        $this->addColumn('cibilian_id', ['header' => __('Cibilian'), 'index' => 'cibilian_id']);
        $this->addColumn('order_id', ['header' => __('Order Id'), 'index' => 'order_id']);
        $this->addColumn('total_amount', ['header' => __('Total Order Amount'), 'index' => 'total_amount']);
        $this->addColumn('total_commission', ['header' => __('Total Commission'), 'index' => 'total_commission']);
        $this->addColumn('cibilia_commision_rate', ['header' => __('Commission rate'), 'index' => 'cibilia_commision_rate']);
        
        $status = $statuses = ['1' => __('Paid'), '0' => __('Unpaid')];
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $status,
                'class' => 'status'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'index' => 'updated_at',
                'type' => 'date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );
        
        $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'commission_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['commission_id' => $row->getId()]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
