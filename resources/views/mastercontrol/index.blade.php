@extends('layouts.app')

@section('title', 'Master Control')

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

  <div class="row mb-5">
      <div class="col-lg-12 margin-tb">
          <h2 class="page-heading">Master Control - {{ date('Y-m-d') }}</h2>

          <div class="pull-left">
            <form class="form-inline" action="{{ route('mastercontrol.index') }}" method="GET">
              <div class="form-group ml-3">
                <input type="text" value="" name="range_start" hidden/>
                <input type="text" value="" name="range_end" hidden/>
                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                  <i class="fa fa-calendar"></i>&nbsp;
                  <span></span> <i class="fa fa-caret-down"></i>
                </div>
              </div>

              <button type="submit" class="btn btn-secondary ml-3">Submit</button>
            </form>
          </div>

          <div class="pull-right mt-4">
            {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#mergeModal">Merge Customers</button> --}}
            {{-- <a class="btn btn-secondary" href="{{ route('customer.create') }}">+</a> --}}
          </div>
      </div>
  </div>

    @include('partials.flash_messages')

    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="documents-table">
            <thead>
            <tr>
                <th>Columns</th>
                <th>S. No</th>
                <th>Page Name</th>
                <th>Particulars</th>
                <th>Time Spent</th>
                <th>Remarks</th>
                <th>Action / Time</th>
            </tr>
            </thead>
            <tbody>
              <tr>
                <td>Broadcasts</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Tasks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Statutory Tasks</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Orders</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Purchases</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Scraping</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
              <tr>
                <td>Reviews</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
               <tr>
                <td>Emails</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
               <tr>
                <td>Accounting</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
           </tbody>
        </table>
    </div>

  


@endsection

@section('scripts')
  {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script type="text/javascript">
  $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]' });
  });

  var group_id = '';
  @foreach ($message_groups as $date => $data)
    @foreach ($data as $group_id => $group)
      group_id = "{{ $date }}{{ $group_id }}";
      console.log(group_id);
      window['horizontalBroadcastBarChart' + group_id] = $('#horizontalBroadcastBarChart' + group_id);
      var horizontalBarChart = new Chart(window['horizontalBroadcastBarChart' + group_id], {
          type: 'horizontalBar',
          data: {
            labels: ['Total'],
            datasets: [
              {
                label: "Sent",
                backgroundColor: '#5EBA31',
                data: [{{ $group['sent'] }}],
              },
              {
                label: "Received",
                backgroundColor: '#5738CA',
                data: [{{ $group['received'] }}],
              },
              {
                label: "Stopped",
                backgroundColor: '#DC143C',
                data: [{{ $group['stopped'] }}],
              }
            ],
          },
          options: {
            beginAtZero: true,
            elements: {
              rectangle: {
                borderWidth: 2,
              }
            },
            responsive: true,
            legend: {
              position: 'right',
            },
            scales: {
              xAxes: [{
                ticks: {
                  beginAtZero: true,
                  max: {{ $group['total'] }}
                }
              }]
            }
          }
      });
    @endforeach
  @endforeach

    $(document).on('click', '.quick-shortcut-button', function(e) {
      e.preventDefault();

      var customer_id = $(this).parent().find('input[name="customer_id"]').val();
      var instruction = $(this).parent().find('input[name="instruction"]').val();
      var category_id = $(this).parent().find('input[name="category_id"]').val();
      var assigned_to = $(this).parent().find('input[name="assigned_to"]').val();
      var thiss = $(this);
      var text = $(this).text();

      $.ajax({
        type: "POST",
        url: "{{ route('instruction.store') }}",
        data: {
          _token: "{{ csrf_token() }}",
          customer_id: customer_id,
          instruction: instruction,
          category_id: category_id,
          assigned_to: assigned_to,
        },
        beforeSend: function() {
          $(thiss).text('Loading...');
        }
      }).done(function(response) {
        $(thiss).text(text);
      }).fail(function(response) {
        $(thiss).text(text);

        alert('Could not execute shortcut!');

        console.log(response);
      });
    });

    let r_s = '{{ $start }}';
    let r_e = '{{ $end }}';

    let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(1, 'days');
    let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

    jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
    jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        maxYear: 1,
        endDate: end,
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    cb(start, end);

    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

        jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
        jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

    });
    var tabs = [];
    var red_tabs = localStorage['red_tabs'];

    if (red_tabs) {
      tabs = JSON.parse(red_tabs);
      tabs.forEach(function(index) {
        $('a[href="' + index + '"]').addClass('text-danger');
      });
    }

    $('#exTab2 li').on('dblclick', function() {
      var href = $(this).find('a').attr('href');

      if (red_tabs) {
        tabs = JSON.parse(red_tabs);
        console.log(red_tabs);

        if (tabs.indexOf(href) < 0) {
          tabs.push(href);
        } else {
          tabs.splice(tabs.indexOf(href), 1);
        }

        localStorage['red_tabs'] = JSON.stringify(tabs);
        red_tabs = localStorage['red_tabs'];

      } else {
        tabs.push(href);
        localStorage['red_tabs'] = JSON.stringify(tabs);
        red_tabs = localStorage['red_tabs'];
      }

      $(this).find('a').toggleClass('text-danger');
    });

    $(document).on('change', '.plan-task', function() {
      var time_slot = $(this).data('timeslot');
      var id = $(this).val();
      var thiss = $(this);
      var target_id = $(this).data('targetid');

      if (id != '') {
        $.ajax({
          type: "POST",
          url: "{{ url('task') }}/" + id + '/plan',
          data: {
            _token: "{{ csrf_token() }}",
            time_slot: time_slot
          }
        }).done(function(response) {
          // var count = $('#' + target_id).find('td').attr('rowspan');
          // console.log(count, '#' + target_id);
          // $('#' + target_id).find('td').attr('rowspan', parseInt(count, 10)+ 1);
          var row = `<tr>
            <td class="p-2">` + time_slot + `</td>
            <td class="p-2">
              <div class="d-flex justify-content-between">
                <span>
                ` + response.task.task_subject + `
                </span>
                <span>
                  <button type="button" class="btn btn-image task-complete p-0 m-0" data-id="` + response.task.id + `" data-type="task"><img src="/images/incomplete.png" /></button>
                </span>
              </div>
            </td>
            <td class="p-2 task-time"></td>
            <td class="p-2"><button type="button" class="btn btn-image make-remark p-0 m-0" data-toggle="modal" data-target="#makeRemarkModal" data-id="` + response.task.id + `"><img src="/images/remark.png" /></button></td>
          </tr>`;

          $(thiss).closest('tr').before(row);
        }).fail(function(response) {
          console.log(response);
          alert('Could not plan a task');
        });
      }
    });

    $(document).on('click', '.make-remark', function(e) {
      e.preventDefault();

      var id = $(this).data('id');
      $('#add-remark input[name="id"]').val(id);

      $.ajax({
          type: 'GET',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.gettaskremark') }}',
          data: {
            id:id,
            module_type: "task"
          },
      }).done(response => {
          var html='';

          $.each(response, function( index, value ) {
            html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
            html+"<hr>";
          });
          $("#makeRemarkModal").find('#remark-list').html(html);
      });
    });

    $('#addRemarkButton').on('click', function() {
      var id = $('#add-remark input[name="id"]').val();
      var remark = $('#add-remark').find('textarea[name="remark"]').val();

      $.ajax({
          type: 'POST',
          headers: {
              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
          },
          url: '{{ route('task.addRemark') }}',
          data: {
            id:id,
            remark:remark,
            module_type: 'task'
          },
      }).done(response => {
          $('#add-remark').find('textarea[name="remark"]').val('');

          var html =' <p> '+ remark +' <br> <small>By You updated on '+ moment().format('DD-M H:mm') +' </small></p>';

          $("#makeRemarkModal").find('#remark-list').append(html);
      }).fail(function(response) {
        console.log(response);

        alert('Could not fetch remarks');
      });
    });

    $(document).on('click', '.task-complete', function(e) {
      e.preventDefault();
      e.stopPropagation();

      var thiss = $(this);
      var task_id = $(thiss).data('id');
      var image = $(this).html();
      var current_user = {{ Auth::id() }};
      var type = $(this).data('type');

      if (type == 'activity') {
        var url = "/dailyActivity/complete/" + task_id;
      } else {
        var url = "/task/complete/" + task_id;
      }

      if (!$(thiss).is(':disabled')) {
        $.ajax({
          type: "GET",
          url: url,
          data: {
            type: 'complete'
          },
          beforeSend: function () {
            $(thiss).text('Completing...');
          }
        }).done(function(response) {
          // $(thiss).parent()
          $(thiss).closest('tr').find('.task-time').text(moment().format('DD-MM HH:mm'));
          $(thiss).remove();
        }).fail(function(response) {
          $(thiss).html(image);

          alert('Could not mark as completed!');

          console.log(response);
        });
      }
    });

    $(document).on('click', '.show-tasks', function() {
      var count = $(this).data('count');
      // var rowspan = $(this)
      $('.hiddentask' + count).toggleClass('hidden');
    });

    function storeDailyActivity(element, activity, time_slot, target_id) {
      $.ajax({
        type: 'POST',
        url: "{{ route('dailyActivity.quick.store') }}",
        data: {
          _token: "{{ csrf_token() }}",
          activity: activity,
          time_slot: time_slot,
          user_id: "{{ isset($selected_user) && $selected_user != '' ? $selected_user : Auth::id() }}",
          for_date: "{{ date('Y-m-d') }}"
        }
      }).done(function(response) {
        var count = $('#' + target_id).find('td').attr('rowspan');
        var row = `<tr>
          <td class="p-2"></td>
          <td class="p-2">
            <div class="d-flex justify-content-between">
              <span>
              ` + activity + `
              </span>
              <span>
                <button type="button" class="btn btn-image task-complete p-0 m-0" data-id="` + response.activity.id + `" data-type="activity"><img src="/images/incomplete.png" /></button>
              </span>
            </div>
          </td>
          <td class="p-2 task-time"></td>
          <td class="p-2"><button type="button" class="btn btn-image make-remark p-0 m-0" data-toggle="modal" data-target="#makeRemarkModal" data-id="` + response.activity.id + `"><img src="/images/remark.png" /></button></td>
        </tr>`;

        $('#' + target_id).find('td').attr('rowspan', parseInt(count, 10)+ 1);

        $(element).closest('tr').before(row);
        $(element).val('');
      }).fail(function(response) {
        console.log(response);

        alert('Could not create activity');
      });
    }

    $('.quick-plan-input').on('keypress', function(e) {
      console.log(e);
      var key = e.which;
      var thiss = $(this);
      var time_slot = $(this).data('timeslot');
      var target_id = $(this).data('targetid');
      var activity = $(this).val();

      if (key == 13) {
        e.preventDefault();

        storeDailyActivity(thiss, activity, time_slot, target_id);
      }
    });

    $('.quick-plan-button').on('click', function(e) {
      var thiss = $(this);
      var time_slot = $(this).data('timeslot');
      var target_id = $(this).data('targetid');
      var activity = $(this).siblings('.quick-plan-input').val();

      storeDailyActivity(thiss, activity, time_slot, target_id);

      $(this).siblings('.quick-plan-input').val('');
    });
  </script>
@endsection
