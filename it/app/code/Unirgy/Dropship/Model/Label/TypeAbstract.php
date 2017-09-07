<?php
/**
 * Unirgy LLC
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.unirgy.com/LICENSE-M1.txt
 *
 * @category   Unirgy
 * @package    \Unirgy\Dropship
 * @copyright  Copyright (c) 2015-2016 Unirgy LLC (http://www.unirgy.com)
 * @license    http:///www.unirgy.com/LICENSE-M1.txt
 */

namespace Unirgy\Dropship\Model\Label;

use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\DataObject;
use \Unirgy\Dropship\Helper\Data as HelperData;

abstract class TypeAbstract extends DataObject
{
    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var DirectoryList
     */
    protected $_filesystemDirectoryList;

    public function __construct(array $data = [],
                                HelperData $helperData = null,
                                DirectoryList $filesystemDirectoryList = null)
    {
        $this->_helperData = $helperData;
        $this->_filesystemDirectoryList = $filesystemDirectoryList;

        parent::__construct($data);
    }

    /**
     * Send batch file PDF download
     *
     */
    public function printBatch($batch=null)
    {
        $data = $this->renderBatchContent($batch);
        $this->_helperData->sendDownload($data['filename'], $data['content'], $data['type']);
    }

    /**
     * Send PDF download only for 1 track
     *
     * @param Track $track
     */
    public function printTrack($track=null)
    {
        $data = $this->renderTrackContent($track);
        $this->_helperData->sendDownload($data['filename'], $data['content'], $data['type']);
    }

    public function getBatchPathName($batch)
    {
        return $this->_filesystemDirectoryList->getPath('var') . ('batch').'/'.$this->getBatchFileName($batch);
    }

    protected function _getTrackVendorId($track)
    {
        $vId = null;
        if ($track instanceof \Unirgy\Rma\Model\Rma\Track) {
            $vId = $track->getRma()->getUdropshipVendor();
        } elseif ($track instanceof \Magento\Sales\Model\Order\Shipment\Track) {
            $vId = $track->getShipment()->getUdropshipVendor();
        }
        return $vId;
    }

    protected function _getTrackVendor($track)
    {
        return $this->_helperData->getVendor($this->_getTrackVendorId($track));
    }
}