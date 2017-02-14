<?php
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

use Webkul\CustomRegistration\Controller\Adminhtml\AbstractMassDisplayOrder;

/**
 * Class MassEnable
 */
class HideInOrder extends AbstractMassDisplayOrder
{
    /**
     * Field id
     */
    const ID_FIELD = 'entity_id';

    /**
     * Resource collection
     *
     * @var string
     */
    protected $collection = 'Webkul\CustomRegistration\Model\ResourceModel\Customfields\Collection';

    /**
     * Post model
     *
     * @var string
     */
    protected $model = 'Webkul\CustomRegistration\Model\Customfields';

    /**
     * Post enable status
     *
     * @var boolean
     */
    protected $status = false;
}