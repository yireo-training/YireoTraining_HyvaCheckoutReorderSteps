# YireoTraining ExampleHyvaCheckoutReorderSteps
**Example Hyva Checkout module to introduce a new custom checkout "Yireo Example Checkout" (`example`) that swaps the ordering of shipment and payment step.**

### TODO
- Make sure to show address form by default on first step, move "My billing and shipping address are the same" to second step
- Use `\Hyva\Checkout\Model\ConfigData\HyvaThemes\SystemConfigBilling::canApplyShippingAsBillingAddress` everywhere