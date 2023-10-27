<?php declare(strict_types=1);

namespace YireoTraining\HyvaCheckoutReorderSteps\Plugin;

use Hyva\Checkout\ViewModel\Checkout\AddressRenderer;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

class FixRenderAddress
{
    public function __construct(
        private SessionCheckout $sessionCheckout,
    ) {
    }

    public function beforeRenderShippingAddress(AddressRenderer $subject, string $code): array
    {
        $this->setBillingAddressAsShippingAddress();
        return [$code];
    }

    /**
     * @param AddressRenderer $subject
     * @param callable $proceed
     * @return bool
     */
    public function aroundCanRenderBillingAddress(AddressRenderer $subject, callable $proceed): bool
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            return $quote->getBillingAddress()->validate() === true;
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return false;
        }
    }


    public function aroundRenderBillingAddress(AddressRenderer $subject, callable $proceed, string $code): string
    {
        try {
            $quote = $this->sessionCheckout->getQuote();
            $addressBilling = $quote->getBillingAddress();
            if ($addressBilling) {
                return $subject->renderCustomerAddress($addressBilling->exportCustomerAddress(), $code);
            }

            throw new NotFoundException(__('%1 address could not be found.', 'Billing'));
        } catch (LocalizedException | NoSuchEntityException $exception) {
            return __('%1 address can not be shown due to a technical malfunction.', 'Billing')->render();
        }
    }

    private function setBillingAddressAsShippingAddress(): bool
    {
        $quote = $this->sessionCheckout->getQuote();
        if (false === $this->hasSameAsBilling($quote)) {
            return false;
        }

        $addressBilling = $quote->getBillingAddress();
        $quote->setShippingAddress($addressBilling);
        return true;
    }

    private function hasSameAsBilling(CartInterface|Quote $quote): bool
    {
        if (!$quote->isVirtual()) {
            return false;
        }

        $addressShipping = $quote->getShippingAddress();
        if (!$addressShipping->getOriginalSameAsBilling()) {
            return false;
        }

        return true;
    }
}
