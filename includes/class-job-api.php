<?php
if (!defined('ABSPATH')) exit;

class JOB_API {
    public static function register_routes() {
        register_rest_route('job-tracker/v1', '/add-job', [
            'methods' => 'POST',
            'callback' => ['JOB_API', 'add_job'],
            'permission_callback' => function () {
                return current_user_can('edit_posts');
            }
        ]);

        register_rest_route('job-tracker/v1', '/delete-job', [
            'methods' => 'POST',
            'callback' => ['JOB_API', 'delete_job'],
            'permission_callback' => function () {
                return current_user_can('delete_posts');
            }
        ]);
    }

    public static function delete_job($request) {
        $params = $request->get_json_params();

        if (!isset($params['post_id'])) {
            return new WP_Error('missing_id', 'Post ID is required.', ['status' => 400]);
        }

        $post_id = intval($params['post_id']);

        // Verify if the post exists
        if (get_post_type($post_id) !== 'applied_jobs') {
            return new WP_Error('invalid_post', 'Invalid Post ID.', ['status' => 404]);
        }

        // Ensure the user has permissions
        if (!current_user_can('delete_post', $post_id)) {
            return new WP_Error('unauthorized', 'You are not allowed to delete this.', ['status' => 403]);
        }

        // Delete the post
        $deleted = wp_delete_post($post_id, true);

        if ($deleted) {
            return rest_ensure_response(['success' => true, 'message' => 'Job deleted successfully!']);
        } else {
            return new WP_Error('delete_failed', 'Failed to delete job.', ['status' => 500]);
        }
    }
}

// Register API Routes
add_action('rest_api_init', ['JOB_API', 'register_routes']);
