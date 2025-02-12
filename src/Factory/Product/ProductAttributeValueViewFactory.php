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
use Sylius\ShopApiPlugin\Factory\Product\ProductAttributeValueViewResolver\ProductAttributeValueViewResolverInterface;
use Sylius\ShopApiPlugin\View\Product\ProductAttributeValueView;
use Symfony\Component\DependencyInjection\ServiceLocator;

final class ProductAttributeValueViewFactory implements ProductAttributeValueViewFactoryInterface
{
    /** @var string */
    private $productAttributeValueViewClass;

    /** @var ServiceLocator */
    private $productAttributeValueViewResolversLocator;

    public function __construct(
        string $productAttributeValueViewClass,
        ServiceLocator $productAttributeValueViewResolversLocator
    ) {
        $this->productAttributeValueViewClass = $productAttributeValueViewClass;
        $this->productAttributeValueViewResolversLocator = $productAttributeValueViewResolversLocator;
    }

    public function create(ProductAttributeValueInterface $productAttributeValue, string $localeCode): ProductAttributeValueView
    {
        /** @var ProductAttributeValueView $productAttributeValueView */
        $productAttributeValueView = new $this->productAttributeValueViewClass();
        $productAttributeValueView->code = $productAttributeValue->getCode();
        $productAttributeValueView->type = $productAttributeValue->getType();

        /** @var ProductAttributeValueViewResolverInterface $valueResolver */
        $valueResolver = $this->productAttributeValueViewResolversLocator->get($productAttributeValue->getType());
        $productAttributeValueView->value = $valueResolver->getValue($productAttributeValue, $localeCode);

        $productAttribute = $productAttributeValue->getAttribute();
        $productAttributeTranslation = $productAttribute->getTranslation($localeCode);
        $productAttributeValueView->name = $productAttributeTranslation->getName();

        return $productAttributeValueView;
    }
}
