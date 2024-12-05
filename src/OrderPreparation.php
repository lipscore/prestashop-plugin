<?php

namespace Lipscore\Prestashop;

use Address;
use Configuration;
use Context;
use Exception;
use Order;
use PrestaShop\PrestaShop\Adapter\Validate;
use Product;

class OrderPreparation
{
    /**
     * @var LipscoreConfig
     */
    protected $config;

    public function __construct(Order $order)
    {
        if (!Validate::isLoadedObject($order)) {
            throw new Exception('No orders were loaded');
        }

        $this->config = new LipscoreConfig();
        $this->ps_order = $order;
        $this->ps_customer = $this->ps_order->getCustomer();
    }

    public function getCustomerInfo()
    {
        $return = [];
        $delivery_address = new Address($this->ps_order->id_address_delivery);
        $invoice_address = new Address($this->ps_order->id_address_invoice);

        if (isset($this->ps_customer->company) && $this->ps_customer->company != '') {
            $return['buyer_name'] = (string) $this->ps_customer->company;
        } elseif (isset($invoice_address->company) && $invoice_address->company != '') {
            $return['buyer_name'] = (string) $invoice_address->company;
        } elseif (isset($delivery_address->company) && $delivery_address->company != '') {
            $return['buyer_name'] = (string) $delivery_address->company;
        } else {
            $return['buyer_name'] = (string) $this->ps_customer->firstname . ' ' . $this->ps_customer->lastname;
        }

        $return['buyer_email'] = (string) $this->ps_customer->email;

        return $return;
    }

    public function getOrderLines()
    {
        $order_lines = $this->ps_order->getProducts();
        $productIdentifier = $this->config->getProductIdentifierAttribute();
        $currency = new \Currency($this->ps_order->id_currency);
        $return = [];

        $configSkuIdentifier = $this->config->getProductSkuAttribute();
        if ($configSkuIdentifier) {
            $skuIdentifier = $configSkuIdentifier;
        }
        $configGtinIdentifier = $this->config->getProductGtinAttribute();
        if ($configGtinIdentifier) {
            $gtinIdentifier = $configGtinIdentifier;
        }
        $configMpnIdentifier = $this->config->getProductMpnAttribute();
        if ($configMpnIdentifier) {
            $mpnIdentifier = $configMpnIdentifier;
        }

        foreach ($order_lines as $order_line) {
            $lang = $this->ps_order->id_lang;
            $product = new Product($order_line['id_product'], true, $lang);
            $category = $product->id_category_default;
            $categoryName = '';
            if ($category) {
                $category = new \Category($category, $lang);
                $categoryName = $category->name;
            }

            $variantId = $variantName = '';
            $productAttributeId = $order_line['product_attribute_id'];
            if (!empty($productAttributeId)) {
                $combination = new \Combination($productAttributeId, $lang);
                $variantName = [$product->name];
                foreach ($combination->getAttributesName($lang) as $attribute) {
                    $variantName[] = $attribute['name'];
                }

                $variantName = implode(' - ', $variantName);
                $variantId = $product->reference . '-' . $combination->reference;
            }

            $link = Context::getContext()->link;
            $image = \Image::getCover($order_line['id_product']);
            $imagePath = '';
            if (isset($image['id_image'])) {
                $imagePath = $link->getImageLink($product->link_rewrite, $image['id_image'], 'large-default');
            }

            $brandValue = Configuration::get('PS_SHOP_NAME', $lang);
            $brandAttribute = $this->config->getProductBrandAttribute();
            if ($brandAttribute) {
                $brandValue = $product->manufacturer_name;
            }

            $return[] = [
                'internal_id' => $order_line[$productIdentifier],
                'variant_id' => $variantId,
                'variant_name' => $variantName,
                'brand' => $brandValue,
                'url' => $link->getProductLink($product),
                'name' => $product->name,
                'price' => $order_line['unit_price_tax_incl'],
                'image_url' => $imagePath,
                'currency' => $currency->iso_code,
                'category' => $categoryName,
                'mpn' => $product->{$mpnIdentifier},
                'gtin' => $product->{$gtinIdentifier},
                'sku_values' => $product->{$skuIdentifier}
            ];
        }

        return $return;
    }

    public function getInvitationArray()
    {
        $buyer = $this->getCustomerInfo();
        $shop = new \Shop($this->ps_order->id_shop);
        $idCustomer = '';
        if ($this->ps_order->id_customer > 0) {
            $idCustomer = (string) $this->ps_order->id_customer;
        }

        return [
            'invitation' => [
                'buyer_email' => $buyer['buyer_email'],
                'buyer_name' => $buyer['buyer_name'],
                'purchased_at' => $this->ps_order->date_add,
                'lang' => $this->config->getLocale(),
                'parent_source_id' => (string) $this->config->getParentSourceId(),
                'parent_source_name' => (string) $this->config->getParentSourceName(),
                'source_id' => (string) $shop->id,
                'source_name' => (string) $shop->name,
                'internal_order_id' => $this->ps_order->reference,
                'internal_customer_id' => $idCustomer
            ],
            'products' => $this->getOrderLines(),
        ];
    }
}
