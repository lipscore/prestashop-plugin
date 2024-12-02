<?php

namespace Lipscore\Prestashop;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Configuration;
use Exception;
use Order;

class LipscoreConfig
{
    const PATH_API_KEY = 'ko_lipscore_api_key';
    const BASE_URI = 'https://api.lipscore.com/';
    const PATH_API_UP = 'ko_lipscore_api_up';
    const PATH_LOCALE = 'ko_lipscore_locale';
    const DEFAULT_LOCALE = 'en';
    const PATH_SECRET_API_KEY = 'ko_lipscore_secret_api_key';
    const PATH_TRANSFER_ORDER_STATE = 'ko_lipscore_transfer_order_state';
    const PATH_TRANSFER_ORDER_STATE2 = 'ko_lipscore_transfer_order_state2';
    const PATH_PRODUCT_IDENTIFIER = 'ko_lipscore_product_identifier';
    const PATH_PRODUCT_SKU = 'ko_lipscore_product_sku';
    const PATH_PRODUCT_GTIN = 'ko_lipscore_product_gtin';
    const PATH_PRODUCT_MPN = 'ko_lipscore_product_mpn';
    const PATH_PRODUCT_BRAND = 'ko_lipscore_product_brand';
    const PATH_PREFIX = 'ko_lipscore_';
    const PATH_DISPLAY_FOOTER = 'ko_lipscore_displayFooter';
    const PATH_DISPLAY_PRODUCT_FOOTER = 'ko_lipscore_displayFooterProduct';
    const PATH_DISPLAY_PRODUCT_ADDITIONAL_INFO = 'ko_lipscore_displayProductAdditionalInfo';
    const PATH_DISPLAY_PRODUCT_LIST_REVIEWS = 'ko_lipscore_displayProductListReviews';

    const PARENT_SOURCE_ID = 'prestashop';
    const PARENT_SOURCE_NAME = 'PrestaShop';

    /**
     * @var Configuration
     */
    protected $prestaConfig;

    public function getConfig()
    {
        if (!$this->prestaConfig) {
            $this->prestaConfig = new \Configuration();
        }

        return $this->prestaConfig;
    }

    public function getApiKey()
    {
        return $this->getConfig()->get(self::PATH_API_KEY);
    }

    public function getApiSecretKey()
    {
        return $this->getConfig()->get(self::PATH_SECRET_API_KEY);
    }

    public function getApiUp()
    {
        return $this->getConfig()->get(self::PATH_API_UP);
    }

    public function getBaseUri()
    {
        return self::BASE_URI;
    }

    public function getLocale()
    {
        $locale = $this->getConfig()->get(self::PATH_LOCALE);
        if (!$locale) {
            $locale = self::DEFAULT_LOCALE;
        }

        return $locale;
    }

    public function getTransfertOrderState()
    {
        return $this->getConfig()->get(self::PATH_TRANSFER_ORDER_STATE);
    }

    public function getTransfertOrderState2()
    {
        return $this->getConfig()->get(self::PATH_TRANSFER_ORDER_STATE2);
    }

    public function getProductIdentifierAttribute()
    {
        return $this->getConfig()->get(self::PATH_PRODUCT_IDENTIFIER);
    }

    public function getProductSkuAttribute()
    {
        return $this->getConfig()->get(self::PATH_PRODUCT_SKU);
    }

    public function getProductMpnAttribute()
    {
        return $this->getConfig()->get(self::PATH_PRODUCT_MPN);
    }

    public function getProductGtinAttribute()
    {
        return $this->getConfig()->get(self::PATH_PRODUCT_GTIN);
    }

    public function getProductBrandAttribute()
    {
        return $this->getConfig()->get(self::PATH_PRODUCT_BRAND);
    }

    public function getDisplayFooter()
    {
        return $this->getConfig()->get(self::PATH_DISPLAY_FOOTER);
    }

    public function getParentSourceId()
    {
        return self::PARENT_SOURCE_ID;
    }

    public function getParentSourceName()
    {
        return self::PARENT_SOURCE_NAME . ' ' . _PS_VERSION_;
    }

    public function getDisplayForHook($hookName = '')
    {
        return $this->getConfig()->get(self::PATH_PREFIX . $hookName);
    }

    public function getDisplayProductFooter()
    {
        return $this->getConfig()->get(self::PATH_DISPLAY_PRODUCT_FOOTER);
    }

    public function getDisplayProductAdditionalInfo()
    {
        return $this->getConfig()->get(self::PATH_DISPLAY_PRODUCT_ADDITIONAL_INFO);
    }

    public function getDisplayProductListReviews()
    {
        return $this->getConfig()->get(self::PATH_DISPLAY_PRODUCT_LIST_REVIEWS);
    }
}
