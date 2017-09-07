<?php

namespace Cibilia\Cibilianpayout\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CibilianpayoutLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\Cibilianpayout\Model\Cibilianpayout
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
