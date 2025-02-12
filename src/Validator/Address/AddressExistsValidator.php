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

namespace Sylius\ShopApiPlugin\Validator\Address;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\ShopApiPlugin\Provider\LoggedInShopUserProviderInterface;
use Sylius\ShopApiPlugin\Validator\Constraints\AddressExists;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AddressExistsValidator extends ConstraintValidator
{
    /** @var AddressRepositoryInterface */
    private $addressRepository;

    /** @var LoggedInShopUserProviderInterface */
    private $loggedInUserProvider;

    public function __construct(
        AddressRepositoryInterface $addressRepository,
        LoggedInShopUserProviderInterface $loggedInUserProvider
    ) {
        $this->addressRepository = $addressRepository;
        $this->loggedInUserProvider = $loggedInUserProvider;
    }

    /**
     * @param string|int $id
     * @param Constraint|AddressExists $constraint
     */
    public function validate($id, Constraint $constraint): void
    {
        $address = $this->addressRepository->findOneBy(['id' => $id]);

        if (!$address instanceof AddressInterface) {
            $this->context->addViolation($constraint->message);

            return;
        }

        $user = $this->loggedInUserProvider->provide();
        $customer = $address->getCustomer();

        if (null === $customer || $customer->getEmail() !== $user->getEmail()) {
            $this->context->addViolation($constraint->message);

            return;
        }
    }
}
