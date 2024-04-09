<?php

namespace Lodipay\PayumQPay\Action;

use Lodipay\PayumQPay\Enum\PaymentStatus;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{
    /**
     * @param GetStatusInterface $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        switch ($model['status']) {
            case PaymentStatus::STATE_NEW->value:
                $request->markNew();
                break;
            case PaymentStatus::STATE_PROCESSING->value:
                $request->markPending();
                break;
            case PaymentStatus::STATE_PAID->value:
                $request->markCaptured();
                break;
            default:
                $request->markFailed();
        }
    }

    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface
            && $request->getModel() instanceof \ArrayAccess
        ;
    }
}
