<?php
namespace Webkul\CustomRegistration\Api\Data;

/**
 * Marketplace Seller interface.
 * @api
 */
interface ManageFieldsDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
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
