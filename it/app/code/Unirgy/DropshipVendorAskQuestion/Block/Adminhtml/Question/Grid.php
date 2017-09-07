<?php

namespace Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as WidgetGrid;
use Magento\Backend\Helper\Data as HelperData;
use Magento\Framework\Registry;
use Unirgy\DropshipVendorAskQuestion\Model\QuestionFactory;
use Unirgy\DropshipVendorAskQuestion\Model\ResourceModel\Question\Collection;
use Unirgy\DropshipVendorAskQuestion\Model\Source;
use Unirgy\Dropship\Model\Source as ModelSource;

class Grid extends WidgetGrid
{
    /**
     * @var QuestionFactory
     */
    protected $_questionFactory;

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Source
     */
    protected $_qaSrc;

    /**
     * @var ModelSource
     */
    protected $_src;

    public function __construct(Context $context, 
        HelperData $backendHelper, 
        QuestionFactory $modelQuestionFactory, 
        Registry $frameworkRegistry,
        Source $modelSource, 
        ModelSource $dropshipModelSource, 
        array $data = [])
    {
        $this->_questionFactory = $modelQuestionFactory;
        $this->_coreRegistry = $frameworkRegistry;
        $this->_qaSrc = $modelSource;
        $this->_src = $dropshipModelSource;

        parent::__construct($context, $backendHelper, $data);
        $this->setId('questionGrid');
        $this->setDefaultSort('created_at');
    }

