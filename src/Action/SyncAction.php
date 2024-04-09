<?php

namespace Lodipay\PayumQPay\Action;

use Lodipay\PayumQPay\Action\Api\BaseApiAwareAction;
use Lodipay\PayumQPay\Enum\PaymentStatus;
use Lodipay\PayumQPay\Request\CheckPayment;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Sync;

final class SyncAction extends BaseApiAwareAction
{
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var Sync $request */
        $details = ArrayObject::ensureArrayObject($request->getModel());

        if ($details['status'] === PaymentStatus::STATE_PROCESSING->value) {
            $this->gateway->execute(new CheckPayment($details));
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof Sync
            && $request->getModel() instanceof \ArrayObject
        ;
    }
}
