<?php

namespace Btph\GatewaySdk;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class Gateway
{
    /**
     * Undocumented variable
     *
     * @var array
     */
    private array $customer;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private string $public_key;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private string $secret_key;

    /**
     * Undocumented variable
     *
     * @var string
     */
    private string $api_url;

    public function __construct()
    {
        $this->api_url = config("gateway.API_URL");
        $this->public_key = config("gateway.API_KEY");
        $this->secret_key = config("gateway.SECRET_KEY");
    }

    /**
     * Undocumented function
     *
     * @return PendingRequest
     */
    private function client(string $reference_no): PendingRequest
    {
        return Http::withHeaders([
            "Accept" => "application/json",
            "X-GATEWAY-KEY" => $this->public_key,
            "X-GATEWAY-SECRET" => $this->digest($reference_no)
        ]);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param array $intent
     * @return Response
     */
    private function request(string $url, array $details): Response
    {
        return $this->client(Arr::get($details, "details.reference_no"))->post($this->api_url . $url, $details);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    private function digest(string $reference_no): string
    {
        return hash_hmac("sha256", $this->public_key . $reference_no, $this->secret_key);
    }

    /**
     * Undocumented function
     *
     * @param array $customer
     * @return self
     */
    public function attachCustomer(array $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Undocumented function
     *
     * @param array $intent
     * @return Response
     */
    public function createWithdrawalIntent(array $intent): array
    {
        return $this->request("/withdraw/intent", [
            "customer" => $this->customer,
            "details" => $intent,
        ])->json();
    }

    /**
     * Undocumented function
     *
     * @param array $intent
     * @return Response
     */
    public function createDepositIntent(array $intent): array
    {
        return $this->request("/deposit/intent", [
            "customer" => $this->customer,
            "details" => $intent,
        ])->json();
    }

    /**
     * Undocumented function
     *
     * @param integer $transaction_number
     * @param array $details
     * @return Response
     */
    public function processWithdrawalIntent(int $transaction_number, array $details): array
    {
        return $this->request("/withdraw/{$transaction_number}", $details)->json();
    }
}
