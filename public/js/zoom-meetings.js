$('#start_date_time').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
$(document).on('click', '.save-meeting', function () { 
            let customer_id = $('#customer_id').val();
            let meeting_topic = $('#meeting_topic').val();
            let meeting_agenda = $('#meeting_agenda').val();
            let start_date_time = $('#start_date_time').val();
            let meeting_timezone = $('#timezone').val();
            let meeting_duration = $('#meeting_duration').val();
            var meeting_url = $('#meetingUrl').val();
            var csrf_token = $('#csrfToken').val();
            $.ajax({
                url: meeting_url,
                type: 'POST',
                success: function (response) {
                    toastr['success']('Meeting created successfully!');
                },
                data: {
                    customer_id: customer_id,
                    meeting_topic: meeting_topic,
                    meeting_agenda: meeting_agenda,
                    start_date_time: start_date_time,
                    meeting_timezone: meeting_timezone,
                    meeting_duration: meeting_duration,
                    _token: csrf_token
                },
                beforeSend: function () {
                    $(this).text('Loading...');
                }
            }).fail(function (response) {
                 toastr['error'](response.responseJSON.message);

            });;
        });