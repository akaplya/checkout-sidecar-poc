<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\GraphQl\Schema\Type;

use Magento\Framework\GraphQl\Schema\Type\InputTypeInterface;
use Magento\Framework\GraphQl\Schema\Type\OutputTypeInterface;

class CustomScalarType extends \GraphQL\Type\Definition\CustomScalarType implements InputTypeInterface, OutputTypeInterface
{

}
