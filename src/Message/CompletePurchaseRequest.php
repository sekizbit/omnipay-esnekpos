<?php

namespace Omnipay\Esnekpos\Message;

use Omnipay\Common\Message\AbstractRequest;
use Omnipay\Esnekpos\Exceptions\OmnipayEsnekposFetchTransactionRequestException;
use Omnipay\Esnekpos\Traits\PurchaseGettersSetters;

class CompletePurchaseRequest extends AbstractRequest
{
    use PurchaseGettersSetters;

    private string $endpoint = '/api/services/ProcessQuery';

    public function getData(): array
    {
        $transactionId = $this->httpRequest->get('ORDER_REF_NUMBER')
            ?? $this->httpRequest->request->get('ORDER_REF_NUMBER');

        $this->validate('merchant', 'merchant_key');

        return [
            'MERCHANT'         => $this->getMerchant(),
            'MERCHANT_KEY'     => $this->getMerchantKey(),
            'ORDER_REF_NUMBER' => $transactionId,
        ];
    }

    public function sendData($data): CompletePurchaseResponse
    {
        $httpResponse = $this->httpClient->request(
            'POST',
            $this->getEndpoint(),
            [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        if ($httpResponse->getStatusCode() !== 200) {
            throw new OmnipayEsnekposFetchTransactionRequestException('CompletePurchase sırasında bir hata oluştu.', $httpResponse->getStatusCode());
        }

        return new CompletePurchaseResponse(
            $this,
            $httpResponse,
            $data['ORDER_REF_NUMBER']
        );
    }
}
