<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_CustomRegistration
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\CustomRegistration\Api\Data;

/**
 * CustomRegistration Manage Fields interface.
 * @api
 */
interface ManageFieldsInterface
{
    /**#@+
     * Constants for keys of data array.
     */
    const ENTITY_ID    = 'entity_id';
    const STATUS    = 'status';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setId($id);
     /**
      * Get ID
      *
      * @return int|null
      */
    public function getStatus();

    /**
     * Set ID
     *
     * @param int $id
     * @return \Webkul\Marketplace\Api\Data\SellerInterface
     */
    public function setStatus($status);
}
