<?php
namespace Magento\Framework\App\Cron;

/**
 * Interceptor class for @see \Magento\Framework\App\Cron
 */
class Interceptor extends \Magento\Framework\App\Cron implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Framework\App\State $state, \Magento\Framework\App\Console\Request $request, \Magento\Framework\App\Console\Response $response, array $parameters = array())
    {
        $this->___init();
        parent::__construct($eventManager, $state, $request, $response, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function launch()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'launch');
        if (!$pluginInfo) {
            return parent::launch();
        } else {
            return $this->___callPlugins('launch', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function catchException(\Magento\Framework\App\Bootstrap $bootstrap, \Exception $exception)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'catchException');
        if (!$pluginInfo) {
            return parent::catchException($bootstrap, $exception);
        } else {
            return $this->___callPlugins('catchException', func_get_args(), $pluginInfo);
        }
    }
}
