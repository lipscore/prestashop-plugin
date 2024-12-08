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

    public function getHooks()
    {
        try {
            $response = $this->client->request('GET', 'hooks');

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
                'timeout' => 1
            ]);
            if ($response->getStatusCode() == 201) {
                return true;
            }
        } catch (Exception $e) {
            $this->log($e->getMessage());
        }
        return false;
    }

    public function checkConfig()
    {
        return !empty($this->config->getApiKey()) && !empty($this->config->getApiSecretKey());
    }

    protected function log($message): void
    {
        if (!_PS_MODE_DEV_) {
            return;
        }

        \PrestaShopLogger::addLog($message, 2);
    }
}
