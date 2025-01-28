jQuery(document).ready(function ($) {
    // Initialize TinyMCE
    function initializeTinyMCE() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: '#job-description, #job-cover-letter',
                menubar: false,
                toolbar: 'bold italic | undo redo | bullist numlist',
                height: 150
            });
        }
    }

    // Show/Hide Add Job Form and Initialize Editor
    $('#job-add-btn').on('click', function () {
        $('#job-form').toggle();
        setTimeout(initializeTinyMCE, 300); // Ensure TinyMCE loads properly
    });

    // Handle Job Submission
    $('#job-submit').on('click', function (e) {
        e.preventDefault();

        let title = $('#job-title').val().trim();
        let company = $('#job-company').val().trim();
        let url = $('#job-url').val().trim();
        let dateApplied = $('#job-date-applied').val().trim();
        let coverLetter = tinymce.get('job-cover-letter') ? tinymce.get('job-cover-letter').getContent().trim() : '';
        let description = tinymce.get('job-description') ? tinymce.get('job-description').getContent().trim() : '';

        if (!title || !description) {
            alert('Please enter a job title and description.');
            return;
        }

        $.ajax({
            url: jobSearch.rest_url,
            method: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', jobSearch.nonce);
            },
            data: JSON.stringify({
                title: title,
                company: company,
                url: url,
                date_applied: dateApplied,
                cover_letter: coverLetter,
                content: description
            }),
            contentType: 'application/json',
            success: function (response) {
                alert('Job added successfully!');
                $('#job-title').val('');
                $('#job-company').val('');
                $('#job-url').val('');
                $('#job-date-applied').val('');
                tinymce.get('job-cover-letter').setContent('');
                tinymce.get('job-description').setContent('');
                $('#job-form').hide();
                fetchJobs();
            },
            error: function () {
                alert('Error adding job.');
            }
        });
    });

    function fetchJobs(query = '') {
        $.ajax({
            url: jobSearch.ajax_url,
            type: 'POST',
            data: { action: 'job_search', query: query },
            beforeSend: function () {
                $('#job-results').html('<p>Loading...</p>');
            },
            success: function (response) {
                if (!response.success) {
                    $('#job-results').html('<p>No results found.</p>');
                    return;
                }

                let resultHtml = response.data.map(job => `
                    <div class="job-result" data-id="${job.id}">
                        <strong>${job.title}</strong>
                        ${job.company ? `<p><strong>Company:</strong> ${job.company}</p>` : ''}
                        ${job.date_applied ? `<p><strong>Date Applied:</strong> ${job.date_applied}</p>` : ''}
                        ${job.url ? `<p><a href="${job.url}" target="_blank">View Listing</a></p>` : ''}
                        ${job.cover_letter ? `<div class="job-cover-letter"><h4>Cover Letter:</h4>${job.cover_letter}</div>` : ''}
                        <div class="job-description"><h4>Job Description:</h4>${job.content}</div>
                        <button class="job-delete">Delete</button>
                    </div>
                `).join('');
                $('#job-results').html(resultHtml || '<p>No results found.</p>');
            }
        });
    }

    // Fetch jobs initially (load last 10 entries)
    fetchJobs();

    // AJAX Live Search
    $('#job-search').on('keyup', function () {
        let query = $(this).val().trim();
        fetchJobs(query);
    });

    // Handle Delete Job
    // Handle Delete Job
    $(document).on('click', '.job-delete', function () {
        let postId = $(this).closest('.job-result').data('id');
        if (!confirm('Are you sure you want to delete this job?')) return;

        $.ajax({
            url: jobSearch.delete_url,
            method: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', jobSearch.nonce);
            },
            data: JSON.stringify({ post_id: postId }),
            contentType: 'application/json',
            success: function (response) {
                if (response.success) {
                    alert('Job deleted successfully!');
                    fetchJobs(); // Refresh list after deletion
                } else {
                    alert('Error: ' + (response.data.message || 'Could not delete job.'));
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                alert('Failed to delete the job. Please try again.');
            }
        });
    });

});
