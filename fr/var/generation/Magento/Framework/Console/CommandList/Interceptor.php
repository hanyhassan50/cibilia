<?php
namespace Magento\Framework\Console\CommandList;

/**
 * Interceptor class for @see \Magento\Framework\Console\CommandList
 */
class Interceptor extends \Magento\Framework\Console\CommandList implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(array $commands = array())
    {
        $this->___init();
        parent::__construct($commands);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommands()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCommands');
        if (!$pluginInfo) {
            return parent::getCommands();
        } else {
            return $this->___callPlugins('getCommands', func_get_args(), $pluginInfo);
        }
    }
}
