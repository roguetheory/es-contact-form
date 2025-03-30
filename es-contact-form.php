<?php
/**
* Plugin Name: ES Contact Form
* Plugin URI: https://ethersense.com
* Description: A WordPress contact form plugin using Vue.js and Ajax
* Version: 1.0.0
* Author: Ethersense
* Author URI: https://ethersense.com
* Text Domain: es-contact-form
*/

if (!defined('ABSPATH')) {
exit;
}

define('ES_CONTACT_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ES_CONTACT_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));


require_once ES_CONTACT_FORM_PLUGIN_DIR . 'includes/EsContactForm.php';
use ethersense\EsContactForm\EsContactForm;
new EsContactForm();

