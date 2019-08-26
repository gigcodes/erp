@extends('layouts.app')

@section('title', 'Broadcast Report')

@section('styles')
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
@endsection

@section('content')

  <div class="row mb-5">
      <div class="col-lg-12 margin-tb">
          <h2 class="page-heading">Supplier Scrapping  Info </h2>

          <div class="pull-left">
            <form class="form-inline" action="{{ route('mastercontrol.index') }}" method="GET">
              
               <div class="form-group ml-3">
               <input type='text' class="form-control" name="search" placeholder="Search" required />
              </div>

              <div class="form-group ml-3">
               <select name="status" class="form-control">
                  <option>Select Type</option>
                  <option>Sucess</option>
                  <option>Failed</option>
              </select> 
              </div>

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
           
          </div>
      </div>
  </div>

  @include('partials.flash_messages')

    

    <div class="row no-gutters mt-3">
      <div class="col-xs-12 col-md-12" id="plannerColumn">
        <div class="table-responsive">
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th>Sr. No</th>
                <th>Supplied ID</th>
                <th>Supplier Name </th>
                <th>Status</th>
                <th>Scrapper Type</th>
                <th>Last Scrapped</th>
                <th>Progess</th>
                <th>Inventory</th>
                <th>New</th>
                <th>Removed</th>
                <th>Total</th>
                <th>Info Scrapping</th>
                <th>Developer</th>
                <th>Remarks</th>
              </tr>
            </thead>

            <tbody>
              @foreach($progress as $key => $progressItem) 
                                                     
              <tr>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2">{{$key}}</td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"> 
                      <div class="progress" style="margin-bottom: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{$progressItem[1]}}%" aria-valuenow="{{$progressItem[1]}}" aria-valuemin="0" aria-valuemax="100">{{ $progressItem[1]}}%</div>
                       </div>
                      {{ $progressItem[0] . ' of ' . $progressItem[2] }}</td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>
                    <td class="p-2"></td>

              </tr>
             @endforeach  
                  
             <tr>
                                <td>{{$key}}</td>
                                <td>
                                    <div class="progress" style="margin-bottom: 5px;">
                                        <div class="progress-bar" role="progressbar" style="width: {{$progressItem[1]}}%" aria-valuenow="{{$progressItem[1]}}" aria-valuemin="0" aria-valuemax="100">{{ $progressItem[1]}}%</div>
                                    </div>
                                    {{ $progressItem[0] . ' of ' . $progressItem[2] }}
                                </td>
                                <td>
                                    {{ $progressItem[4]->started_at ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ $progressItem[4]->ended_at ?? 'N/A' }}
                                </td>
                                <td class="expand-row">
                                    <span class="td-mini-container">
                                        {!! strlen($progressItem[3]) > 20 ? substr($progressItem[3], 0, 20).'...' : $progressItem[3] !!}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {!! $progressItem[3] !!}
                                    </span>
                                </td>
                            </tr>
                
            </thead>

            <tbody>
                          </tbody>
          </table>
        </div>
      </div>
    </div> 
  
  @endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
  <script type="text/javascript">


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

  

   

    $(document).on('click', '.show-tasks', function() {
      var count = $(this).data('count');
      // var rowspan = $(this)
      $('.hiddentask' + count).toggleClass('hidden');
    });

   

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
