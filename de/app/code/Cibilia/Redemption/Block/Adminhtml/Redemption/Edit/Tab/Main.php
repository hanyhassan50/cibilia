<?php
namespace Cibilia\Redemption\Block\Adminhtml\Redemption\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('redemption');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Cibilia_Redemption::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('redemption_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Redemption Information')]);

        if ($model->getId()) {
            $fieldset->addField('redemption_id', 'hidden', ['name' => 'redemption_id']);
        }

        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'amount',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'required' => true,
                'disabled' => true
            ]
        );

        $withdrawType = ['1' => __('Bank Transfer'), '2' => __('Paypal')];
        $fieldset->addField(
            'withdrawal_type',
            'select',
            [
                'name' => 'withdrawal_type',
                'label' => __('Withdrawal Type'),
                'title' => __('Author'),
                'options' => $withdrawType,
                'required' => true,
                'disabled' => true
            ]
        );

         $fieldset->addField(
            'comment',
            'textarea',
            [
                'name' => 'comment',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'required' => true,
                'disabled' => false
            ]
        );

          $fieldset->addField(
            'transaction_id',
            'text',
            [
                'name' => 'transaction_id',
                'label' => __('Transcation Id'),
                'title' => __('Transcation Id'),
                'required' => true,
                'disabled' => false
            ]
        );

        $status = ['1' => __('Paid'), '2' => __('Unpaid'),'3'=>'Cancled'];
        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'options' => $status,
                'required' => true,
                'disabled' => false
            ]
        );
        
        /*$dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField('published_at', 'date', [
            'name'     => 'published_at',
            'date_format' => $dateFormat,
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'value' => $model->getPublishedAt(),
            'label'    => __('Publishing Date'),
            'title'    => __('Publishing Date'),
            'required' => true
        ]);*/
        
        $this->_eventManager->dispatch('adminhtml_redemption_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Redemption Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Redemption Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
