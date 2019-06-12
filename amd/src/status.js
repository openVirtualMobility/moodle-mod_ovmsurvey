define(['jquery', 'core/str', 'core/ajax', 'core/notification'],
function($, str, ajax, notification) {
    var Status = function() {
        var st = localStorage.getItem('ovms-status');

        if (!st) {
            $('#status_picker').show();
        } else {
            str.get_string(st, 'ovmsurvey')
                .done(function(s) {
                    $('#ovm-status').text(s);
                });
        }

        $('.status-item').bind('click', function(){
            var status = $(this).data('value');
            localStorage.setItem('ovms-status', status);

            ajax.call([{
                methodname: 'mod_ovmsurvey_set_status',
                args: {
                    status: status
                },
                done: function(data) {
                    str.get_string(data, 'ovmsurvey')
                        .done(function(s) {
                            $('#ovm-status').text(s);
                            $('#status_picker').hide();
                            window.location.reload();
                        });
                }.bind(this),
                fail: notification.exception
            }]);
        });

        $('#ovm-status').bind('click', function() {
            $('#status_picker').show();
        });
    };

    return Status;
});