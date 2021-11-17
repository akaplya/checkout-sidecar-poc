<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\GraphQl\Schema\Type\Scalar;

use Magento\GraphQlServer\GraphQl\Config\Element\Scalar as ScalarElement;
use Magento\Framework\ObjectManagerInterface;

/**
 *
 */
class ScalarRegistry
{
    /**
     * @var ObjectManagerInterface
     */
    private ObjectManagerInterface $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create custom scalar
     *
     * @param ScalarElement $element
     * @return CustomScalarInterface
     */
    public function get(ScalarElement $element) : CustomScalarInterface
    {
        return $this->objectManager->get($element->getImplementation());
    }
}
