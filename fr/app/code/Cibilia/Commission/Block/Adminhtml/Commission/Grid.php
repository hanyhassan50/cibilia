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

    protected $_request;

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
        \Cibilia\Commission\Model\Commissions $commission,
        \Cibilia\Commission\Model\ResourceModel\Commissions\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_commission = $commission;
        $this->_request = $request;
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

        $filterCibilian = $this->getRequest()->getParam('cibilian_id');
        $lockedFilter = $this->getRequest()->getParam('lock');
        if($filterCibilian){

            $strCibilianName = $this->_getCibilianName($filterCibilian);
            $this->setDefaultFilter(array('cibilian_id' => $strCibilianName));
        }
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(false);


    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {   
       $collection = $this->_collectionFactory->create();
       if($this->getRequest()->getParam('lock')){

            $collection->addFieldToFilter('status',3);
        }else{
             $collection->addFieldToFilter('status',2);
        }
        /* @var $collection \Cibilia\Commission\Model\ResourceModel\Commission\Collection */
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
        $this->addColumn('commission_id', [
            'header'    => __('ID'),
            'index'     => 'commission_id',
             'type' => 'number'
        ]);
        
        $this->addColumn('cibilian_id', [
            'header' => __('Cibilian'),
            'index' => 'cibilian_id',
            'renderer' => 'Cibilia\Commission\Block\Adminhtml\Commission\Grid\Renderer\Cibilian',
            'filter_condition_callback' => array($this, '_filterCibilianCallback')
        ]);

        $this->addColumn('order_id', [
            'header' => __('Order Id'),
            'index' => 'order_id',
            'type' => 'number'
        ]);

        $this->addColumn('total_amount', [
            'header' => __('Total Order Amount'),
            'index' => 'total_amount',
            'type' => 'currency'
        ]);
        $this->addColumn('total_commission', [
            'header' => __('Total Commission'),
            'index' => 'total_commission',
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
        
       /* $this->addColumn(
            'action',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'edit',
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
        );*/

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
        return "";
        //return $this->getUrl('*/*/edit', ['commission_id' => $row->getId()]);
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
    protected function _getCibilianName($id){
        
        $srtName = '';

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objCibilian = $objectManager->create('Magento\Customer\Model\Customer')->load($id);
        if($objCibilian && $objCibilian->getId()){
            $srtName = $srtName = $objCibilian->getName();            
        }
        return $srtName; 
    }

    
}
