<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\Type\Scalar;

use GraphQL\Type\Definition\ScalarType;

class JsonScalarType
{

//    public $name = 'Json';

    public static function serialize($value)
    {
        $result = json_encode($value);
        if (false === $result) {
            throw new \InvalidArgumentException("Unable to serialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }

    public static function parseValue($value)
    {
        return self::serialize($value);
    }

    public static function parseLiteral($valueNode, ?array $variables = null)
    {
        return $valueNode->value;
    }
}
