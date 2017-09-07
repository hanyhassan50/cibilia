<?php

namespace Cibilia\CibilianRedemption\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CibilianRedemptionLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\CibilianRedemption\Model\CibilianRedemption
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
