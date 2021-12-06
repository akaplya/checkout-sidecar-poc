<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Model;

class ProductDataProvider
{
    /**
     * @var ConnectionFactory
     */
    private $connectionFactory;

    public function __construct(
        ConnectionFactory $connectionFactory
    ) {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * @param array $productIds
     * @param string $storeViewCode
     * @return \Generator
     * @throws \Zend_Db_Statement_Exception
     */
    public function getProducts(array $productIds, string $storeViewCode) {
        $connection = $this->connectionFactory->getConnection('default');
        $select = $connection->select()
            ->from(
                ['t' => 'catalog_data_exporter_products'],
                ['t.feed_data']
            )
            ->where('t.id IN (?)', $productIds)
            ->where('t.store_view_code = ?', $storeViewCode);
        $cursor = $connection->query($select);
        while ($row = $cursor->fetch()) {
            yield $row;
        }
    }
}