    protected function _prepareCollection()
    {
        $collection = $this->_questionFactory->create()->getCollection();
        $collection->joinShipments()->joinProducts()->joinVendors();

        if ($this->getVendorId() || $this->getRequest()->getParam('vendorId', false)) {
            $this->setVendorId(($this->getVendorId() ? $this->getVendorId() : $this->getRequest()->getParam('vendorId')));
            $collection->addVendorFilter($this->getVendorId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $this->setCustomerId(($this->getCustomerId() ? $this->getCustomerId() : $this->getRequest()->getParam('customerId')));
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            $collection->addPendingStatusFilter();
        }

        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        $id = $column->getId();
        $value = $column->getFilter()->getValue();
        switch ($id) {
            case 'vendor_id':
                $this->getCollection()->addVendorFilter($value);
                return $this;
            case 'context':
                $this->getCollection()->addContextFilter($value);
                return $this;
        }
        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    protected function _prepareColumns()
    {
        $statuses = $this->_qaSrc
            ->setPath('statuses')
            ->toOptionHash();

        $prefix = $this->uIsMassactionAvailable() ? '' : 'udquestion_grid_';

        $this->addColumn($prefix.'question_id', [
            'header'        => __('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'index'         => 'question_id',
        ]);

        $this->addColumn($prefix.'question_date', [
            'header'        => __('Question Date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'index'         => 'question_date',
        ]);

        $this->addColumn($prefix.'question_status', [
            'header'        => __('Question Status'),
            'align'         => 'left',
            'type'          => 'options',
            'options'       => $statuses,
            'width'         => '100px',
            'index'         => 'question_status',
        ]);

        if (!$this->getCustomerId()) {
        $this->addColumn($prefix.'customer_name', [
            'header'        => __('Customer Name'),
            'align'         => 'left',
            'width'         => '100px',
            'index'         => 'customer_name',
            'filter_index'  => 'main_table.customer_name',
            'renderer'      => '\Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\GridRenderer\CustomerName',
            'format'        => sprintf('<a href="%sid/$customer_id/">$customer_name</a>', $this->getUrl('customer/index/edit'))
        ]);
        }

        $this->addColumn($prefix.'question_text', [
            'header'        => __('Question Text'),
            'align'         => 'left',
            'index'         => 'question_text',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ]);

        if (!$this->getVendorId()) {
        $this->addColumn($prefix.'vendor_id', [
            'header'        => __('Vendor'),
            'align'         => 'left',
            'width'         => '100px',
            'options'       => $this->_src->setPath('vendors')->toOptionHash(),
            'index'         => 'vendor_id',
            'filter'        => '\Unirgy\Dropship\Block\Vendor\GridColumnFilter',
            'format'        => sprintf('<a onclick="this.target=\'blank\'" href="%sid/$vendor_id/">$vendor_name</a>', $this->getUrl('udropship/vendor/edit'))
        ]);
        }

        $this->addColumn($prefix.'answer_date', [
            'header'        => __('Answer Date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'index'         => 'answer_date',
        ]);

        $this->addColumn($prefix.'answer_status', [
            'header'        => __('Answer Status'),
            'align'         => 'left',
            'type'          => 'options',
            'options'       => $statuses,
            'width'         => '100px',
            'index'         => 'answer_status',
        ]);

        $this->addColumn($prefix.'answer_text', [
            'header'        => __('Answer Text'),
            'align'         => 'left',
            'index'         => 'answer_text',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ]);

        $this->addColumn($prefix.'context', [
            'header'        => __('Context'),
            'align'         => 'left',
            'index'         => 'increment_id',
            'renderer'      => '\Unirgy\DropshipVendorAskQuestion\Block\Adminhtml\Question\GridRenderer\Context',
        ]);

        $this->addColumn($prefix.'action',
            [
                'header'    => __('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => [
                    [
                        'caption' => __('Edit'),
                        'url'     => [
                            'base'=>'udqa/index/edit',
                            'params'=> [
                                'vendorId' => $this->getVendorId(),
                                'customerId' => $this->getCustomerId(),
                                'ret'       => ( $this->_coreRegistry->registry('usePendingFilter') ) ? 'pending' : null
                            ]
                         ],
                         'field'   => 'id'
                    ]
                ],
                'filter'    => false,
                'sortable'  => false
        ]);

        return parent::_prepareColumns();
    }

    protected $_uIsMassactionAvailable = true;
    public function uIsMassactionAvailable($flag=null)
    {
        $result = $this->_uIsMassactionAvailable;
        if (null !== $flag) {
            $this->_uIsMassactionAvailable = $flag;
        }
        return $result;
    }
    public function setUisMassactionAvailable($flag)
    {
        $this->uIsMassactionAvailable($flag);
        return $this;
    }
    protected function _prepareMassaction()
    {
        if ($this->uIsMassactionAvailable()) {
            $this->setMassactionIdField('question_id');
            $this->setMassactionIdFieldOnlyIndexValue(true);
            $this->getMassactionBlock()->setFormFieldName('questions');

            $this->getMassactionBlock()->addItem('delete', [
                'label'=> __('Delete'),
                'url'  => $this->getUrl('udqa/index/massDelete', ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']),
                'confirm' => __('Are you sure?')
            ]);

            $statuses = $this->_qaSrc
                ->setPath('statuses')
                ->toOptionArray();
            array_unshift($statuses, ['label'=>'', 'value'=>'']);
            $this->getMassactionBlock()->addItem('update_question_status', [
                'label'         => __('Update Question Status'),
                'url'           => $this->getUrl('udqa/index/massUpdateQuestionStatus', ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']),
                'additional'    => [
                    'status'    => [
                        'name'      => 'status',
                        'type'      => 'select',
                        'class'     => 'required-entry',
                        'label'     => __('Status'),
                        'values'    => $statuses
                    ]
                ]
            ]);
            $this->getMassactionBlock()->addItem('update_answer_status', [
                'label'         => __('Update Answer Status'),
                'url'           => $this->getUrl('udqa/index/massUpdateAnswerStatus', ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']),
                'additional'    => [
                    'status'    => [
                        'name'      => 'status',
                        'type'      => 'select',
                        'class'     => 'required-entry',
                        'label'     => __('Status'),
                        'values'    => $statuses
                    ]
                ]
            ]);
            $this->getMassactionBlock()->addItem('send_customer_notification', [
                'label'         => __('Send Customer Notification'),
                'url'           => $this->getUrl('udqa/index/massSendCustomer', ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']),
            ]);
            $this->getMassactionBlock()->addItem('send_vendor_notification', [
                'label'         => __('Send Vendor Notification'),
                'url'           => $this->getUrl('udqa/index/massSendVendor', ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']),
            ]);
        }
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('udqa/index/edit', [
            'id' => $row->getId(),
            'vendorId' => $this->getVendorId(),
            'customerId' => $this->getCustomerId(),
            'ret'       => ( $this->_coreRegistry->registry('usePendingFilter') ) ? 'pending' : null,
        ]);
    }

    public function getGridUrl()
    {
        if( $this->getVendorId() || $this->getCustomerId() ) {
            if ($this->uIsMassactionAvailable()) {
                return $this->getUrl('udqa/index/' . ($this->_coreRegistry->registry('usePendingFilter') ? 'pending' : ''), [
                    'vendorId' => $this->getVendorId(),
                    'customerId' => $this->getCustomerId(),
                ]);
            } elseif ($this->getVendorId()) {
                return $this->getUrl('udqa/index/VendorQuestions', [
                    'vendorId' => $this->getVendorId(),
                ]);
            } else {
                return $this->getUrl('udqa/index/CustomerQuestions', [
                    'customerId' => $this->getCustomerId(),
                ]);
            }
        } else {
            return $this->getCurrentUrl();
        }
    }
}
