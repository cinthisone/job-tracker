<?php
if (!defined('ABSPATH')) exit;

class QNA_API {
    public static function register_routes() {
        register_rest_route('qna-storage/v1', '/add-question', [
            'methods' => 'POST',
            'callback' => ['QNA_API', 'add_question'],
            'permission_callback' => function () {
                return current_user_can('edit_posts'); // Only allow logged-in users
            }
        ]);

        register_rest_route('qna-storage/v1', '/delete-question', [
            'methods' => 'POST',
            'callback' => ['QNA_API', 'delete_question'],
            'permission_callback' => function () {
                return current_user_can('delete_posts'); // Only allow users with delete permission
            }
        ]);
    }

    public static function add_question($request) {
        $params = $request->get_json_params();

        if (!isset($params['title']) || !isset($params['content'])) {
            return new WP_Error('missing_fields', 'Title and content are required.', ['status' => 400]);
        }

        // Insert the question into the database
        $post_id = wp_insert_post([
            'post_type'    => 'job_questions',
            'post_title'   => sanitize_text_field($params['title']),
            'post_content' => wp_kses_post($params['content']),
            'post_status'  => 'publish'
        ]);

        if ($post_id) {
            if (!empty($params['company'])) {
                update_post_meta($post_id, 'company_name', sanitize_text_field($params['company']));
            }
            if (!empty($params['url'])) {
                update_post_meta($post_id, 'question_url', esc_url_raw($params['url']));
            }

            return rest_ensure_response([
                'message' => 'Question added successfully!',
                'post_id' => $post_id
            ]);
        } else {
            return new WP_Error('insert_failed', 'Failed to add question.', ['status' => 500]);
        }
    }

    public static function delete_question($request) {
        $params = $request->get_json_params();

        if (!isset($params['post_id'])) {
            return new WP_Error('missing_id', 'Post ID is required.', ['status' => 400]);
        }

        $post_id = intval($params['post_id']);

        // Check if post exists
        if (get_post_type($post_id) !== 'job_questions') {
            return new WP_Error('invalid_post', 'Invalid Post ID.', ['status' => 404]);
        }

        // Ensure user has permissions
        if (!current_user_can('delete_post', $post_id)) {
            return new WP_Error('unauthorized', 'You are not allowed to delete this.', ['status' => 403]);
        }

        // Delete post
        $deleted = wp_delete_post($post_id, true);

        if ($deleted) {
            return rest_ensure_response([
                'message' => 'Question deleted successfully!',
                'post_id' => $post_id
            ]);
        } else {
            return new WP_Error('delete_failed', 'Failed to delete question.', ['status' => 500]);
        }
    }
}

// Register API Routes
add_action('rest_api_init', ['QNA_API', 'register_routes']);
