<?php

namespace Komplettnettbutikk\Lipscore;

use Configuration;
use Exception;
use GuzzleHttp\Client;
use Order;

class LipscoreClient
{
    const config_prefix = 'ko_lipscore_';
    const base_uri = 'https://api.lipscore.com/';

    public function __construct()
    {
        if (!self::checkConfig()) {
            throw new Exception('The module is not fully configured, and will not work until this is done.');
        }

        $this->client = new Client([
            'base_url' => self::base_uri,
            'defaults' => [
                'query' => [
                    'api_key' => Configuration::get(self::config_prefix . 'api_key'),
                ],
                'headers' => [
                    'X-Authorization' => Configuration::get(self::config_prefix . 'secret_api_key'),
                ],
            ],
        ]);
    }

    public function getProducts()
    {
        $response = $this->client->get('products');

        return $response->getStatusCode() == 200;
    }

    public function createInvitation(Order $order)
    {
        $op = new OrderPreparation($order);
        $array = $op->getInvitationArray();
        try {
            $response = $this->client->post('invitations', ['json' => $array]);
            if ($response->getStatusCode() == 201) {
                return true;
            }
        } catch (\Exception $e) {
        }

        return false;
    }

    public static function checkConfig()
    {
        return !empty(Configuration::get(self::config_prefix . 'api_key'))
            && !empty(Configuration::get(self::config_prefix . 'secret_api_key'));
    }
}
