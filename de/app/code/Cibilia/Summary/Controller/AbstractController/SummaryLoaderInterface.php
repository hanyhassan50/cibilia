<?php

namespace Cibilia\Summary\Controller\AbstractController;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;

interface SummaryLoaderInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return \Cibilia\Summary\Model\Summary
     */
    public function load(RequestInterface $request, ResponseInterface $response);
}
