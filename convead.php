<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Convead Module
 */
class Convead extends MY_Controller {

    protected $settings;
    protected $conveadClient;

    public function __construct() {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('convead');
        $this->load->model('convead_model');
        $this->settings = $this->convead_model->getSettings();
    }

    public function index() {
        $this->template->registerMeta("ROBOTS", "NOINDEX, NOFOLLOW");
        $this->core->error_404();
    }

    /**
     * Bind events
     */
    public function autoload() {
        if ($this->settings['enabled'] == '1' and !empty($this->settings['app_key'])) {
            // Inject JS only for non-ajax requests
            if (!$this->input->is_ajax_request()) {
              $this->RegisterConveadMainScript();
              CMSFactory\Events::create()->onProductPageLoad()->setListener('ViewProductEvent');  
            }
            
            // Bind update cart events
            CMSFactory\Events::create()->onAddItemToCart()->setListener('UpdateCartEvent');
            CMSFactory\Events::create()->onRemoveFromCart()->setListener('UpdateCartEvent');
            CMSFactory\Events::create()->onSetQuantity()->setListener('UpdateCartEvent');
            CMSFactory\Events::create()->onEmptyCart()->setListener('UpdateCartEvent');

            // Bind purchase event
            CMSFactory\Events::create()->onShopMakeOrder()->setListener('PurchaseEvent');
        }
    }

    /**
     * Send 'view_product' event
     */
    public static function ViewProductEvent($data) {
        $model = $data['model'];
        $template_data = array(
            'id' => $model->firstVariant->getId(),
            'name' => $model->getName(),
            'url' => shop_url('product/'.$model->getUrl()),
            'category_id' => $model->getCategories()->getData()[0]->getId()
          );

        $convead_js = \CMSFactory\assetManager::create()
                        ->setData($template_data)
                        ->fetchTemplate('product');

        \CMSFactory\assetManager::create()
            ->registerJsScript($convead_js, FALSE, 'before');
    }

    /**
     * Send 'update_cart' event
     */
    public static function UpdateCartEvent($data) {
      try {
        $mod_convead = new Convead(false);

        if ($mod_convead->settings['enabled'] == '0' || empty($mod_convead->settings['app_key'])) {
          return;
        }
        
        $mod_convead->initConveadClient();
        $eventItems = array();
        $cartItems = \Cart\BaseCart::getInstance()->getItems();

        // Cast cart items to Convead event format
        foreach ($cartItems['data'] as $item) {
          $eventItems[] = array(
            'product_id' => $item->data['id'],
            'qnt' => $item->data['quantity'],
            'price' => $item->data['price']
          );
        }

        $mod_convead->conveadClient->eventUpdateCart($eventItems);

      } catch (Exception $e) {
        return true;
      }
    }

    /**
     * Send 'purchase' event
     */
    public static function PurchaseEvent($data) {
      try {

        $mod_convead = new Convead(false);

        if ($mod_convead->settings['enabled'] == '0' || empty($mod_convead->settings['app_key'])) {
          return;
        }
        
        $order = $data['order'];

        // Order id and total
        $order_id = $order->id;
        $revenue = $data['price'];

        // Get order items
        $orderItems = array();
        foreach ($order->getSOrderProductss() as $item) {
          $product = $item->getSProducts();
          $orderItems[] = array(
            'product_id' => $item->variant_id,
            'qnt' => $item->getQuantity(),
            'price' => $item->toCurrency()
          );
        }

        // Get visitor info
        $visitor_info = array(
          'first_name' => $order->user_full_name,
          'last_name' => $order->user_famil,
          'phone' => $order->user_phone,
          'email' => $order->user_email
        );

        $mod_convead->initConveadClient($visitor_info);
        $mod_convead->conveadClient->eventOrder($order_id, $revenue, $orderItems);

      } catch (Exception $e) {
        return true;
      }
    }

    /**
     * Install module
     */
    public function _install() {
        $this->load->dbforge();

        $fields = array(
          'id' => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE,),
          'name' => array('type' => 'VARCHAR', 'constraint' => 50,),
          'value' => array('type' => 'VARCHAR', 'constraint' => 255,)
          );

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_field($fields);
        $this->dbforge->create_table('mod_convead', TRUE);

        // Prefill table with initial settings
        $data = array(
          array('name' => 'app_key', 'value' => NULL),
          array('name' => 'enabled', 'value' => 'FALSE'),
        );
        $this->db->insert_batch('mod_convead', $data);

        $this->db->where('name', 'convead')
          ->update('components', array('autoload' => '1', 'enabled' => '1'));
    }

    /**
     * Uninstall module
     */
    public function _deinstall() {
        $this->load->dbforge();
        $this->dbforge->drop_table('mod_convead');
    }


    /**
     * Inject Convead main script
     */
    private function RegisterConveadMainScript() {
      $data = array();
      $data['app_key'] = $this->settings['app_key'];
      if ($this->dx_auth->is_logged_in()) {
        $data['visitor_uid'] = $this->dx_auth->get_user_id();
        $data['visitor_info'] = array(
          'first_name' => $this->dx_auth->get_username(),
          'email' => $this->dx_auth->get_user_email()
        );
      }

      $convead_js = \CMSFactory\assetManager::create()
                        ->setData($data)
                        ->fetchTemplate('main');

      \CMSFactory\assetManager::create()
          ->registerJsScript($convead_js, FALSE, 'before');

    }

    /**
     * Initialize Convead API client
     */
    public function initConveadClient($visitor_info = NULL) {
        include_once('convead_api/ConveadTracker.php');

        $app_key     = $this->settings['app_key'];
        $host        = $_SERVER['HTTP_HOST'];
        $guest_uid   = (isset($_COOKIE['convead_guest_uid']) ? $_COOKIE['convead_guest_uid'] : '');
        $visitor_uid = ($this->dx_auth->is_logged_in() ? $this->dx_auth->get_user_id() : false);

        $this->conveadClient = new ConveadTracker($app_key, $host, $guest_uid, $visitor_uid, $visitor_info);
    }

}

/* End of file convead.php */
