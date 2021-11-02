<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\Type;

use Magento\Framework\GraphQl\Schema\Type\ScalarTypes as FrameworkScalarTypes;

/**
 * Scalar Types Override
 */
class ScalarTypes extends FrameworkScalarTypes
{
//    private $scalarTypes;
//
//    /**
//     * @var \GraphQL\Type\Definition\ScalarType[]
//     */
//    private $scalarExtensions;
//
//    /**
//     * @param array $scalarExtensions
//     */
//    public function __construct(
//        array $scalarExtensions
//    ) {
//        $this->scalarExtensions = $scalarExtensions;
////        \GraphQL\Type\Definition\Type::overrideStandardTypes($scalarExtensions);
//    }
//
//    /**
//     * @return \GraphQL\Type\Definition\ScalarType[]
//     */
//    private function getScalars() : array
//    {
//        if (!$this->scalarTypes) {
//            $scalars = \GraphQL\Type\Definition\Type::getStandardTypes();
//            foreach ($this->scalarExtensions as $extension) {
//                $scalars[$extension->name] = $extension;
//            }
//            $this->scalarTypes = $scalars;
//        }
//        return $this->scalarTypes;
//    }
//    /**
//     * Check if type is scalar
//     *
//     * @param string $typeName
//     * @return bool
//     */
//    public function isScalarType(string $typeName) : bool
//    {
//        $scalars = $this->getScalars();
//        return isset($scalars[$typeName]);
//    }
//
//    /**
//     * Get instance of scalar type
//     *
//     * @param string $typeName
//     * @return \GraphQL\Type\Definition\ScalarType|\GraphQL\Type\Definition\Type
//     * @throws \LogicException
//     */
//    public function getScalarTypeInstance(string $typeName) : \GraphQL\Type\Definition\Type
//    {
//        $scalars = $this->getScalars();
//        if (isset($scalars[$typeName])) {
//            return $scalars[$typeName];
//        } else {
//            throw new \LogicException(sprintf('Scalar type %s doesn\'t exist', $typeName));
//        }
//    }
}
