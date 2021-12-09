<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQlServer\Resolver;

use Magento\CatalogGraphQlServer\Model\ProductDataProvider;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\CatalogGraphQlServer\Model\Context\StoreViewContextValue;
use Magento\Framework\Serialize\Serializer\Json;

class ComplexProductVariantsResolver implements ResolverInterface
{
    /**
     * @var ProductDataProvider
     */
    private $productDataProvider;

    /**
     * @var Json
     */
    private $jsonSerializer;

    public function __construct(
        ProductDataProvider $productDataProvider,
        Json $jsonSerializer
    ) {
        $this->productDataProvider = $productDataProvider;
        $this->jsonSerializer = $jsonSerializer;
    }

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
        $optionValues = [];
        foreach ($value['raw']['optionsV2'] as $option) {
            if (!empty($productOptionIds) && !isset($productOptionIds[$option['id']])) {
                continue;
            }
            foreach ($option['values'] as $optionValue) {
                if (!empty($productOptionValueId) && !isset($productOptionValueId[$optionValue['id']])) {
                    continue;
                }
                $optionValues[$option['label']][$optionValue['label']] = [
                    'productOptionId' => $option['id'],
                    'productOptionValueId' => $optionValue['id']
                ];
            }
        }
        if (empty($optionValues)) {
            return [];
        }

        foreach ($value['raw']['variants'] as $variant) {
            $variantSelectionValues = [];
            $crossingFound = false;
            foreach ($variant['selections'] as $variantSelection) {
                $selectionName = $variantSelection['name'];
                $selectionValue = $variantSelection['value'];
                if (isset($optionValues[$selectionName][$selectionValue])) {
                    $crossingFound = true;
                    $variantSelectionValues[$selectionName] = $optionValues[$selectionName][$selectionValue];
                } else {
                    $crossingFound = false;
                    break;
                }
            }
            $resultVariants = [];
            if (true === $crossingFound) {
                foreach ($variantSelectionValues as $variantSelectionValue) {
                    $resultVariants[$variant['sku']][] = $variantSelectionValue;
                }

                foreach ($this->productDataProvider->getProducts(
                    array_keys($resultVariants),
                    $context->getValue(StoreViewContextValue::STORE_VIEW_CONTEXT),
                    true
                ) as $row) {
                    $data['raw'] = $this->jsonSerializer->unserialize($row['feed_data']);
                    $data = $this->copyFields(
                        $data,
                        [
                            'id' => 'productId',
                            'name',
                            'description',
                            'shortDescription'
                        ]
                    );

                    $result[] = [
                        'selections' => $resultVariants[$data['raw']['sku']] ?? null,
                        'product' => $data
                    ];
                }
            }
        }

        return $result;
    }

    private function copyFields(array $data, array $fields): array
    {
        foreach ($fields as $alias => $field) {
            $data[\is_string($alias) ? $alias : $field] = $data['raw'][$field] ?? null;
        }
        return $data;
    }
}
