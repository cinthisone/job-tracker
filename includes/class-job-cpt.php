<?php
if (!defined('ABSPATH')) exit;

class JOB_CPT {
    public static function register_cpt() {
        register_post_type('applied_jobs', [
            'labels'      => [
                'name'          => 'Applied Jobs',
                'singular_name' => 'Applied Job'
            ],
            'public'      => true,
            'has_archive' => true,
            'supports'    => ['title', 'editor', 'custom-fields'],
            'menu_icon'   => 'dashicons-briefcase'
        ]);
    }
}

// Register the custom post type
add_action('init', ['JOB_CPT', 'register_cpt']);
