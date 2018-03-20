<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product;

use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class VideoForm extends \Magento\Framework\View\Element\Template
{
    /**
     * Anchor is product video
     */
    const PATH_ANCHOR_PRODUCT_VIDEO = 'catalog_product_video-link';

    /**
     * @var \Magento\ProductVideo\Helper\Media
     */
    protected $mediaHelper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $_formFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $mathRandom;

    protected $_form;
    protected $_prodHlp;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\ProductVideo\Helper\Media $mediaHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Unirgy\DropshipVendorProduct\Helper\Data $vendorProductHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\ProductVideo\Helper\Media $mediaHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Math\Random $mathRandom,
        array $data = []
    ) {
        $this->_prodHlp = $vendorProductHelper;
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_formFactory = $formFactory;
        $this->mediaHelper = $mediaHelper;
        $this->urlBuilder = $context->getUrlBuilder();
        $this->jsonEncoder = $jsonEncoder;
        $this->mathRandom = $mathRandom;
        $this->setUseContainer(true);
    }

    protected $elementRenderer;
    protected $fieldsetRenderer;
    protected $fieldsetElementRenderer;
    protected function _prepareLayout()
    {
        $this->elementRenderer = $this->getLayout()->createBlock(
            'Unirgy\DropshipVendorProduct\Block\Vendor\Product\VideoForm\Renderer\Element',
            $this->getNameInLayout() . '_element'
        );
        $this->fieldsetRenderer = $this->getLayout()->createBlock(
            'Unirgy\DropshipVendorProduct\Block\Vendor\Product\VideoForm\Renderer\Fieldset',
            $this->getNameInLayout() . '_fieldset'
        );
        $this->fieldsetElementRenderer = $this->getLayout()->createBlock(
            'Unirgy\DropshipVendorProduct\Block\Vendor\Product\VideoForm\Renderer\Fieldset\Element',
            $this->getNameInLayout() . '_fieldset_element'
        );

        return parent::_prepareLayout();
    }


    /**
     * Form preparation
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'new_video_form',
                'class' => 'admin__scope-old',
                'enctype' => 'multipart/form-data',
            ]
        ]);
        $form->setUseContainer($this->getUseContainer());

        $form->addField('new_video_messages', 'note', []);

        $fieldset = $form->addFieldset('new_video_form_fieldset', []);
        $fieldset->setRenderer($this->fieldsetRenderer);

        $fieldset->addField(
            '',
            'hidden',
            [
                'name' => 'form_key',
                'value' => $this->getFormKey(),
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'item_id',
            'hidden',
            []
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'file_name',
            'hidden',
            []
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'video_provider',
            'hidden',
            [
                'name' => 'video_provider',
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'video_url',
            'text',
            [
                'class' => 'edited-data validate-url',
                'label' => __('Url'),
                'title' => __('Url'),
                'required' => true,
                'name' => 'video_url',
                'note' => $this->getNoteVideoUrl(),

            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'video_title',
            'text',
            [
                'class' => 'edited-data',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'name' => 'video_title',
            ]
        );

        $fieldset->addField(
            'video_description',
            'textarea',
            [
                'class' => 'edited-data',
                'label' => __('Description'),
                'title' => __('Description'),
                'name' => 'video_description',
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'new_video_screenshot',
            'file',
            [
                'label' => __('Preview Image'),
                'title' => __('Preview Image'),
                'name' => 'image',
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'new_video_screenshot_preview',
            'button',
            [
                'class' => 'preview_hidden_image_input_button',
                'label' => '',
                'name' => '_preview',
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $fieldset->addField(
            'new_video_get',
            'button',
            [
                'label' => '',
                'title' => 'Get Video Information',
                'name' => 'new_video_get',
                'value' => __('Get Video Information'),
                'class' => 'action-default'
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $this->addMediaRoleAttributes($fieldset);

        $fieldset->addField(
            'new_video_disabled',
            'checkbox',
            [
                'class' => 'edited-data',
                'label' => __('Hide from Product Page'),
                'title' => __('Hide from Product Page'),
                'name' => 'disabled',
            ]
        )->setRenderer($this->fieldsetElementRenderer);

        $this->setForm($form);
    }

    /**
     * Get html id
     *
     * @return mixed
     */
    public function getHtmlId()
    {
        if (null === $this->getData('id')) {
            $this->setData('id', $this->mathRandom->getUniqueHash('id_'));
        }
        return $this->getData('id');
    }

    /**
     * Get widget options
     *
     * @return string
     */
    public function getWidgetOptions()
    {
        return $this->jsonEncoder->encode(
            [
                'saveVideoUrl' => $this->_urlBuilder->addSessionParam()->getUrl('udprod/vendor/upload'),
                'saveRemoteVideoUrl' => $this->_urlBuilder->addSessionParam()->getUrl('udprod/vendor/retrieveImage'),
                'htmlId' => $this->getHtmlId(),
                'youTubeApiKey' => $this->mediaHelper->getYouTubeApiKey()
            ]
        );
    }

    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->_prodHlp->getProductToEdit());
        }
        return $this->getData('product');
    }

    /**
     * Add media role attributes to fieldset
     *
     * @param Fieldset $fieldset
     * @return $this
     */
    protected function addMediaRoleAttributes(Fieldset $fieldset)
    {
        $fieldset->addField('roleLabel', 'note', ['text' => __('Role')]);
        $mediaRoles = $this->getProduct()->getMediaAttributes();
        ksort($mediaRoles);
        foreach ($mediaRoles as $mediaRole) {
            $fieldset->addField(
                'video_' . $mediaRole->getAttributeCode(),
                'checkbox',
                [
                    'class' => 'video_image_role',
                    'label' => __($mediaRole->getFrontendLabel()),
                    'title' => __($mediaRole->getFrontendLabel()),
                    'data-role' => 'role-type-selector',
                    'value' => $mediaRole->getAttributeCode(),
                ]
            )->setRenderer($this->fieldsetElementRenderer);
        }
        return $this;
    }

    /**
     * Get note for video url
     *
     * @return \Magento\Framework\Phrase
     */
    protected function getNoteVideoUrl()
    {
        $result = __('Vimeo supported.<br />');
        if ($this->mediaHelper->getYouTubeApiKey() !== null) {
            $result .= __('YouTube supported.');
        } else {
            $result .= __('YouTube NOT supported (API Not Setup).');
        }
        return $result;
    }

    /**
     * Get url for config params
     *
     * @return string
     */
    protected function getConfigApiKeyUrl()
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'catalog',
                '_fragment' => self::PATH_ANCHOR_PRODUCT_VIDEO
            ]
        );
    }

    /**
     * Get form object
     *
     * @return \Magento\Framework\Data\Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * Set form object
     *
     * @param \Magento\Framework\Data\Form $form
     * @return $this
     */
    public function setForm(\Magento\Framework\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->_urlBuilder->getBaseUrl());

        $customAttributes = $this->getData('custom_attributes');
        if (is_array($customAttributes)) {
            foreach ($customAttributes as $key => $value) {
                $this->_form->addCustomAttribute($key, $value);
            }
        }
        return $this;
    }
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        return parent::_beforeToHtml();
    }
}
