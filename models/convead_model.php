<?php

/**
 * @property CI_DB_active_record $db
 * @property DX_Auth $dx_auth
 */
class Convead_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }


    /**
     * Get module settings
     */
    public function getSettings($locale = 'ru') {
        $settings = array();
        $mod_table = $this->db->get('mod_convead');
        if ($mod_table) {
          foreach ($mod_table->result() as $row)
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    /**
     * Save module settings
     */
    public function setSettings() {
        $enabled = ($this->input->post('enabled') == 'on') ? '1' : 0;
        $this->db->where('name', 'enabled')->update('mod_convead', array('value' => $enabled));
        $this->db->where('name', 'app_key')->update('mod_convead', array('value' => $this->input->post('app_key')));
    }

}

?>
