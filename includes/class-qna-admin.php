<?php
if (!defined('ABSPATH')) exit;

class QNA_Admin {
    public static function add_menu() {
        add_menu_page('Q&A Storage', 'Q&A Storage', 'manage_options', 'qna-storage', ['QNA_Admin', 'render_admin_page']);
    }

    public static function render_admin_page() {
        echo '<div class="wrap"><h1>Q&A Storage</h1>';
        echo '<p>Use the Job Questions post type to store and manage Q&A.</p>';
        echo '</div>';
    }
}

add_action('admin_menu', ['QNA_Admin', 'add_menu']);
