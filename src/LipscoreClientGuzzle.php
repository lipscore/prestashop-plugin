<?php

namespace Lipscore\Prestashop;

use Exception;
use GuzzleHttp\Client;
use Order;

class LipscoreClientGuzzle
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var LipscoreConfig
     */
    protected $config;

    public function __construct()
    {
        $this->config = new LipscoreConfig();
        if (!$this->checkConfig()) {
            throw new Exception('The module is not fully configured, and will not work until this is done.');
        }

        $this->client = new Client([
            'base_url' => $this->config->getBaseUri(),
            'defaults' => [
                'query' => [
                    'api_key' => $this->config->getApiKey(),
                ],
                'headers' => [
                    'X-Authorization' => $this->config->getApiSecretKey(),
                ],
            ],
        ]);
    }

    public function getProducts()
    {
        $response = $this->client->get($this->config->getBaseUri() . 'products');

        return $response->getStatusCode() == 200;
    }

    public function createInvitation(Order $order)
    {
        $op = new OrderPreparation($order);
        $array = $op->getInvitationArray();
        try {
            $response = $this->client->post($this->config->getBaseUri() . 'invitations', ['json' => $array]);
            if ($response->getStatusCode() == 201) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    public function checkConfig()
    {
        return !empty($this->config->getApiKey()) && !empty($this->config->getApiSecretKey());
    }
}
