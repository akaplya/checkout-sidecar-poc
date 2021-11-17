<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\GraphQlServer\GraphQl\Config\Element;

use Magento\Framework\GraphQl\Config\ConfigElementInterface;

class Scalar implements ConfigElementInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $implementation;

    public function __construct(
        string $name,
        string $description,
        string $implementation
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->implementation = $implementation;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getImplementation(): string
    {
        return $this->implementation;
    }
}
