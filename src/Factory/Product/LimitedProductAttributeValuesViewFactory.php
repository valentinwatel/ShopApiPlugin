<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Factory\Product;

use Sylius\Component\Product\Model\ProductAttributeValueInterface;

final class LimitedProductAttributeValuesViewFactory implements ProductAttributeValuesViewFactoryInterface
{
    /** @var ProductAttributeValueViewFactoryInterface */
    private $productAttributeValueViewFactory;

    /** @var string[] */
    private $allowedAttributesCodes;

    public function __construct(ProductAttributeValueViewFactoryInterface $productAttributeValueViewFactory, array $allowedAttributesCodes)
    {
        $this->productAttributeValueViewFactory = $productAttributeValueViewFactory;
        $this->allowedAttributesCodes = $allowedAttributesCodes;
    }

    public function create(array $attributeValues, string $locale): array
    {
        $attributeValuesView = [];

        /** @var ProductAttributeValueInterface $attributeValue */
        foreach ($attributeValues as $attributeValue) {
            if (!in_array($attributeValue->getCode(), $this->allowedAttributesCodes, true)) {
                continue;
            }

            $attributeValuesView[] = $this->productAttributeValueViewFactory->create($attributeValue, $locale);
        }

        return $attributeValuesView;
    }
}
