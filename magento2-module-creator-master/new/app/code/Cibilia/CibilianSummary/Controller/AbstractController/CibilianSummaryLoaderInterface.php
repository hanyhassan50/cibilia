<?php

namespace Cibilia\CibilianSummary\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface CibilianSummaryLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\CibilianSummary\Model\CibilianSummary
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
