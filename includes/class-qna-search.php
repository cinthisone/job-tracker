<?php
if (!defined('ABSPATH')) exit;

class QNA_Search {
    public static function enqueue_scripts() {
        wp_enqueue_script('qna-search-js', QNA_STORAGE_URL . 'js/qna-search.js', ['jquery'], null, true);
        wp_enqueue_style('qna-style', QNA_STORAGE_URL . 'css/qna-style.css');

        wp_localize_script('qna-search-js', 'qnaSearch', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url_raw(rest_url('qna-storage/v1/add-question')),
            'delete_url' => esc_url_raw(rest_url('qna-storage/v1/delete-question')),
            'nonce' => wp_create_nonce('wp_rest')
        ]);

        wp_enqueue_script('qna-tinymce', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.0/tinymce.min.js', [], null, true);
    }

    public static function ajax_search() {
        if (!isset($_POST['query'])) {
            wp_send_json_error('No query provided');
            wp_die();
        }

        $query = sanitize_text_field($_POST['query']);
        $args = [
            'post_type' => 'job_questions',
            's' => $query,
            'posts_per_page' => 10
        ];

        $search_query = new WP_Query($args);
        $results = [];

        if ($search_query->have_posts()) {
            while ($search_query->have_posts()) {
                $search_query->the_post();
                $company = get_post_meta(get_the_ID(), 'company_name', true);
                $url = get_post_meta(get_the_ID(), 'question_url', true);
                $results[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'content' => get_the_excerpt(),
                    'company' => $company ? $company : '',
                    'url' => $url ? $url : ''
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json($results);
        wp_die();
    }
}

// Register AJAX Actions
add_action('wp_ajax_qna_search', ['QNA_Search', 'ajax_search']);
add_action('wp_ajax_nopriv_qna_search', ['QNA_Search', 'ajax_search']);
