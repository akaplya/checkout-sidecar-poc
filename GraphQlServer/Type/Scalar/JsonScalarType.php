<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\Type\Scalar;

use Magento\GraphQlServer\GraphQl\Schema\Type\Scalar\CustomScalarInterface;

class JsonScalarType implements CustomScalarInterface
{

    public function serialize($value)
    {
        return json_decode($value);
    }

    public function parseValue($value)
    {
        return json_encode($value);
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $valueNode->value;
    }
}
