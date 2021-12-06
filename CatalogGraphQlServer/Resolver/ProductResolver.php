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
use Magento\Framework\Serialize\Serializer\Json;
use Magento\CatalogGraphQlServer\Model\Context\StoreViewContextValue;
use Magento\CatalogGraphQlServer\Model\ProductDataProvider;

class ProductResolver implements ResolverInterface
{
    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var Json
     */
    private $jsonSerializer;

    /**
     * @param Json $jsonSerializer
     */
    public function __construct(
        ProductDataProvider $productDataProvider,
        Json $jsonSerializer
    ) {
        $this->productDataProvider = $productDataProvider;
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
            $data[is_string($alias) ? $alias : $field] = $data['raw'][$field] ?? null;
        }
        return $data;
    }

    private function resolveProducts(array $arguments, Context $context)
    {
        foreach (
            $this->productDataProvider->getProducts(
                $arguments['ids'],
                $context->getValue(StoreViewContextValue::STORE_VIEW_CONTEXT)
            ) as $row) {
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
