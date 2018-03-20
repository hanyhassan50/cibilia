<?php

namespace Unirgy\DropshipVendorProduct\Block\Vendor\Product\Renderer\Downloadable;

use Magento\Downloadable\Block\Adminhtml\Catalog\Product\Edit\Tab\Downloadable\Links as DownloadableLinks;
use Magento\Downloadable\Model\Link;
use Magento\MediaStorage\Helper\File\Storage\Database;

class Links extends DownloadableLinks
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('Unirgy_DropshipVendorProduct::product/edit/downloadable/links.phtml');
    }
    public function getConfigJson($type='links')
    {
        $this->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()
                ->getUrl('udprod/vendor/downloadableUpload', ['type' => $type])
        );
        $this->getConfig()->setParams(['form_key' => $this->getFormKey()]);
        $this->getConfig()->setFileField($type);
        $this->getConfig()->setFilters([
            'all'    => [
                'label' => __('All Files'),
                'files' => ['*.*']
            ]
        ]);
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(true);
        return $this->_jsonEncoder->encode($this->getConfig()->getData());
    }
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            [
                'label' => __('Add New Link'),
                'id' => 'add_link_item',
                'class' => 'action-add',
                'data_attribute' => ['action' => 'add-link'],
                'template' => 'Unirgy_DropshipVendorProduct::widget/button.phtml'
            ]
        )->setTemplate('Unirgy_DropshipVendorProduct::widget/button.phtml');
        return $addButton->toHtml();
    }
    public function getUploadUrl($type)
    {
        return $this->_urlBuilder->addSessionParam()
                ->getUrl('udprod/vendor/downloadableUpload', ['type' => $type]);
    }
}