<?php declare(strict_types=1);

namespace YireoTraining\ExampleHyvaCheckoutReorderSteps\Form;

use Hyva\Checkout\Magewire\Checkout\AddressView\MagewireAddressFormInterface;
use Hyva\Checkout\Model\Form\EntityFormInterface;
use Hyva\Checkout\Model\Form\EntityFormModifierInterface;
use Magento\Checkout\Model\Session as SessionCheckout;

class ShippingAddressFormModifier implements EntityFormModifierInterface
{
    public function __construct(
        private readonly SessionCheckout $sessionCheckout
    ) {
    }

    public function apply(EntityFormInterface $form): EntityFormInterface
    {
        $form->registerModificationListener(
            'YireoTraining_ExampleHyvaCheckoutReorderSteps::formInit',
            'form:init',
            function(EntityFormInterface $form) {
                return $form;
            }
        );

        $form->registerModificationListener(
            'YireoTraining_ExampleHyvaCheckoutReorderSteps::formFill',
            'form:fill',
            function(EntityFormInterface $form) {
            }
        );

        return $form;
    }
}