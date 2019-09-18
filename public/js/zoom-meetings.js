$('#start_date_time').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });
$(document).on('click', '.set-meetings', function() {
          let userId = $(this).data('id');
          let userType = $(this).data('type');
          $('#user__id').val(userId);
          $('#user__type').val(userType);

      });
$('#zoomModal').on('hidden.bs.modal', function (e) {
  $(this)
    .find("input,textarea,select")
       .val('')
       .end()
    .find("input[type=checkbox], input[type=radio]")
       .prop("checked", "")
       .end();
});
$(document).on('click', '.save-meeting', function () {  
            let user_id = $('#user__id').val();
            let user_type = $('#user__type').val();
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
                     $('#zoomModal').modal('toggle');
                    toastr['success']('Meeting created successfully!');
                },
                data: {
                    user_id: user_id,
                    user_type: user_type,
                    meeting_topic: meeting_topic,
                    meeting_agenda: meeting_agenda,
                    start_date_time: start_date_time,
                    timezone: meeting_timezone,
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