<?php
if (!defined('ABSPATH')) exit;

class JOB_API {
    public static function register_routes() {
        register_rest_route('job-tracker/v1', '/add-job', [
            'methods' => 'POST',
            'callback' => ['JOB_API', 'add_job'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('job-tracker/v1', '/delete-job', [
            'methods' => 'POST',
            'callback' => ['JOB_API', 'delete_job'],
            'permission_callback' => '__return_true'
        ]);
    }

    public static function add_job($request) {
        $params = $request->get_json_params();

        if (empty($params['title'])) {
            return new WP_Error('missing_title', 'Job title is required.', ['status' => 400]);
        }
        if (empty($params['content'])) {
            return new WP_Error('missing_content', 'Job description is required.', ['status' => 400]);
        }

        $post_id = wp_insert_post([
            'post_type'    => 'applied_jobs',
            'post_title'   => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_status'  => 'publish'
        ]);

        if (!$post_id) {
            return new WP_Error('insert_failed', 'Failed to add job.', ['status' => 500]);
        }

        update_post_meta($post_id, 'company_name', sanitize_text_field($params['company'] ?? ''));
        update_post_meta($post_id, 'job_url', esc_url_raw($params['url'] ?? ''));
        update_post_meta($post_id, 'date_applied', sanitize_text_field($params['date_applied'] ?? ''));
        update_post_meta($post_id, 'cover_letter', wp_kses_post($params['cover_letter'] ?? ''));

        return rest_ensure_response([
            'success' => true,
            'message' => 'Job added successfully!',
            'post_id' => $post_id
        ]);
    }

    public static function delete_job($request) {
        $params = $request->get_json_params();
        $post_id = intval($params['post_id'] ?? 0);

        if ($post_id === 0 || get_post_type($post_id) !== 'applied_jobs') {
            return new WP_Error('invalid_post', 'Invalid Post ID.', ['status' => 404]);
        }

        $deleted = wp_delete_post($post_id, true);

        if ($deleted) {
            return rest_ensure_response([
                'success' => true,
                'message' => 'Job deleted successfully!',
                'post_id' => $post_id
            ]);
        } else {
            return new WP_Error('delete_failed', 'Failed to delete job.', ['status' => 500]);
        }
    }
}

add_action('rest_api_init', ['JOB_API', 'register_routes']);
