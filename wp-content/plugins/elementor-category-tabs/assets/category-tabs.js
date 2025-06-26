
jQuery(document).ready(function ($) {
   $(document).on('click', '.category-tab', function (e) {
    e.preventDefault();
    
    let category = $(this).data('category');
    $('.category-tab').removeClass('active');
    $(this).addClass('active');

    // Avoid unnecessary AJAX calls
    if ($('#post-list').data('current-category') === category) {
        return;
    }

    // Show the loader before clearing posts
    $('.loader').show();
    $('#post-list').html('');
    $('.load-more-container').html('');
    $('#post-list').data('current-category', category);

    $.ajax({
        url: ajax_params.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'filter_posts_by_category',
            category: category,
            paged: 1
        },
        success: function (response) {
            $('.loader').hide(); // Hide loader on success
            $('#post-list').html(response.html);

            if (response.has_more) {
                $('.load-more-container').html('<button class="load-more" data-category="' + category + '" data-page="' + response.next_page + '">Load More</button>');
            } else {
                $('.load-more-container').html('');
            }
        },
        error: function () {
            $('.loader').hide(); // Hide loader on error
            $('#post-list').html('<p class="error-message">Failed to load posts. Please try again.</p>');
        }
    });
});


   $(document).on('click', '.load-more', function () {
    let button = $(this);
    let category = button.data('category');
    let paged = button.data('page');
    button.text("Loading...").prop("disabled", true);
    $.ajax({
        url: ajax_params.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'filter_posts_by_category',
            category: category,
            paged: paged
        },
        success: function (response) {
           
            $('#post-list').find('li:last').after(response.html);

            if (response.has_more) {
                    button.data('page', response.next_page).text("Load More").prop("disabled", false);
                } else {
                    button.remove(); 
                }
        }
    });
});

});
