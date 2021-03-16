<?php

namespace Komplettnettbutikk\Lipscore;

use Address;
use Configuration;
use Context;
use Exception;
use Order;
use PrestaShop\PrestaShop\Adapter\Validate;
use Product;

class OrderPreparation
{
    public function __construct(Order $order)
    {
        if (!Validate::isLoadedObject($order)) {
            throw new Exception('Ingen ordre ble lastet');
        }

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
        $return = [];
        foreach ($order_lines as $order_line) {
            $return[] = [
                'internal_id' => $order_line[Configuration::get(LipscoreClient::config_prefix . 'product_identifier')],
                'url' => Context::getContext()->link->getProductLink(new Product($order_line['id_product'])),
                'name' => $order_line['product_name'],
            ];
        }

        return $return;
    }

    public function getInvitationArray()
    {
        $buyer = $this->getCustomerInfo();

        return [
            'invitation' => [
                'buyer_email' => $buyer['buyer_email'],
                'buyer_name' => $buyer['buyer_name'],
                'lang' => Context::getContext()->language->iso_code,
            ],
            'products' => $this->getOrderLines(),
        ];
    }
}
