<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\GraphQl\Schema\Type\Scalar;

use GraphQL\Language\AST\Node;
use Magento\GraphQlServer\GraphQl\Config\Element\Scalar as ScalarElement;
use Magento\GraphQlServer\GraphQl\Schema\Type\CustomScalarType;

class Scalar extends CustomScalarType
{
    public function __construct(ScalarElement $configElement)
    {
        $config = [
            'name' => $configElement->getName(),
            'description' => $configElement->getDescription(),
            'serialize' =>
                static function($value) use ($configElement) {return call_user_func([$configElement->getDefinition(), 'serialize'], $value);},
            'parseValue' =>
                static function($value) use ($configElement) {return call_user_func([$configElement->getDefinition(), 'parseValue'], $value);},
            'parseLiteral' =>
                static function(Node $valueNode, ?array $variables = null) use ($configElement) {return call_user_func([$configElement->getDefinition(), 'parseLiteral'], $valueNode, $variables);},
        ];
        parent::__construct($config);
    }

}
