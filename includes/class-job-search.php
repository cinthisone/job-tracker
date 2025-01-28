<?php
if (!defined('ABSPATH')) exit;

class JOB_Search {
    public static function enqueue_scripts() {
        wp_enqueue_script('job-search-js', JOB_TRACKER_URL . 'js/job-search.js', ['jquery'], null, true);
        wp_enqueue_style('job-style', JOB_TRACKER_URL . 'css/job-style.css');

        wp_localize_script('job-search-js', 'jobSearch', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url_raw(rest_url('job-tracker/v1/add-job')),
            'delete_url' => esc_url_raw(rest_url('job-tracker/v1/delete-job')),
            'nonce' => wp_create_nonce('wp_rest')
        ]);

        wp_enqueue_script('job-tinymce', 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.7.0/tinymce.min.js', [], null, true);
    }

    public static function ajax_search() {
        $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

        $args = [
            'post_type' => 'applied_jobs',
            'posts_per_page' => 10,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        // If a search term is entered, filter results
        if (!empty($query)) {
            $args['s'] = $query;
        }

        $search_query = new WP_Query($args);
        $results = [];

        if ($search_query->have_posts()) {
            while ($search_query->have_posts()) {
                $search_query->the_post();
                $company = get_post_meta(get_the_ID(), 'company_name', true);
                $url = get_post_meta(get_the_ID(), 'job_url', true);
                $date_applied = get_post_meta(get_the_ID(), 'date_applied', true);
                $cover_letter = get_post_meta(get_the_ID(), 'cover_letter', true);
                $content = get_the_content();

                $results[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'content' => apply_filters('the_content', $content),
                    'company' => $company ? $company : '',
                    'url' => $url ? $url : '',
                    'date_applied' => $date_applied ? $date_applied : '',
                    'cover_letter' => $cover_letter ? apply_filters('the_content', $cover_letter) : ''
                ];
            }
            wp_reset_postdata();
        }

        wp_send_json_success($results);
        wp_die();
    }
}

// Register AJAX Actions
add_action('wp_ajax_job_search', ['JOB_Search', 'ajax_search']);
add_action('wp_ajax_nopriv_job_search', ['JOB_Search', 'ajax_search']);
