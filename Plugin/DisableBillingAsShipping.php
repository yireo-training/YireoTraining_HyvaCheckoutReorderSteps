<?php declare(strict_types=1);

namespace YireoTraining\ExampleHyvaCheckoutReorderSteps\Plugin;

use Hyva\Checkout\Magewire\Checkout\AddressView\BillingDetails;
use Magento\Checkout\Model\Session as SessionCheckout;

class DisableBillingAsShipping
{
    public function __construct(
        private readonly SessionCheckout $sessionCheckout
    ) {
    }

    /**
     * @param BillingDetails $subject
     * @param null $result
     * @return void
     */
    public function afterBoot(BillingDetails $subject, $result): void
    {
        $addressShipping = $this->sessionCheckout->getQuote()->getShippingAddress();
        $addressShipping->setOriginalSameAsBilling($addressShipping->getSameAsBilling());
        $addressShipping->setSameAsBilling(false);
    }
}