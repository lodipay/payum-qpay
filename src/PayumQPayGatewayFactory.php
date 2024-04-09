<?php

namespace Lodipay\PayumQPay;

use Lodipay\PayumQPay\Action\Api\CheckPaymentAction;
use Lodipay\PayumQPay\Action\Api\CreateInvoiceAction;
use Lodipay\PayumQPay\Action\CaptureAction;
use Lodipay\PayumQPay\Action\ConvertPaymentAction;
use Lodipay\PayumQPay\Action\ConvertQPayAction;
use Lodipay\PayumQPay\Action\NotifyAction;
use Lodipay\PayumQPay\Action\StatusAction;
use Lodipay\PayumQPay\Action\SyncAction;
use Lodipay\Qpay\Api\Enum\Env;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use Webmozart\Assert\Assert;

class PayumQPayGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'qpay',
            'payum.factory_title' => 'QPay',
            'payum.action.capture' => new CaptureAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.sync' => new SyncAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.convert_qpay' => new ConvertQPayAction(),
            'payum.action.check_payment' => new CheckPaymentAction(),
            'payum.action.create_invoice' => new CreateInvoiceAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'env' => 'sandbox',
                'username' => '',
                'password' => '',
                'options' => [],
            ];
            /* @phpstan-ignore-next-line */
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty(['username', 'password', 'invoiceCode']);

                Assert::string($config['username']);
                Assert::string($config['password']);
                Assert::string($config['invoiceCode']);
                Assert::string($config['env']);

                /** @var ?array<string, mixed> $options */
                $options = $config['options'];

                $api = new Api(
                    $config['username'],
                    $config['password'],
                    Env::from($config['env']),
                    $config['invoiceCode'],
                    $options ?? []
                );

                return $api;
            };
        }
    }
}
