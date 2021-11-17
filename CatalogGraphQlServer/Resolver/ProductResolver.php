<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQlServer\Model\Context\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\CatalogGraphQlServer\Model\Context\StoreViewContextValue;

class ProductResolver implements ResolverInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @param ResourceConnection $resourceConnection
     * @param Json $jsonSerializer
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        Json $jsonSerializer
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed|void
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        return $this->resolveProducts($args, $context);
    }

    private function copyFields(array $data, array $fields): array
    {
        foreach ($fields as $alias => $field) {
            $data[is_string($alias) ? $alias : $field] = $data['raw'][$field];
        }
        return $data;
    }

    private function resolveProducts(array $arguments, Context $context)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from(
                ['t' => $this->resourceConnection->getTableName('catalog_data_exporter_products')],
                ['t.feed_data']
            )
            ->where('t.id IN (?)', $arguments['ids'])
            ->where('t.store_view_code = ?', $context->getValue(StoreViewContextValue::STORE_VIEW_CONTEXT));
        $output = [];
        $cursor = $connection->query($select);
        while ($row = $cursor->fetch()) {
            $data['raw'] = $this->jsonSerializer->unserialize($row['feed_data']);
                $data = $this->copyFields(
                    $data,
                    [
                        'id' => 'productId',
                        'name',
                        'description',
                        'shortDescription'
                    ]
                );
            $output[] = $data;
        }
        return $output;
    }
}
