<?php

namespace Omnipay\Esnekpos\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Esnekpos\Constants\Statuses;
use Omnipay\Esnekpos\Models\FetchTransactionResponseModel;

class CompletePurchaseResponse extends AbstractResponse
{
    protected ?array $transaction = null;

    protected string $transactionId;

    public function __construct(RequestInterface $request, $data, string $transactionId)
    {
        parent::__construct($request, $data);

        $this->transactionId = $transactionId;
        $this->data          = new FetchTransactionResponseModel(
            json_decode($data->getBody(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function isSuccessful(): bool
    {
        if ($this->data->RETURN_CODE !== '0') {
            return false;
        }

        return collect($this->data->TRANSACTIONS)
            ->contains('STATUS_ID', Statuses::PAYMENT_SUCCESS);
    }

    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    public function getTransactionReference(): ?string
    {
        return $this->data->SUCCESS_TRANSACTION_ID ?? $this->data->REFNO ?? null;
    }

    public function getCode(): ?string
    {
        return $this->data->RETURN_CODE ?? null;
    }

    public function getMessage(): ?string
    {
        return $this->data->RETURN_MESSAGE ?? null;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function setTransaction(array $transaction): void
    {
        $this->transaction = $transaction;
    }
}
