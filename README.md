# YireoTraining ExampleHyvaCheckoutReorderSteps
Example Hyva Checkout module to introduce a new custom checkout "Yireo Example Checkout" (`example`) that swaps the ordering of shipment and payment step.

**This module is abandoned and no longer maintained. We have moved to our new [LokiCheckout](https://loki-checkout.com/) instead.**


### TODO
- Make sure to show address form by default on first step, move "My billing and shipping address are the same" to second step
- Use `\Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigBilling::canApplyShippingAsBillingAddress` everywhere
