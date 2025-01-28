<?php
/*
Plugin Name: Job Tracker
Plugin URI: https://github.com/cinthisone/job-tracker
Description: A WordPress plugin to track jobs you have applied for, featuring AJAX search, frontend submission, and REST API.
Version: 1.0.0
Author: Chansamone Inthisone
Author URI: https://github.com/cinthisone
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!defined('ABSPATH')) exit;

// Define new constants for Job Tracker
define('JOB_TRACKER_PATH', plugin_dir_path(__FILE__));
define('JOB_TRACKER_URL', plugin_dir_url(__FILE__));

// Require necessary files
require_once JOB_TRACKER_PATH . 'includes/class-job-cpt.php';
require_once JOB_TRACKER_PATH . 'includes/class-job-admin.php';
require_once JOB_TRACKER_PATH . 'includes/class-job-search.php';
require_once JOB_TRACKER_PATH . 'includes/class-job-api.php';
require_once JOB_TRACKER_PATH . 'includes/class-job-shortcode.php'; // ✅ This line was missing

function job_tracker_enqueue_scripts() {
    wp_enqueue_script('job-tinymce', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.0/tinymce.min.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'job_tracker_enqueue_scripts');


class JOB_Storage {
    public function __construct() {
        add_action('init', ['JOB_CPT', 'register_cpt']);
        add_action('wp_enqueue_scripts', ['JOB_Search', 'enqueue_scripts']);
        add_action('wp_ajax_job_search', ['JOB_Search', 'ajax_search']);
        add_action('wp_ajax_nopriv_job_search', ['JOB_Search', 'ajax_search']);
    }
}

// Initialize plugin
new JOB_Storage();
