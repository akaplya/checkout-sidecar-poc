<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\Type\Scalar;

use GraphQL\Type\Definition\ScalarType;

class JsonScalarType extends ScalarType
{

    public $name = 'JSON';

    public function serialize($value)
    {
        $result = json_encode($value);
        if (false === $result) {
            throw new \InvalidArgumentException("Unable to serialize value. Error: " . json_last_error_msg());
        }
        return $result;
    }

    public function parseValue($value)
    {
        return $this->serialize($value);
    }

    public function parseLiteral($valueNode, ?array $variables = null)
    {
        return $valueNode->value;
    }
}
