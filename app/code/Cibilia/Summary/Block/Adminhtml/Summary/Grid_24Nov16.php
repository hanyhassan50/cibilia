<?php
namespace Cibilia\Summary\Block\Adminhtml\Summary;

/**
 * Adminhtml Summary grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Cibilia\Summary\Model\Summary
     */
    protected $_summary;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cibilia\Summary\Model\Summary $summaryPage
     * @param \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cibilia\Summary\Model\Summary $summary,
        \Cibilia\Summary\Model\ResourceModel\Summary\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_summary = $summary;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('summaryGrid');
        $this->setDefaultSort('summary_id');
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
        /* @var $collection \Cibilia\Summary\Model\ResourceModel\Summary\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('summary_id', [
            'header'    => __('ID'),
            'index'     => 'summary_id',
            'type' => 'number'
        ]);
        
        $this->addColumn('cibilian_id', [
            'header' => __('Cibilian'),
            'index' => 'cibilian_id',
            'renderer' => 'Cibilia\Commission\Block\Adminhtml\Commission\Grid\Renderer\Cibilian',
             'filter_condition_callback' => array($this, '_filterCibilianCallback')
        ]);

        $this->addColumn('total_amount', [
            'header' => __('Total Amount'),
            'index' => 'total_amount',
            'type' => 'currency'
        ]);

        $this->addColumn('paid_amount', [
            'header' => __('Paid Amount'),
            'index' => 'paid_amount',
            'type' => 'currency'
        ]);

         $this->addColumn('pending_amount', [
            'header' => __('Pending Amount'),
            'index' => 'pending_amount',
            'type' => 'currency'
        ]);
        
        //$this->addColumn('cibilia_commision_rate', ['header' => __('Commission rate'), 'index' => 'cibilia_commision_rate']);
        
        /*$status = $statuses = ['1' => __('Paid'), '2' => __('Unpaid')];
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $status,
                'class' => 'status'
            ]
        );*/

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
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );
        
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getCibilianId',
                'actions' => [
                    [
                        'caption' => __('Earned History'),
                        'url' => [
                            'base' => 'commission/index/index',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'cibilian_id'
                    ],
                    [
                        'caption' => __('Withdrawal History'),
                        'url' => [
                            'base' => 'redemption/index/index',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'cibilian_id'
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
        return '';
        //return $this->getUrl('*/*/edit', ['summary_id' => $row->getId()]);
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
    protected function _filterCibilianCallback($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $collection;
        }
        
        $collection->getSelect()->join('customer_grid_flat as customer','main_table.cibilian_id = customer.entity_id')->where("customer.name like '%".$value."%'");
        
        return $collection;
    }
}
