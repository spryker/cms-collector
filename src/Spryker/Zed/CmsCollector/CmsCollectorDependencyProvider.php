<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\CmsCollector;

use Spryker\Zed\CmsCollector\Dependency\Facade\CmsCollectorToCmsBridge;
use Spryker\Zed\CmsCollector\Dependency\Facade\CmsCollectorToCollectorBridge;
use Spryker\Zed\CmsCollector\Dependency\Facade\CmsCollectorToSearchBridge;
use Spryker\Zed\CmsCollector\Dependency\Facade\CmsCollectorToStoreFacadeBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\CmsCollector\CmsCollectorConfig getConfig()
 */
class CmsCollectorDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_COLLECTOR = 'FACADE_COLLECTOR';

    /**
     * @var string
     */
    public const FACADE_SEARCH = 'FACADE_SEARCH';

    /**
     * @var string
     */
    public const FACADE_CMS = 'FACADE_CMS';

    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const QUERY_CONTAINER_TOUCH = 'QUERY_CONTAINER_TOUCH';

    /**
     * @var string
     */
    public const SERVICE_DATA_READER = 'SERVICE_DATA_READER';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container->set(static::SERVICE_DATA_READER, function (Container $container) {
            return $container->getLocator()->utilDataReader()->service();
        });

        $container->set(static::FACADE_COLLECTOR, function (Container $container) {
            return new CmsCollectorToCollectorBridge($container->getLocator()->collector()->facade());
        });

        $container->set(static::FACADE_SEARCH, function (Container $container) {
            return new CmsCollectorToSearchBridge($container->getLocator()->search()->facade());
        });

        $container->set(static::QUERY_CONTAINER_TOUCH, function (Container $container) {
            return $container->getLocator()->touch()->queryContainer();
        });

        $container = $this->addCmsFacade($container);
        $container = $this->addStoreFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCmsFacade(Container $container)
    {
        $container->set(static::FACADE_CMS, function (Container $container) {
            return new CmsCollectorToCmsBridge($container->getLocator()->cms()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new CmsCollectorToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }
}
