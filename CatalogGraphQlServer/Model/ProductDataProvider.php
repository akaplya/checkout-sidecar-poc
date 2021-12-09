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
     * @param bool|null $skuAsId
     * @return \Generator
     * @throws \Zend_Db_Statement_Exception
     */
    public function getProducts(array $productIds, string $storeViewCode, $skuAsId = null): ?\Generator
    {
        $connection = $this->connectionFactory->getConnection('default');
        $select = $connection->select()
            ->from(
                ['t' => 'catalog_data_exporter_products'],
                ['t.feed_data']
            )
            ->where('t.store_view_code = ?', $storeViewCode);
        if (true === $skuAsId) {
            $select->where('t.sku IN (?)', $productIds);
        } else {
            $select->where('t.id IN (?)', $productIds);
        }
        $cursor = $connection->query($select);
        while ($row = $cursor->fetch()) {
            yield $row;
        }
    }
}
