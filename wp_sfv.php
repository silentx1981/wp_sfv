<?php

/*
 * Plugin Name: Sfv Integration
 * Plugin URI: https://wyssinet.ch/WpSfv
 * Description: Damit können Inhalte von der SFV-API im Wordpress integriert werden
 * Author: Raffael Wyss
 * Author: URI: https://wyssinet.ch
 * Version: 1.0.0
 */

require_once(WP_PLUGIN_DIR.'/wp_sfv/vendor/autoload.php');

if (!defined('WPINC'))
    die;

class wp_sfv {
    public function __construct()
    {

    }

    public static function activate()
    {
        $createDb = new \wpSfv\Db\CreateDb();
        $createDb->run();
    }

    public static function deactivate()
    {
        $dropDb = new \wpSfv\Db\DropDb();
        $dropDb->run();
    }
}

register_activation_hook(__FILE__, ['wp_sfv', 'activate']);
register_deactivation_hook( __FILE__, ['wp_sfv', 'deactivate']);
\wpSfv\Wp\AddShortcodes::run();
\wpSfv\Wp\AddActions::run();
new \wpSfv\Wp\AdminPage();