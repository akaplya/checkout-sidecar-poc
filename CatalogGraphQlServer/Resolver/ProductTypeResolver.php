<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

class ProductTypeResolver implements TypeResolverInterface
{
    private $complexTypes = ['configurable', 'bundle'];
    /**
     * @param array $data
     * @return string
     */
    public function resolveType(array $data): string
    {
        $type = 'SimpleProductV2';
        if (in_array($data['raw']['type'], $this->complexTypes)) {
            $type = 'ComplexProductV2';
        }
        return $type;
    }
}
