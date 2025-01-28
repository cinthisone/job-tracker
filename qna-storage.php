<?php
/*
Plugin Name: Q&A Storage
Plugin URI: https://github.com/cinthisone/qna-storage
Description: A WordPress plugin to store and search job application questions and answers with AJAX. Features a custom post type, AJAX live search, and a frontend submission form with a WYSIWYG editor.
Version: 1.0.0
Author: Chansamone Inthisone
Author URI: https://github.com/cinthisone
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

define('QNA_STORAGE_PATH', plugin_dir_path(__FILE__));
define('QNA_STORAGE_URL', plugin_dir_url(__FILE__));

require_once QNA_STORAGE_PATH . 'includes/class-qna-cpt.php';
require_once QNA_STORAGE_PATH . 'includes/class-qna-admin.php';
require_once QNA_STORAGE_PATH . 'includes/class-qna-search.php';
require_once QNA_STORAGE_PATH . 'includes/class-qna-shortcode.php';
require_once QNA_STORAGE_PATH . 'includes/class-qna-api.php';


class QNA_Storage {
    public function __construct() {
        add_action('init', ['QNA_CPT', 'register_cpt']);
        add_action('wp_enqueue_scripts', ['QNA_Search', 'enqueue_scripts']);
        add_action('wp_ajax_qna_search', ['QNA_Search', 'ajax_search']);
        add_action('wp_ajax_nopriv_qna_search', ['QNA_Search', 'ajax_search']);
    }
}

new QNA_Storage();
