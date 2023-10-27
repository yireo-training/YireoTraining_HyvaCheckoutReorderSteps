<?php declare(strict_types=1);

namespace YireoTraining\HyvaCheckoutReorderSteps\ViewModel;

use Magento\Checkout\Model\Session as SessionCheckout;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class QuoteIsVirtual implements ArgumentInterface
{

    public function __construct(
        private SessionCheckout $sessionCheckout
    ) {
    }

    public function isVirtual(): bool
    {
        return $this->sessionCheckout->getQuote()->isVirtual();
    }
}
