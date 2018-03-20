<?php
namespace Cibilia\Idproofs\Block;
use Magento\Framework\View\Element\Template;

/**
* Baz block
*/
class Refrerform extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Cibilia\Idproofs\Model\Idproof
     */
    protected $_idProof;

    /**
     * @var \Unirgy\Dropship\Model\Vendor
     */
    protected $_vendor;

    /**
     * Refrerform constructor.
     *
     * @param \Cibilia\Idproofs\Model\Idproof $idproof
     * @param \Unirgy\Dropship\Model\Vendor $vendor
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Cibilia\Idproofs\Model\Idproof $idproof,
        \Unirgy\Dropship\Model\Vendor $vendor,
        Template\Context $context,
        array $data = []
    )
    {
        $this->_idProof = $idproof;
        $this->_vendor = $vendor;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Review Your Information'));
    }

    public function getVenodorwork()
    {
        return $this->_idProof->getVendorwork();
    }

    public function getVendorcat()
    {
        return $this->_idProof->getVendorcat();
    }

    public function getVendortype()
    {
        return $this->_idProof->getVendortype();
    }

    public function getVendorRole()
    {
        return $this->_idProof->getVendorRole();
    }

    public function getCompanyEmployee()
    {
        return $this->_idProof->getcompanyemployee();
    }


    public function getCompanyType()
    {
        return $this->_idProof->getcompanytype();
    }

    public function productCategories()
    {
        return $this->_idProof->productcategories();
    }

    public function productSellPlace()
    {
        return $this->_idProof->productsellplace();
    }

    public function bestTimeToCall()
    {
        return $this->_idProof->bestTimetocall();
    }

    public function getVendor($id) {
        return $this->_vendor->load($id);
    }
}
