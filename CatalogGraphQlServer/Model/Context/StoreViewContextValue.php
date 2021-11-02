<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Model\Context;

use Magento\GraphQlServer\Model\Context\ContextValueInterface;
use Magento\Store\Model\StoreManager;
use Magento\Store\Api\StoreResolverInterface;

/**
 * Store View Value
 */
class StoreViewContextValue implements ContextValueInterface
{
    const STORE_VIEW_CONTEXT = 'STORE_VIEW_CONTEXT';

    /**
     * @var StoreResolverInterface
     */
    private $storeResolver;

    /**
     * @var StoreManager
     */
    private $storeManager;

    /**
     * @param StoreResolverInterface $storeResolver
     * @param StoreManager $storeManager
     */
    public function __construct(
        StoreResolverInterface $storeResolver,
        StoreManager $storeManager
    ) {
        $this->storeResolver = $storeResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::STORE_VIEW_CONTEXT;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getValue(): string
    {
        return $this->storeManager->getStore($this->storeResolver->getCurrentStoreId())->getCode();
    }
}
