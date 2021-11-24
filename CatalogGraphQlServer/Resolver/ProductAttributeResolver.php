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

class ProductAttributeResolver implements ResolverInterface
{

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $attributes = [];
        $rawAttributes = $value['raw']['attributes'] ?? [];
        foreach ($rawAttributes as $attribute) {
            $attributeCode = $attribute['attributeCode'];
            if (count($attribute['value']) == 1) {
                $attributeValue = $attribute['value'][0];
            } else {
                $attributeValue = $attribute['value'];
            }

            $attributes[] = [
                'name' => $attributeCode,
                'value' => json_encode($attributeValue)
            ];
        }
        return $attributes;
    }
}
