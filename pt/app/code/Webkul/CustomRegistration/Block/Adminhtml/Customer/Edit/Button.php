<?php
namespace Webkul\CustomRegistration\Block\Adminhtml\Customer\Edit;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_dob=null;

	 const BUTTON_TEMPLATE = 'customfields/customer/button.phtml';

     /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
            \Magento\Framework\Registry $registry,
            \Magento\Backend\Block\Widget\Context $context,
            array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
     
	 /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::BUTTON_TEMPLATE);
        }
        return $this;
    }

    public function getDobFields(){
        if(!$this->_dob){
            return json_encode( $this->_coreRegistry->registry('dobField'));
        }
        return json_encode($this->_dob);
    }

}