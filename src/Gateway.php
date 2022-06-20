<?php

namespace Btph\GatewaySdk;

use Exception;
use GuzzleHttp\Client;

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

    public function getAuthHeaders()
    {
        return [
            "Accept" => "application/json",
            "Content-Type" => "application/json",
            "X-GATEWAY-KEY" => $this->public_key,
            "X-GATEWAY-SECRET" => md5($this->secret_key . $this->public_key)
        ];
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

    private function request(string $url, array $data): array
    {
        $payload = [];

        try {
            $response = (new Client())->request("POST", $this->api_url . $url, [
                "headers" => $this->getAuthHeaders(),
                "json" => $data
            ]);
            $payload = $response->getBody();
        } catch (Exception $e) {
            if (!method_exists($e, "getResponse")) {
                throw $e;
            }

            $response = $e->getResponse();

            if (!method_exists($response, "getBody")) {
                throw $e;
            }
            $payload = $response->getBody();
        }

        return json_decode($payload, true);
    }

    /**
     * Undocumented function
     *
     * @param array $intent
     * @return Response
     */
    public function createWithdrawalIntent(array $intent): array
    {
        return $this->request("/withdraw/intent",  [
            "customer" => $this->customer,
            "details" => $intent
        ]);
    }

    /**
     * Undocumented function
     *
     * @param array $intent
     * @return Response
     */
    public function createDepositIntent(array $intent): array
    {
        return $this->request("/deposit/intent",  [
            "customer" => $this->customer,
            "details" => $intent
        ]);
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
        return $this->request("/withdraw/{$transaction_number}", $details);
    }
}
