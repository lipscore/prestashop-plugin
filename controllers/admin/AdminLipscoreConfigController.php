<?php

use Komplettnettbutikk\Lipscore\LipscoreClient;

class AdminLipscoreConfigController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->fields_options = [
            'options' => [
                'title' => $this->l('Innstillinger'),
                'fields' => [
                    LipscoreClient::config_prefix . 'api_key' => [
                        'title' => $this->l('API key'),
                        'type' => 'text',
                        'desc' => $this->l('Go to your LipScore member page > Settings > General > API settings, where you must copy the API key to paste here. Be careful not to include spaces in the code.'),
                    ],
                    LipscoreClient::config_prefix . 'secret_api_key' => [
                        'title' => $this->l('Secret API key'),
                        'type' => 'text',
                        'desc' => $this->l('Go to your LipScore member page > Settings > General > API settings, where you must copy the Secret API key to paste here. Be careful not to include spaces in the code.'),
                    ],
                    LipscoreClient::config_prefix . 'transfer_order_state' => [
                        'title' => $this->l('Send invitation on ordrestate'),
                        'type' => 'select',
                        'identifier' => 'id_order_state',
                        'class' => 'w-100',
                        'list' => OrderState::getOrderStates((int) Configuration::get('PS_LANG_DEFAULT')),
                    ],
                    LipscoreClient::config_prefix . 'product_identifier' => [
                        'title' => $this->l('Product identifier'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'class' => 'w-100',
                        'list' => [
                            ['type' => 'id_product', 'name' => $this->l('Product-ID')],
                            ['type' => 'product_reference', 'name' => $this->l('Product-reference')],
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitOptions',
                ],
            ],
            'placements' => [
                'title' => $this->l('Placements'),
                'fields' => [
                    LipscoreClient::config_prefix . 'displayFooter' => [
                        'title' => $this->l('Footer testemonials'),
                        'type' => 'bool',
                    ],
                    LipscoreClient::config_prefix . 'displayProductListReviews' => [
                        'title' => $this->l('Product miniature stars'),
                        'type' => 'bool',
                    ],
                    LipscoreClient::config_prefix . 'displayProductAdditionalInfo' => [
                        'title' => $this->l('Product stars on product details page'),
                        'type' => 'bool',
                    ],
                    LipscoreClient::config_prefix . 'displayFooterProduct' => [
                        'title' => $this->l('Form and reviews in product footer'),
                        'type' => 'bool',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitOptions',
                ],
            ],
        ];

        $this->content.= $this->module->fetch('module:'.$this->module->name.'/views/templates/info.tpl'); 
        
    }

    public function postProcess()
    {
        $return = parent::postProcess();
        $this->testApi();
        return $return;
    }

    public function testApi()
    {
        try {
            if (LipscoreClient::checkConfig()) {
                $lipscore = new LipscoreClient();
                if ($lipscore->getProducts()) {
                    $this->confirmations[] = $this->l('⭐️⭐️⭐️⭐️⭐️ LipScore API is now in communication with your webshop! ⭐️⭐️⭐️⭐️⭐️');
                    Configuration::updateValue(LipscoreClient::config_prefix . 'api_up', true);
                } else {
                    Configuration::updateValue(LipscoreClient::config_prefix . 'api_up', false);
                    $this->errors[] = $this->l('Unfortunately there is something wrong with the LipScore API connection 🤔 Check API-Keys!');
                }
            }
        } catch (Exception $e) {
            $this->errors[] = $this->l('Unfortunately an exception was thrown during LipScore API connection 🤔 Check API-Keys!');
        }
    }
}
