<?php

namespace Unirgy\Dropship\Model;

use \Magento\Catalog\Model\Product\Image;
use \Magento\Framework\Filesystem\Io\File;

class ProductImage extends Image
{
    public function clearCache($vId=null)
    {
        $hasImageUpload = true;
        $subDir = '';
        if ($vId instanceof Vendor) {
            $hasImageUpload = $vId->hasImageUpload();
            $vId = $vId->getId();
            $subDir = 'vendor/'.$vId;
        }
        if (!$hasImageUpload) return;
        $baseMediaPath = $this->_catalogProductMediaConfig->getBaseMediaPath();
        if (!empty($baseMediaPath)) {
            $baseMediaPath .= '/';
        }
        foreach ($this->_storeManager->getStores() as $store) {
            $directory = sprintf('%s%s/%s/%s',
                $baseMediaPath, 'cache', $store->getId(), $subDir
            );

            $this->_mediaDirectory->delete($directory);

            $this->_coreFileStorageDatabase->deleteFolder($this->_mediaDirectory->getAbsolutePath($directory));
        }
    }
}