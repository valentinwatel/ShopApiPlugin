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

namespace Sylius\ShopApiPlugin\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class UserCartRecalculationListener
{
    /** @var CartContextInterface */
    private $cartContext;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    public function __construct(CartContextInterface $cartContext, OrderProcessorInterface $orderProcessor)
    {
        $this->cartContext = $cartContext;
        $this->orderProcessor = $orderProcessor;
    }

    public function recalculateCartWhileLogin(JWTCreatedEvent $event): void
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException $exception) {
            return;
        }

        Assert::isInstanceOf($cart, OrderInterface::class);

        $this->orderProcessor->process($cart);
    }
}
