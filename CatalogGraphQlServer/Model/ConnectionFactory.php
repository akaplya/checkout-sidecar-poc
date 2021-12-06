<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Model;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection\ConfigInterface as ResourceConfigInterface;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Type\Db\ConnectionFactoryInterface;

/**
 * Creates connection instance
 *
 */
class ConnectionFactory
{
    /**
     * @var ResourceConfigInterface
     */
    private $resourceConfig;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var ConnectionFactoryInterface
     */
    private $connectionFactory;

    /**
     * @var array`
     */
    private static $configData;

    /**
     * @param ResourceConfigInterface $resourceConfig
     * @param DeploymentConfig $deploymentConfig
     * @param ConnectionFactoryInterface $connectionFactory
     */
    public function __construct(
        ResourceConfigInterface $resourceConfig,
        DeploymentConfig $deploymentConfig,
        ConnectionFactoryInterface $connectionFactory
    ) {
        $this->resourceConfig = $resourceConfig;
        $this->deploymentConfig = $deploymentConfig;
        $this->connectionFactory = $connectionFactory;
    }


    /**
     * Creates one-time connection for export
     *
     * @param string $resourceName
     * @return AdapterInterface
     */
    public function getConnection($resourceName)
    {
        if (!self::$configData) {
            $connectionName = $this->resourceConfig->getConnectionName($resourceName);
            self::$configData = $this->deploymentConfig->get(
                ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTIONS . '/' . $connectionName
            );
        }
        return $this->connectionFactory->create(self::$configData);
    }
}
