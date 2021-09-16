<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Elementor Import/Export
 * Description:       A simple plugin for importing/exporting Elementor post data
 * Version:           1.0.0
 * Author:            Mark Biek
 * Author URI:        http://mark.biek.org
 */
ini_set('display_errors', true);

if (!defined('WPINC')) {
	die();
}

require_once __DIR__ . '/vendor/autoload.php';

use ElImEx\Loader;

$plugin = new Loader(__DIR__, plugin_dir_url(__FILE__));
$plugin->init();
