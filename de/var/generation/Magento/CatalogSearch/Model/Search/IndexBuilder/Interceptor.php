<?php
namespace Magento\CatalogSearch\Model\Search\IndexBuilder;

/**
 * Interceptor class for @see \Magento\CatalogSearch\Model\Search\IndexBuilder
 */
class Interceptor extends \Magento\CatalogSearch\Model\Search\IndexBuilder implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource, \Magento\Framework\App\Config\ScopeConfigInterface $config, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Search\Adapter\Mysql\ConditionManager $conditionManager, \Magento\Framework\Indexer\ScopeResolver\IndexScopeResolver $scopeResolver, \Magento\CatalogSearch\Model\Search\TableMapper $tableMapper)
    {
        $this->___init();
        parent::__construct($resource, $config, $storeManager, $conditionManager, $scopeResolver, $tableMapper);
    }

    /**
     * {@inheritdoc}
     */
    public function build(\Magento\Framework\Search\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'build');
        if (!$pluginInfo) {
            return parent::build($request);
        } else {
            return $this->___callPlugins('build', func_get_args(), $pluginInfo);
        }
    }
}
