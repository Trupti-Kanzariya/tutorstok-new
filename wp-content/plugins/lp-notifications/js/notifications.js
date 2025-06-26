jQuery(document).ready(function($) {
    $('.notification-item').on('click', function() {
        var $item = $(this);
        var notificationId = $item.data('id');
        
        $.post(lp_notification_vars.ajax_url, {
            action: 'mark_notification_read',
            id: notificationId
        }, function() {
            $item.removeClass('unread').addClass('read');
        });
    });
});
