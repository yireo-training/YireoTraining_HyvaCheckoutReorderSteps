<?php declare(strict_types=1);

namespace YireoTraining\HyvaCheckoutReorderSteps\Magewire\Checkout\AddressView;

use Hyva\Checkout\Magewire\Checkout\AddressView\ShippingDetails as OriginalShippingDetails;
use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Customer\Model\Session as SessionCustomer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\QuoteRepository;
use Magento\QuoteGraphQl\Model\Cart\QuoteAddressFactory;

class ShippingDetails extends OriginalShippingDetails
{
    public bool $billingAsShipping = false;

    public function __construct(
        private readonly SessionCheckout $sessionCheckout,
        private readonly SessionCustomer $sessionCustomer,
        private QuoteRepository $quoteRepository,
        private QuoteAddressFactory $quoteAddressFactory
    ) {
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function boot(): void
    {
        $addressShipping = $this->sessionCheckout->getQuote()->getShippingAddress();
        $this->billingAsShipping = (bool) $addressShipping->getOriginalSameAsBilling();
    }

    public function updatedBillingAsShipping(bool $value): bool
    {
        try {
            $quote = $this->sessionCheckout->getQuote();

            $addressShipping = $quote->getShippingAddress();
            $addressShipping->setSameAsBilling($value);

            $quote = $this->toggleBillingAsShipping($quote, $value);
            $this->quoteRepository->save($quote);
        } catch (LocalizedException $exception) {
            $this->dispatchErrorMessage('Something went wrong while saving your billing preferences: '.$exception->getMessage());
            $value = ! $value;
        }

        return $value;
    }

    public function toggleBillingAsShipping(Quote $quote, bool $value): Quote
    {
        $address = $quote->getShippingAddress();

        if ($value === false) {
            $addressBillingPrimary = $this->sessionCustomer->getCustomer()->getPrimaryBillingAddress();

            if ($addressBillingPrimary) {
                $quote->getBillingAddress()->setCustomerAddressId($addressBillingPrimary->getId());
                return $quote;
            }

            // Handover the shipping address object for later usage.
            $addressShipping = $address;

            $address = $this->quoteAddressFactory->create();
            $address->importCustomerAddressData($addressShipping->exportCustomerAddress());
            $address->setCustomerAddressId($addressShipping->getCustomerAddressId());
        }

        $quote->getBillingAddress()
            ->importCustomerAddressData($address->exportCustomerAddress())
            ->setCustomerAddressId($address->getCustomerAddressId());

        return $quote;
    }

    /**
     * @deprecated
     * @see BillingDetails::toggleBillingAsShipping()
     */
    public function unsetBillingAsShipping(Quote $quote, Address $address)
    {
        $addressBillingPrimary = $this->sessionCustomer->getCustomer()->getPrimaryBillingAddress();

        $quote->getBillingAddress()
            ->importCustomerAddressData($address->exportCustomerAddress())
            ->setCustomerAddressId($addressBillingPrimary
                ? $addressBillingPrimary->getId()
                : $address->getCustomerAddressId());
    }
}
