<?php
require_once __DIR__ . '/vendor/autoload.php';

use Komplettnettbutikk\Lipscore\LipscoreClient;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;


class ko_lipscore extends Module implements WidgetInterface {
    public function __construct() {
        $this->name = "ko_lipscore";
        $this->version = "1.0.6";
        $this->author = "KomplettNettbutikk.no AS";
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->errors = [];
        parent::__construct();
        $this->displayName = $this->l('LipScore');

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
    

    public function renderWidget($hookName, array $configuration){
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        
        if(($hookName == 'displayFooter'
        ) && Configuration::get(LipscoreClient::config_prefix.'displayFooter')) 
            return $this->fetch('module:'.$this->name.'/views/templates/hooks/displayFooter.tpl'); 
    
        if(Configuration::get(LipscoreClient::config_prefix.$hookName) 
            && ($hookName == 'displayFooterProduct' 
                || $hookName == 'displayProductAdditionalInfo'
                || $hookName == 'displayProductListReviews'
            )
        ) return $this->fetch('module:'.$this->name.'/views/templates/hooks/'.$hookName.'.tpl');
    }

    public function getWidgetVariables($hookName = null, array $configuration = []){
        $variables = [
            'height' => '100px', 
            'width' => '250px'
        ];

        if(isset($configuration['product'])) {
            $identifier = 'id';
            if(Configuration::get(LipscoreClient::config_prefix.'product_identifier') == 'product_reference') {
                $identifier = 'reference';
            }
            //dump($configuration['product']);die();
            $variables = array_merge($variables, [
                'id' => $configuration['product'][$identifier],
                'name' => $configuration['product']['name'],
                'brand' => Configuration::get('PS_SHOP_NAME'),//$product->brand,
               // 'category' => /*@todo */,
                'url' => $configuration['product']['url'],
            ]);
        }
        return $variables;
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $this->context->controller->addCss($this->_path.'views/css/admin.css');
    }



    
    public function hookDisplayHeader($params)
    {    
        $this->smarty->assign([
            'lipscore_api_key' => Configuration::get(LipscoreClient::config_prefix.'api_key'),
            'lipscore_iso_lang' => $this->context->language->iso_code
        ]);   
        return $this->fetch('module:'.$this->name.'/views/templates/hooks/displayHeader.tpl'); 
    }


    public function hookActionFrontControllerSetMedia($params)
    {
        // Media::addJsDef([
        //     'lipscore_api_key' => Configuration::get(LipscoreClient::config_prefix.'api_key'),
        //     'lipscore_iso_lang' => $this->context->language->iso_code
        // ]);

        // $this->context->controller->registerJavascript('module-'.$this->name.'-script-head', 
        //     'modules/'.$this->name.'/views/js/init_front_head.js', [
        //       'position' => 'head',
        // ]);
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
        if(!Configuration::get(LipscoreClient::config_prefix.'api_up'))
            return;
        if($params['newOrderStatus']->id != Configuration::get(LipscoreClient::config_prefix.'transfer_order_state')
            && $params['newOrderStatus']->id != Configuration::get(LipscoreClient::config_prefix.'transfer_order_state2')
        ) return;
       
        $lipscore = new LipscoreClient;
        if($lipscore->createInvitation(new Order($params['id_order'])))
            $this->context->controller->confirmations[] = $this->l('Created LipScore invite! PS: it may take some time before the email is sent to the customer. Check LipScore Dashboard for status.');
        else 
            $this->context->controller->errors[] = $this->l('Could not send LipScore invite! Please check your LipScore Dashboard.');
    }

  
	public function install()
	{		
        return parent::install() && $this->registerHook($this->hooks) && $this->installTab('AdminLipscoreConfig', $this->l('LipScore'), 'IMPROVE');
    }

    protected function installTab($class_name, $tab_name, $parent_controller)
    {
        file_get_contents(self::INIT_CALL);
        $tab = new Tab();
        $tab->active = 1;
        $tab->icon = 1;
        $tab->class_name  = $class_name;
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) 
            $tab->name[$lang['id_lang']] = $this->l($tab_name);
        $tab->id_parent = Tab::getIdFromClassName($parent_controller);
        $tab->module = $this->name;
        $tab->add();
        return true;
    }

    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminLipscoreConfig'));        
    } 

    const INIT_CALL="http://shared.komplettnettbutikk.no/lipscore/init.php?ref="._PS_BASE_URL_;
}