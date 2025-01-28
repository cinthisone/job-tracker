<?php
if (!defined('ABSPATH')) exit;

class QNA_Shortcode {
    public static function render_search() {
        ob_start();
        ?>
        <div id="qna-container">
            <button id="qna-add-btn">Add Question</button>
            
            <!-- Hidden Form -->
            <div id="qna-form" style="display: none;">
                <input type="text" id="qna-title" placeholder="Enter Question Title">
                <input type="text" id="qna-company" placeholder="Company (Optional)">
                <input type="url" id="qna-url" placeholder="Related URL (Optional)">
                <textarea id="qna-answer"></textarea>
                <button id="qna-submit">Submit</button>
            </div>

            <input type="text" id="qna-search" placeholder="Search Questions...">
            <div id="qna-results"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}

add_shortcode('job_question_search', ['QNA_Shortcode', 'render_search']);
