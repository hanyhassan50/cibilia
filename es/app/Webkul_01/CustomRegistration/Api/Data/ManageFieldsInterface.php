<?php
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
}
