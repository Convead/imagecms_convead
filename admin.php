<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS 
 * Convead Module Admin
 */
class Admin extends BaseAdminController {

    public function __construct() {
        parent::__construct();
        $lang = new MY_Lang();
        $lang->load('convead');
        $this->load->model('convead_model');
    }

    public function index() {
        $settings = $this->db->get('mod_convead')->result();
        foreach ($settings as $item) {
          $data[$item->name] = $item->value;
        }

        \CMSFactory\assetManager::create()
          ->setData($data)
          ->renderAdmin('settings');
    }

    /**
     * Saves settings in the table
     */
    public function save() {
        if ($this->dx_auth->is_admin() && $this->input->is_ajax_request()) {
            $this->convead_model->setSettings();
            $this->lib_admin->log(lang("Convead settings were updated", "convead"));
        }
    }
}