<?php
if (!defined('ABSPATH')) exit;

class QNA_CPT {
    public static function register_cpt() {
        register_post_type('job_questions', [
            'labels' => [
                'name' => 'Job Questions',
                'singular_name' => 'Job Question',
            ],
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'editor', 'custom-fields'],
            'menu_position' => 25,
            'menu_icon' => 'dashicons-editor-help',
        ]);
    }
}
