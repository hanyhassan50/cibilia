<?php

namespace Cibilia\Commission\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CommissionLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\Commission\Model\Commission
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
