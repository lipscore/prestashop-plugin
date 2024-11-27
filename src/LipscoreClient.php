<?php

namespace Lipscore\Prestashop;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Exception;
use Order;

class LipscoreClient
{
    /**
     * @var HttpClientInterface
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

        $this->client = HttpClient::create([
            'base_uri' => $this->config->getBaseUri(),
            'query' => [
                'api_key' => $this->config->getApiKey(),
            ],
            'headers' => [
                'X-Authorization' => $this->config->getApiSecretKey(),
            ],
        ]);
    }

    public function getProducts()
    {
        try {
            $response = $this->client->request('GET', 'products');

            return $response->getStatusCode() == 200;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function createInvitation(Order $order)
    {
        $op = new OrderPreparation($order);
        $array = $op->getInvitationArray();
        try {
            $response = $this->client->request('POST', 'invitations', [
                'json' => $array,
            ]);
            if ($response->getStatusCode() == 201) {
                return true;
            }
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }

    public function checkConfig()
    {
        return !empty($this->config->getApiKey()) && !empty($this->config->getApiSecretKey());
    }
}
