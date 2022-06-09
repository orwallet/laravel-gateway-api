<?php

namespace Btph\GatewaySdk;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

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
    private function client(): PendingRequest
    {
        return Http::withHeaders([
            "Accept" => "application/json",
            "X-GATEWAY-KEY" => $this->public_key,
            "X-GATEWAY-SECRET" => $this->digest()
        ]);
    }

    /**
     * Undocumented function
     *
     * @param string $url
     * @param array $intent
     * @return Response
     */
    private function request(string $url, array $intent): Response
    {
        return $this->client()->post($this->api_url . $url, [
            "customer" => $this->customer,
            "details" => $intent,
        ]);
    }

    /**
     * Undocumented function
     *
     * @return string
     */
    private function digest(): string
    {
        return md5($this->secret_key . $this->public_key);
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
        return $this->request("/withdraw/intent", $intent)->json();
    }

    /**
     * Undocumented function
     *
     * @param array $intent
     * @return Response
     */
    public function createDepositIntent(array $intent): array
    {
        return $this->request("/deposit/intent", $intent)->json();
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
