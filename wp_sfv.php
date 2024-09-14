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

\wpSfv\Wp\AddShortcodes::run();