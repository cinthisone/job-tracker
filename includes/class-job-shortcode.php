<?php
if (!defined('ABSPATH')) exit;

class JOB_Shortcode {
    public static function register_shortcode() {
        add_shortcode('job_tracker_search', ['JOB_Shortcode', 'render_search']);
    }

    public static function render_search() {
        ob_start();
        ?>
        <div id="job-container">
            <button id="job-add-btn">Add Job</button>
            
            <!-- Hidden Form -->
            <div id="job-form" style="display: none;">
                <input type="text" id="job-title" placeholder="Enter Job Title" required>
                <input type="text" id="job-company" placeholder="Company (Optional)">
                <input type="url" id="job-url" placeholder="Job URL (Optional)">
                <input type="date" id="job-date-applied" placeholder="Date Applied">
                <textarea id="job-cover-letter" placeholder="Paste Cover Letter"></textarea>
                <textarea id="job-description" placeholder="Job Description"></textarea>
                <button id="job-submit">Submit</button>
            </div>

            <input type="text" id="job-search" placeholder="Search Jobs...">
            <div id="job-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Register the shortcode
add_action('init', ['JOB_Shortcode', 'register_shortcode']);
