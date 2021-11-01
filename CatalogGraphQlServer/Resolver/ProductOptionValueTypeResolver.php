<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class ProductOptionValueTypeResolver implements TypeResolverInterface
{
    /**
     * @param array $data
     * @return string
     */
    public function resolveType(array $data): string
    {
        return 'ConfigurableOptionValue';
    }
}
