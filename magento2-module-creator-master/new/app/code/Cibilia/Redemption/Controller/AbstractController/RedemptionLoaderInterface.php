<?php

namespace Cibilia\Redemption\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface RedemptionLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\Redemption\Model\Redemption
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
