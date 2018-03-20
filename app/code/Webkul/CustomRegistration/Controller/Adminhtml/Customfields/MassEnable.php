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
namespace Webkul\CustomRegistration\Controller\Adminhtml\Customfields;

use Webkul\CustomRegistration\Controller\Adminhtml\AbstractMassStatus;

/**
 * Class MassEnable
 */
class MassEnable extends AbstractMassStatus
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
    protected $status = true;
}
