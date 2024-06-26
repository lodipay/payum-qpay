<?php

namespace Lodipay\PayumQPay;

use Lodipay\Qpay\Api\DTO\CheckPaymentRequest;
use Lodipay\Qpay\Api\DTO\CheckPaymentResponse;
use Lodipay\Qpay\Api\DTO\CreateInvoiceRequest;
use Lodipay\Qpay\Api\DTO\CreateInvoiceResponse;
use Lodipay\Qpay\Api\DTO\GetInvoiceResponse;
use Lodipay\Qpay\Api\DTO\Offset;
use Lodipay\Qpay\Api\Enum\Env;
use Lodipay\Qpay\Api\Enum\ObjectType;
use Lodipay\Qpay\Api\QPayApi;

class Api
{
    private QPayApi $client;

    /**
     * @param array<string, mixed> $defaultOptions
     */
    public function __construct(
        private string $username,
        private string $password,
        private Env|string $env,
        private string $invoiceCode,
        array $defaultOptions = [],
    ) {
        $this->client = new QPayApi(
            username: $this->username,
            password: $this->password,
            env: is_string($this->env) ? Env::from($this->env) : $this->env,
            options: $defaultOptions,
        );
    }

    /**
     * @param array<string, mixed> $details
     */
    public function createInvoice(array $details): CreateInvoiceResponse
    {
        return $this->client->createInvoice(
            CreateInvoiceRequest::from(
                array_merge(['invoiceCode' => $this->invoiceCode], $details),
            ),
        );
    }

    public function getInvoice(string $invoiceId): GetInvoiceResponse
    {
        return $this->client->getInvoice($invoiceId);
    }

    public function checkPayment(
        ObjectType $objectType,
        string $objectId,
        ?Offset $offset = null,
    ): CheckPaymentResponse {
        return $this->client->checkPayment(CheckPaymentRequest::from([
            'objectType' => $objectType->value,
            'objectId' => $objectId,
            'offset' => $offset ?? Offset::from([
                'pageNumber' => 1,
                'pageLimit' => 100,
            ]),
        ]));
    }
}
