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
            $output[] = $this->format($row['feed_data']);
        }
        return $output;
    }

    private function format(string $json): array
    {
        $data = $this->jsonSerializer->unserialize($json);
        $entry = [
            'id' => $data['productId'],
            'name' => $data['name'],
            'description' => $data['description'],
            'shortDescription' => $data['shortDescription']
        ];
        foreach ($data['attributes'] as $attribute) {
            $attributeCode = $attribute['attributeCode'];
            if (count($attribute['value']) == 1) {
                $attributeValue = $attribute['value'][0];
            } else {
                $attributeValue = $attribute['value'];
            }

            $entry['attributes'][] = [
                'name' => $attributeCode,
                'value' => $attributeValue
            ];
        }

        return $entry;
    }
}
