<?php

namespace Cibilia\Commissions\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CommissionsLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\Commissions\Model\Commissions
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
