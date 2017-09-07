<?php
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

use Webkul\CustomRegistration\Controller\Adminhtml\AbstractMassDisplayEmail;

/**
 * Class MassEnable
 */
class HideInEmail extends AbstractMassDisplayEmail
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