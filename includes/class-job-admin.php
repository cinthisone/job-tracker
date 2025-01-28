<?php
if (!defined('ABSPATH')) exit;

class JOB_Admin {
    public static function init() {
        add_action('add_meta_boxes', ['JOB_Admin', 'add_job_meta_box']);
        add_action('save_post', ['JOB_Admin', 'save_job_meta']);
        add_action('admin_enqueue_scripts', ['JOB_Admin', 'enqueue_admin_scripts']);
    }

    public static function add_job_meta_box() {
        add_meta_box(
            'job_details_meta_box',
            'Job Details',
            ['JOB_Admin', 'render_meta_box'],
            'applied_jobs',
            'normal',
            'high'
        );
    }

    public static function enqueue_admin_scripts() {
        wp_enqueue_editor(); // Loads WordPress TinyMCE editor
    }

    public static function render_meta_box($post) {
        // Retrieve saved meta values
        $company = get_post_meta($post->ID, 'company_name', true);
        $job_url = get_post_meta($post->ID, 'job_url', true);
        $date_applied = get_post_meta($post->ID, 'date_applied', true);
        $cover_letter = get_post_meta($post->ID, 'cover_letter', true);

        // Security nonce
        wp_nonce_field('save_job_meta', 'job_meta_nonce');

        ?>
        <table class="form-table">
            <tr>
                <th><label for="company_name">Company Name</label></th>
                <td><input type="text" id="company_name" name="company_name" value="<?php echo esc_attr($company); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="job_url">Job URL</label></th>
                <td><input type="url" id="job_url" name="job_url" value="<?php echo esc_url($job_url); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="date_applied">Date Applied</label></th>
                <td><input type="date" id="date_applied" name="date_applied" value="<?php echo esc_attr($date_applied); ?>"></td>
            </tr>
            <tr>
                <th><label for="cover_letter">Cover Letter</label></th>
                <td>
                    <?php
                    wp_editor(
                        $cover_letter,
                        'cover_letter',
                        [
                            'textarea_name' => 'cover_letter',
                            'media_buttons' => false, // Hide media upload button
                            'teeny' => false, // Show full TinyMCE editor
                            'quicktags' => true // Enable quicktags for text mode
                        ]
                    );
                    ?>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function save_job_meta($post_id) {
        // Check nonce
        if (!isset($_POST['job_meta_nonce']) || !wp_verify_nonce($_POST['job_meta_nonce'], 'save_job_meta')) {
            return;
        }

        // Don't save on auto-drafts
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) return;

        // Save fields
        if (isset($_POST['company_name'])) {
            update_post_meta($post_id, 'company_name', sanitize_text_field($_POST['company_name']));
        }
        if (isset($_POST['job_url'])) {
            update_post_meta($post_id, 'job_url', esc_url_raw($_POST['job_url']));
        }
        if (isset($_POST['date_applied'])) {
            update_post_meta($post_id, 'date_applied', sanitize_text_field($_POST['date_applied']));
        }
        if (isset($_POST['cover_letter'])) {
            update_post_meta($post_id, 'cover_letter', wp_kses_post($_POST['cover_letter']));
        }
    }
}

// Initialize meta boxes
add_action('load-post.php', ['JOB_Admin', 'init']);
add_action('load-post-new.php', ['JOB_Admin', 'init']);
