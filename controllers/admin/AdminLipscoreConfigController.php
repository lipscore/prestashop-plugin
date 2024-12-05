<?php

use Lipscore\Prestashop\LipscoreClient;
use Lipscore\Prestashop\LipscoreConfig;

class AdminLipscoreConfigController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->fields_options = [
            'options' => [
                'title' => $this->l('Settings'),
                'fields' => [
                    LipscoreConfig::PATH_LOCALE => [
                        'title' => $this->l('Lang'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'desc' => $this->l('Specify language for Lipscore widgets'),
                        'list' => [
                            ['type' => 'br',   'name' => $this->l('Brazilian')],
                            ['type' => 'cz',   'name' => $this->l('Czech')],
                            ['type' => 'dk',   'name' => $this->l('Danish')],
                            ['type' => 'nl',   'name' => $this->l('Dutch')],
                            ['type' => 'en',   'name' => $this->l('English')],
                            ['type' => 'et',   'name' => $this->l('Estonian')],
                            ['type' => 'fi',   'name' => $this->l('Finnish')],
                            ['type' => 'fr',   'name' => $this->l('French')],
                            ['type' => 'de',   'name' => $this->l('German')],
                            ['type' => 'it',   'name' => $this->l('Italian')],
                            ['type' => 'ja',   'name' => $this->l('Japanese')],
                            ['type' => 'lv',   'name' => $this->l('Latvian')],
                            ['type' => 'no',   'name' => $this->l('Norwegian')],
                            ['type' => 'pl',   'name' => $this->l('Polish')],
                            ['type' => 'br',   'name' => $this->l('Portuguese (Brazil)')],
                            ['type' => 'ru',   'name' => $this->l('Russian')],
                            ['type' => 'sk',   'name' => $this->l('Slovak')],
                            ['type' => 'es',   'name' => $this->l('Spanish')],
                            ['type' => 'se',   'name' => $this->l('Swedish')],
                        ],
                    ],
                    LipscoreConfig::PATH_API_KEY => [
                        'title' => $this->l('API key'),
                        'type' => 'text',
                        'desc' => $this->l('Go to your Lipscore member page > Settings > General > API settings, where you must copy the API key to paste here. Be careful not to include spaces in the code.'),
                    ],
                    LipscoreConfig::PATH_SECRET_API_KEY => [
                        'title' => $this->l('Secret API key'),
                        'type' => 'text',
                        'desc' => $this->l('Go to your Lipscore member page > Settings > General > API settings, where you must copy the Secret API key to paste here. Be careful not to include spaces in the code.'),
                    ],
                    LipscoreConfig::PATH_TRANSFER_ORDER_STATE => [
                        'title' => $this->l('Send invitation on order state'),
                        'type' => 'select',
                        'identifier' => 'id_order_state',
                        'class' => 'w-100',
                        'list' => OrderState::getOrderStates((int) Configuration::get('PS_LANG_DEFAULT')),
                    ],
                    LipscoreConfig::PATH_TRANSFER_ORDER_STATE2 => [
                        'title' => $this->l('Also send invitation on order state'),
                        'type' => 'select',
                        'identifier' => 'id_order_state',
                        'class' => 'w-100',
                        'list' => OrderState::getOrderStates((int) Configuration::get('PS_LANG_DEFAULT')),
                    ],
                    LipscoreConfig::PATH_PRODUCT_IDENTIFIER => [
                        'title' => $this->l('Product identifier'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'class' => 'w-100',
                        'list' => [
                            ['type' => 'id_product', 'name' => $this->l('Product ID')],
                            ['type' => 'reference', 'name' => $this->l('Product reference')],
                            ['type' => 'isbn', 'name' => $this->l('Product ISBN')],
                            ['type' => 'ean13', 'name' => $this->l('Product EAN13')],
                            ['type' => 'upc', 'name' => $this->l('Product UPC')],
                            ['type' => 'mpn', 'name' => $this->l('Product MPN')],
                        ],
                    ],
                    LipscoreConfig::PATH_PRODUCT_SKU => [
                        'title' => $this->l('Product SKU'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'class' => 'w-100',
                        'list' => [
                            ['type' => 'reference', 'name' => $this->l('Product reference')],
                            ['type' => 'id_product', 'name' => $this->l('Product ID')],
                            ['type' => 'isbn', 'name' => $this->l('Product ISBN')],
                            ['type' => 'ean13', 'name' => $this->l('Product EAN13')],
                            ['type' => 'upc', 'name' => $this->l('Product UPC')],
                            ['type' => 'mpn', 'name' => $this->l('Product MPN')],
                        ],
                    ],
                    LipscoreConfig::PATH_PRODUCT_GTIN => [
                        'title' => $this->l('Product GTIN'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'class' => 'w-100',
                        'list' => [
                            ['type' => 'reference', 'name' => $this->l('Product reference')],
                            ['type' => 'id_product', 'name' => $this->l('Product ID')],
                            ['type' => 'isbn', 'name' => $this->l('Product ISBN')],
                            ['type' => 'ean13', 'name' => $this->l('Product EAN13')],
                            ['type' => 'upc', 'name' => $this->l('Product UPC')],
                            ['type' => 'mpn', 'name' => $this->l('Product MPN')],
                        ],
                    ],
                    LipscoreConfig::PATH_PRODUCT_MPN => [
                        'title' => $this->l('Product MPN'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'class' => 'w-100',
                        'list' => [
                            ['type' => 'mpn', 'name' => $this->l('Product MPN')],
                            ['type' => 'reference', 'name' => $this->l('Product reference')],
                            ['type' => 'id_product', 'name' => $this->l('Product ID')],
                            ['type' => 'isbn', 'name' => $this->l('Product ISBN')],
                            ['type' => 'ean13', 'name' => $this->l('Product EAN13')],
                            ['type' => 'upc', 'name' => $this->l('Product UPC')],
                        ],
                    ],
                    LipscoreConfig::PATH_PRODUCT_BRAND => [
                        'title' => $this->l('Product Brand'),
                        'type' => 'select',
                        'identifier' => 'type',
                        'class' => 'w-100',
                        'list' => [
                            ['type' => '', 'name' => $this->l('Use Shop name')],
                            ['type' => 'attribute', 'name' => $this->l('Product Manufacturer')],
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
                    LipscoreConfig::PATH_DISPLAY_FOOTER => [
                        'title' => $this->l('Testimonial widget in footer'),
                        'type' => 'bool',
                    ],
                    LipscoreConfig::PATH_DISPLAY_PRODUCT_LIST_REVIEWS => [
                        'title' => $this->l('Small rating widget on product page'),
                        'type' => 'bool',
                    ],
                    LipscoreConfig::PATH_DISPLAY_PRODUCT_ADDITIONAL_INFO=> [
                        'title' => $this->l('Rating widget on product details page'),
                        'type' => 'bool',
                    ],
                    LipscoreConfig::PATH_DISPLAY_PRODUCT_FOOTER => [
                        'title' => $this->l('Review list in product footer'),
                        'type' => 'bool',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'name' => 'submitOptions',
                ],
            ],
        ];
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
            $lipscore = new LipscoreClient();
            if ($lipscore->checkConfig()) {
                if ($lipscore->getHooks()) {
                    $this->confirmations[] = $this->l('Lipscore API is now in communication with your webshop!');
                    Configuration::updateValue(LipscoreConfig::PATH_API_UP, true);
                } else {
                    Configuration::updateValue(LipscoreConfig::PATH_API_UP . 'api_up', false);
                    throw new \Exception($this->l('Unfortunately there is something wrong with the Lipscore API connection. Please verify API keys.'));
                }
            }
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
    }
}
