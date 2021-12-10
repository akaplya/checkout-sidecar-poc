<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductImageResolver implements ResolverInterface
{
    /**
     * @var string[]
     */
    private $enumMap = [
        'image' => 'REGULAR',
        'small_image' => 'SMALL',
        'thumbnail' => 'THUMBNAIL',
        'swatch_image' => 'SWATCH'
    ];

    /**
     * @param array $rawRoles
     * @return array
     */
    private function formatRoles(array $rawRoles): array
    {
        if (empty($rawRoles)) {
            return ['REGULAR'];
        }
        $roles = [];
        foreach ($rawRoles as $role) {
            $roles[] = $this->enumMap[$role] ?? 'REGULAR';
        }
        return $roles;
    }

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $images = [];
        $filterRoles = [];
        if (!empty($args['roles'])) {
            $filterRoles = $args['roles'];
        }
        $rawImages = $value['raw']['images'] ?? [];
        foreach ($rawImages as $image) {
            $roles = $this->formatRoles($image['resource']['roles'] ?? []);
            if (empty($filterRoles) || array_intersect($filterRoles, $roles)) {
                $images[] = [
                    'resource' => [
                        'url' => $image['resource']['url'],
                        'label' => $image['resource']['label']
                    ],
                    'roles' => $roles
                ];
            }
        }
        return $images;
    }
}
