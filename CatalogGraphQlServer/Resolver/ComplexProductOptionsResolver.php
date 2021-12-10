<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ComplexProductOptionsResolver implements ResolverInterface
{

    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        if (isset($args['selections'])) {
            $productOptionIds = [];
            $productOptionValueId = [];
            foreach ($args['selections'] as $selection) {
                if (isset($selection['productOptionId'])) {
                    $productOptionIds[$selection['productOptionId']] = true;
                }
                if (isset($selection['productOptionValueId'])) {
                    $productOptionValueId[$selection['productOptionValueId']] = true;
                }
            }
        }
        foreach ($value['raw']['optionsV2'] as $option) {

            $values = [];
            foreach ($option['values'] as $value) {
                if (!empty($productOptionIds) && isset($productOptionIds[$option['id']])
                    && !empty($productOptionValueId) && !isset($productOptionValueId[$value['id']])) {
                    continue;
                }
                $values[] = [
                    'id' => $value['id'],
                    'title' => $value['label'],
                ];
            }

            if (!empty($values)) {
                $result[] = [
                    'id' => $option['id'],
                    'required' => false, //no data
                    'multi' => false, //no data
                    'title' => $option['label'],
                    'values' => $values,
                ];
            }
        }

        return $result;
    }
}
