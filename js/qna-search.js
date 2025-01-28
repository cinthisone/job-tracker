jQuery(document).ready(function($) {
    // Initialize TinyMCE
    tinymce.init({
        selector: '#qna-answer',
        menubar: false,
        toolbar: 'bold italic | undo redo | bullist numlist',
        height: 150
    });

    // Show/Hide Add Form
    $('#qna-add-btn').on('click', function() {
        $('#qna-form').toggle();
    });

    // Handle Form Submission
    $('#qna-submit').on('click', function(e) {
        e.preventDefault();
        
        let title = $('#qna-title').val().trim();
        let company = $('#qna-company').val().trim();
        let url = $('#qna-url').val().trim();
        let answer = tinymce.get('qna-answer').getContent().trim();

        if (!title || !answer) {
            alert('Please enter a title and an answer.');
            return;
        }

        $.ajax({
            url: qnaSearch.rest_url,
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', qnaSearch.nonce);
            },
            data: JSON.stringify({ title: title, company: company, url: url, content: answer }),
            contentType: 'application/json',
            success: function(response) {
                alert('Question added successfully!');
                $('#qna-title').val('');
                $('#qna-company').val('');
                $('#qna-url').val('');
                tinymce.get('qna-answer').setContent('');
                $('#qna-form').hide();
                fetchLatestQuestions();
            },
            error: function() {
                alert('Error adding question.');
            }
        });
    });

    // AJAX Live Search
    $('#qna-search').on('keyup', function() {
        let query = $(this).val().trim();
        if (query.length < 2) {
            $('#qna-results').html('');
            return;
        }

        $.ajax({
            url: qnaSearch.ajax_url,
            type: 'POST',
            data: { action: 'qna_search', query: query },
            beforeSend: function() {
                $('#qna-results').html('<p>Searching...</p>');
            },
            success: function(response) {
                let resultHtml = response.map(q => `
                    <div class="qna-result" data-id="${q.id}">
                        <strong>${q.title}</strong>
                        <p>${q.content}</p>
                        ${q.company ? `<p><strong>Company:</strong> ${q.company}</p>` : ''}
                        ${q.url ? `<p><a href="${q.url}" target="_blank">View Source</a></p>` : ''}
                        <button class="qna-delete">Delete</button>
                    </div>
                `).join('');
                $('#qna-results').html(resultHtml || '<p>No results found.</p>');
            }
        });
    });

    // Handle Delete Question
    $(document).on('click', '.qna-delete', function() {
        let postId = $(this).closest('.qna-result').data('id');
        if (!confirm('Are you sure you want to delete this question?')) return;

        $.ajax({
            url: qnaSearch.rest_url.replace('add-question', 'delete-question'),
            method: 'POST',
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-WP-Nonce', qnaSearch.nonce);
            },
            data: JSON.stringify({ post_id: postId }),
            contentType: 'application/json',
            success: function() {
                alert('Question deleted successfully!');
                $(`.qna-result[data-id="${postId}"]`).remove();
            },
            error: function() {
                alert('Error deleting question.');
            }
        });
    });
});
