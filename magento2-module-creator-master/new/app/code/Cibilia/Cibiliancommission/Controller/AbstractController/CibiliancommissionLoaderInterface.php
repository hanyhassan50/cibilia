<?php

namespace Cibilia\CibilianCommission\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CibilianCommissionLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\CibilianCommission\Model\CibilianCommission
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
