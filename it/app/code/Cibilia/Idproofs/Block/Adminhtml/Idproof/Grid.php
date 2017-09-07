<?php
namespace Cibilia\Idproofs\Block\Adminhtml\Idproof;


class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $_setsFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_type;
	
	protected $_idproofAttributeTab;

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_status;
	protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_visibility;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Type $type
     * @param \Magento\Catalog\Model\Product\Attribute\Source\Status $status
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
		\Cibilia\Idproofs\Model\ResourceModel\Idproof\Collection $collectionFactory,
		\Webkul\CustomRegistration\Block\Adminhtml\Customer\Edit\Tabs $objCustomerIdProofTab,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
		
		$this->_collectionFactory = $collectionFactory;
        $this->_websiteFactory = $websiteFactory;
		$this->_idproofAttributeTab = $objCustomerIdProofTab;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
		
        $this->setId('productGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
       
    }
	
	protected function fnGetApprovalStatus()
	{
		$objCustomAttribute = $this->_idproofAttributeTab->getCustomAttribute();
		$arrApprovalStatus = $arrNewApprovalStatus = array();
		if($objCustomAttribute->count())
		{
			foreach($objCustomAttribute as $objSpecCustomAttribute)
			{
				if($objSpecCustomAttribute->getAttributeCode() == 'approval_status')
				{
					$arrNewApprovalStatus = $objSpecCustomAttribute->getSource()->getAllOptions();
					break;
				}
			}
		}
		if(count($arrNewApprovalStatus))
		{
			foreach($arrNewApprovalStatus as $key => $arrSpecStatus)
			{
				$arrApprovalStatus[$arrSpecStatus['value']] = $arrSpecStatus['label'];
			}
		}
		return $arrApprovalStatus;
	}
		

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
		try{
			
			
			//$collection =$this->_collectionFactory->load();

		    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $arrCustomerCollection = $objectManager->create('Magento\Customer\Model\Customer')->getCollection()
            ->addAttributeToSelect('approval_status')
            ->addFieldToFilter('idproof', array('neq' => 'NULL' ));
                
            $this->setCollection($arrCustomerCollection);

			parent::_prepareCollection();
		  
			return $this;
		}
		catch(Exception $e)
		{
			echo $e->getMessage();die;
		}
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'catalog_product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		$this->addColumn(
            'firstname',
            [
                'header' => __('Name'),
                'index' => 'entity_id',
                'class' => 'firstname',
                'renderer' => 'Cibilia\Idproofs\Block\Adminhtml\Idproof\Grid\Renderer\Name',
                'filter_condition_callback' => [$this, '_productFilter'],
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
                'class' => 'email'
            ]
        );
		$arrApprovalStatus = $this->fnGetApprovalStatus();	
		
		$this->addColumn(
            'approval_status',
            [
                'header' => __('Approval Status'),
				'type' => 'options',
				'options' => $arrApprovalStatus,
                'index' => 'approval_status',
                'class' => 'approval_status'
            ]
        );
        $this->addColumn(
            'actions',
            [
                'header'    =>__('Action'),
                'width'     => '150px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   =>  [ 
                                    [
                                        'caption' => __('Identity Proof'),
                                        'url'     => array('base'=>'customer/index/edit/idproof/1'),
                                        'field'   => 'id'
                                    ]
                                ],
                                                                                           
                'filter'    => false,
                'sortable'  => false
            ]
        );

		/*{{CedAddGridColumn}}*/

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
    protected function _productFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
        
       $collection->addFieldToFilter(array(
                array('attribute'=>'firstname', array('like' => '%'.$value.'%')),
                array('attribute'=>'lastname',  array('like' => '%'.$value.'%'))));
       
       return $this;
    }

    
}
