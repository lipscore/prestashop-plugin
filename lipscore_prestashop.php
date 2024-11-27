<?php
require_once __DIR__ . '/vendor/autoload.php';

use Lipscore\Prestashop\LipscoreClient;
use Lipscore\Prestashop\LipscoreConfig;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class lipscore_prestashop extends Module implements WidgetInterface
{
    /**
     * @var LipscoreConfig
     */
    protected $config;

    public function __construct() {
        $this->name = "lipscore_prestashop";
        $this->displayName = $this->l('Lipscore');
        $this->description = $this->l('Lipscore integration for PrestaShop');
        $this->tab = 'front_office_features';
        $this->version = "1.5.0";
        $this->author = "Lipscore";
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->errors = [];
        $this->config = new LipscoreConfig();
        parent::__construct();

        $this->hooks = [
            'displayAdminOrder', 
            'actionOrderStatusPostUpdate',
            'actionAdminControllerSetMedia',
            'actionFrontControllerSetMedia',
            'displayFooterProduct',
            'displayProductListReviews',
            'displayProductAdditionalInfo',
            'displayFooter',
            'displayHeader'
        ];  
    }

    public function renderWidget($hookName, array $configuration)
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        
        if ($hookName == 'displayFooter' && $this->config->getDisplayFooter()) {
            return $this->fetch('module:'.$this->name.'/views/templates/hooks/displayFooter.tpl');
        }

        if ($this->config->getDisplayForHook($hookName)
            && ($hookName == 'displayFooterProduct' || $hookName == 'displayProductAdditionalInfo' || $hookName == 'displayProductListReviews')
        ) {
            return $this->fetch('module:'.$this->name.'/views/templates/hooks/'.$hookName.'.tpl');
        }

        return '';
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $variables = [
            'height' => '100px', 
            'width' => '250px'
        ];

        if (isset($configuration['product'])) {
            $identifier = $gtinIdentifier = $skuIdentifier = $mpnIdentifier = 'id';

            $configIdentifier = $this->config->getProductIdentifierAttribute();
            if ($configIdentifier) {
                $identifier = $configIdentifier;
            }
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
            /** @var PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray $product */
            $product = $configuration['product'];

            $productImage = $product['cover'];

            if (isset($productImage['large'])) {
                $variables['imageUrl'] = $productImage['large']['url'];
            }

            $variantId = $variantName = '';
            $attributes = $product['attributes'];
            if (!empty($attributes)) {
                $variantId = '';
                $variantName = [$product['name']];
                foreach ($attributes as $attribute) {
                    $variantName[] = sprintf('%s %s', $attribute['group'], $attribute['name']);
                    $variantId = $attribute['reference'];
                }

                $variantName = implode(' - ', $variantName);
                $variantId = $product['reference'] . '-' . $variantId;
            }

            $brandValue = Configuration::get('PS_SHOP_NAME');
            $brandAttribute = $this->config->getProductBrandAttribute();
            if ($brandAttribute) {
                $brandValue = $product['manufacturer_name'];
            }

            $context = Context::getContext();
            $currency = $context->currency;

            $variables = array_merge($variables, [
                'id' => $product[$identifier],
                'mpn' => $product[$mpnIdentifier],
                'sku' => $product[$skuIdentifier],
                'gtin' => $product[$gtinIdentifier],
                'name' => $product['name'],
                'brand' => $brandValue,
                'url' => $product['url'],
                'category' => $product['category_name'],
                'stock' => $product['quantity'],
                'description' => $product['meta_description'],
                'variantId' => $variantId,
                'variantName' => $variantName,
                'showPrice' => $product['show_price']
            ]);

            if ($product['show_price']) {
                $variables['price'] = $product['rounded_display_price'];
                $variables['currency'] = $currency->iso_code;
            }
        }
        return $variables;
    }

    public function hookDisplayHeader($params)
    {
        $variables = [
            'apiKey' => $this->config->getApiKey(),
            'locale' => $this->config->getLocale(),
        ];

        $this->smarty->assign($variables);
        return $this->fetch('module:' . $this->name . '/views/templates/hooks/displayInit.tpl');
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addCss($this->_path.'views/css/admin.css');
    }

    public function hookActionFrontControllerSetMedia($params)
    {
        $this->context->controller->registerJavascript('module-'.$this->name.'-script-bottom', 
            'modules/'.$this->name.'/views/js/init_front_bottom.js', [
              'position' => 'bottom',
        ]);
        $this->context->controller->registerStylesheet('module-'.$this->name.'-styles', 
            'modules/'.$this->name.'/views/css/front.css', [
        ]);
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        if (!$this->config->getApiUp()) {
            return;
        }

        if ($params['newOrderStatus']->id != $this->config->getTransfertOrderState()
            && $params['newOrderStatus']->id != $this->config->getTransfertOrderState2()
        ) {
            return;
        }
       
        $lipscore = new LipscoreClient;
        if ($lipscore->createInvitation(new Order($params['id_order']))) {
            $this->context->controller->confirmations[] = $this->l('Created Lipscore invite! PS: it may take some time before the email is sent to the customer. Check Lipscore Dashboard for status.');
        } else {
            $this->context->controller->errors[] = $this->l('Could not send Lipscore invite! Please check your Lipscore Dashboard.');
        }
    }

  
	public function install()
	{		
        return parent::install() 
            && $this->registerHook($this->hooks) 
            && $this->installTab('AdminLipscoreConfig', $this->l('Lipscore'), 'IMPROVE')
        ;
    }

    protected function installTab($class_name, $tab_name, $parent_controller)
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->icon = 1;
        $tab->class_name  = $class_name;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $this->l($tab_name);
        }
        $tab->id_parent = Tab::getIdFromClassName($parent_controller);
        $tab->module = $this->name;
        $tab->add();
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminLipscoreConfig'));        
    } 
}