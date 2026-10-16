<?php

//===============================================================
//====================STANDARD GEMSTONE CODES====================
//=============AVOID PUTTING YOUR CODE IN THIS FILE==============
//=============AVOID CHANGING ANYTHING IN THIS FILE==============
//===============================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class GS_Module {

    protected $CI;
    protected $module_name = "Unknown";
    protected $module_version = 1;
    protected $module_code = "MOD001";
    protected $module_description = "A gemstone module.";
    protected $required_gemstone_version = "3.8.6";
    protected $required_module = [];
    protected $required_table = [];

    function __construct() {
        $this->CI = & get_instance();
        $this->initialize();
    }

    public function get_module_detail() {
        $response = new stdClass();
        $response->module_name = $this->module_name;
        $response->module_version = $this->module_version;
        $response->module_code = $this->module_code;
        $response->module_description = $this->module_description;
        $response->required_gemstone_version = $this->required_gemstone_version;
        $response->required_module = $this->required_module;
        $response->required_table = $this->required_table;
        return $response;
    }

    public function check_gemstone_version() {
        $current_version = explode(".", GEMSTONE_VERSION_NUMBER);
        $required_version = explode(".", $this->required_gemstone_version);
        $version_check = TRUE;
        for ($i = 0; $i < count($current_version); $i++) {
            if ((int) $current_version[$i] < (int) $required_version[$i]) {
                $version_check = FALSE;
                break;
            } else if ((int) $current_version[$i] > (int) $required_version[$i]) {
                break;
            }
        }

        return $version_check;
    }

    public function initialize() {
        $check = $this->CI->gemstone_model->check_module_installed($this);
        if ($check->installed === FALSE) {
            $db_ready = TRUE;
            //echo "Initializing $this->module_name<br>";
            foreach ($this->required_module as $module_name) {
                //echo "Checking table $table_name: ";
                if (class_exists($module_name) == FALSE) {
                    $db_ready = FALSE;
                    log_message("error", "Required module: $module_name not found.");
                    //echo "Not found<br>";
                } else {
                    //echo "Found<br>";
                }
            }
            foreach ($this->required_table as $table_name) {
                //echo "Checking table $table_name: ";
                if ($this->CI->db->table_exists($table_name) == FALSE) {
                    $db_ready = FALSE;
                    //echo "Not found<br>";
                } else {
                    //echo "Found<br>";
                }
            }
            $gemstone_check = $this->check_gemstone_version();
            if ($gemstone_check === TRUE) {
                if ($db_ready === FALSE) {
                    //echo "Required table not complete<br>";
                    log_message("error", "Database for $this->module_name module not installed yet.");
                    $this->install();
                } else {
                    
                }
            } else {
                log_message("error", "Module $this->module_name requires at least Gemstone version $this->required_gemstone_version to run properly.");
            }
        } else {
            $installed_module = $check->installed_module;
            $installed_version = (int) $installed_module->module_version;
            $this->module_version = (int) $this->module_version;
            if ($this->module_version > $installed_version) {
                for ($i = $installed_version; $i <= $this->module_version; $i++) {
                    $patch_check = $this->patch_module($i);
                    if ($patch_check === FALSE) {
                        log_message("error", "Failed patching module $this->module_name to version $i.");
                        break;
                    } else {
                        log_message("error", "Patched module $this->module_name to version $i.");
                    }
                }
            }
        }
    }

    public function require_table($table_name) {
        $this->required_table[] = $table_name;
    }

    public function require_module($module_name) {
        $this->required_module[] = $module_name;
    }

    /**
     * A mandatory function for each Gemstone module that will be called if the table structure is not found yet
     */
    public function install() {
        log_message("debug", "Installing database for $this->module_name module...");
    }

    /**
     * A mandatory function for each Gemstone module that will be called if programmer wants to uninstall a module
     */
    public function uninstall() {
        log_message("debug", "Uninstalling database for $this->module_name module...");
    }

    /**
     * A mandatory function for each Gemstone module that will be called if installed module version is lower than this version
     */
    public function patch_module($version_number) {
        //use these lines for patching non db-related changes
        //        switch ($version_number) {
        //            case 1: {
        //                    break;
        //                }
        //            default: {
        //                    break;
        //                }
        //        }
        //use this line for patching db-related changes
        //$this->CI->customer_model->patch_module($version_number);
    }

}

//===============================================================
//====================STANDARD GEMSTONE CODES====================
//=============AVOID PUTTING YOUR CODE IN THIS FILE==============
//=============AVOID CHANGING ANYTHING IN THIS FILE==============
//===============================================================
