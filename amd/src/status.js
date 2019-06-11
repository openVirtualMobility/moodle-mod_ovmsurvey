define(['jquery', 'core/str'], function($, str) {
    var Status = function() {
        st = localStorage.getItem('ovms-status');

        if (!st) {
            $('#status_picker').show();
        } else {
            str.get_string(st, 'ovmsurvey')
                .done(function(s) {
                    $('#ovm-status').text(s);

                    notification.addNotification({
                        message: s,
                        type: 'success'
                    });
                });
        }

        $('.status-item').bind('click', function() {
            localStorage.setItem('ovms-status', $(this).data('value'));

            str.get_string($(this).data('value'), 'ovmsurvey')
                .done(function(s) {
                    $('#ovm-status').text(s);
                    $('#status_picker').hide();

                    notification.addNotification({
                        message: s,
                        type: 'success'
                    });
                });
        });

        $('#ovm-status').bind('click', function() {
            $('#status_picker').show();
        });
    };

    return Status;
});