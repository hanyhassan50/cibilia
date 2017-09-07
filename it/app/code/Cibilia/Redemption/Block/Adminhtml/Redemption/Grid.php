<?php
namespace Cibilia\Redemption\Block\Adminhtml\Redemption;

/**
 * Adminhtml Redemption grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Cibilia\Redemption\Model\Redemption
     */
    protected $_redemption;

    protected $_request;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Cibilia\Redemption\Model\Redemption $redemptionPage
     * @param \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $collectionFactory
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Cibilia\Redemption\Model\Redemption $redemption,
        \Cibilia\Redemption\Model\ResourceModel\Redemption\CollectionFactory $collectionFactory,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_redemption = $redemption;
        $this->_request = $request;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('redemptionGrid');
        $this->setDefaultSort('redemption_id');
        $this->setDefaultDir('DESC');

        $filterCibilian = $this->getRequest()->getParam('cibilian_id');
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
        /* @var $collection \Cibilia\Redemption\Model\ResourceModel\Redemption\Collection */
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
        $this->addColumn('redemption_id', [
            'header'    => __('ID'),
            'index'     => 'redemption_id',
        ]);
        
        $this->addColumn('cibilian_id', [
            'header' => __('Cibilian'),
            'index' => 'cibilian_id',
            'renderer' => 'Cibilia\Redemption\Block\Adminhtml\Redemption\Grid\Renderer\Cibilian',
            'filter_condition_callback' => array($this, '_filterCibilianCallback')
        ]);


        $this->addColumn('order_id', ['header' => __('Order Id'), 'index' => 'order_id']);


        $withdrawType = ['1' => __('Bank Transfer'), '2' => __('Paypal')];
        $this->addColumn(
            'withdrawal_type',
            [
                'header' => __('Withdrawal Type'),
                'index' => 'withdrawal_type',
                'type' => 'options',
                'options' => $withdrawType,
                'class' => 'status'
            ]
        );

        $this->addColumn('amount', [
            'header' => __('Total Commission'),
            'index' => 'amount',
            'type' => 'currency'
        ]);
        $this->addColumn('email_bank_details', ['header' => __('Details'), 'index' => 'email_bank_details']);
        $this->addColumn('comment', ['header' => __('Comments'), 'index' => 'comment']);
        $this->addColumn('transaction_id', ['header' => __('Transaction Id'), 'index' => 'transaction_id']);
        
        $status = ['1' => __('Paid'), '2' => __('Unpaid'),'3' => __('Canceled')];
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
                'type' => 'datetime',
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
                        'field' => 'redemption_id'
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
        return $this->getUrl('*/*/edit', ['redemption_id' => $row->getId()]);
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
