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

class SimpleProductPriceResolver implements ResolverInterface
{

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        return [
            'regular' => [
                'amount' => $value['raw']['prices']['minimumPrice']['regularPrice'],
                'currency' => $value['raw']['currency']
            ],
            'final' => [
                'amount' => $value['raw']['prices']['minimumPrice']['finalPrice'],
                'currency' => $value['raw']['currency']
            ]
        ];
    }
}
