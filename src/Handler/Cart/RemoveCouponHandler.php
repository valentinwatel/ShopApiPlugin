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

namespace Sylius\ShopApiPlugin\Handler\Cart;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\ShopApiPlugin\Command\Cart\RemoveCoupon;
use Webmozart\Assert\Assert;

final class RemoveCouponHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        OrderProcessorInterface $orderProcessor
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderProcessor = $orderProcessor;
    }

    public function __invoke(RemoveCoupon $removeCoupon): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy(['tokenValue' => $removeCoupon->orderToken()]);

        Assert::notNull($cart, sprintf('Cart with token %s has not been found.', $removeCoupon->orderToken()));

        $cart->setPromotionCoupon(null);

        $this->orderProcessor->process($cart);
    }
}
