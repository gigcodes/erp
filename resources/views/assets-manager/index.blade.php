@extends('layouts.app')

@section('title', 'Assets Manager List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Assets Manager List</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{ route('assets-manager.index') }}" method="GET">
                <div class="form-group ml-3">
                  <?php echo Form::text("search", request()->get("search", ""), ["class" => "form-control", "placeholder" => "Enter keyword for search"]); ?>
                </div>
                <div class="form-group ml-3">
                  <select class="form-control" name="archived">
                    <option value="">Select</option>
                    <option value="1" {{ isset($archived) && $archived == 1 ? 'selected' : '' }}>Archived</option>
                  </select>
                </div>
                <div class="form-group ml-3">
                  <?php echo Form::select("asset_type", \App\AssetsManager::assertTypeList(), request("asset_type", ""), ["class" => "form-control"]); ?>
                </div>
                <div class="form-group ml-3">
                  <?php echo Form::select("purchase_type", \App\AssetsManager::purchaseTypeList(), request("purchase_type", ""), ["class" => "form-control"]); ?>
                </div>
                <div class="form-group ml-3">
                  <?php echo Form::select("payment_cycle", \App\AssetsManager::paymentCycleList(), request("payment_cycle", ""), ["class" => "form-control"]); ?>
                </div>
                <button type="submit" class="btn btn-xs"><i class="fa fa-filter"></i></button>
              </form>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-xs ml-3 mr-3" data-toggle="modal" data-target="#assetsCreateModal"><i class="fa fa-plus"></i></button>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-xs ml-3 mr-3" data-toggle="modal" data-target="#cashflows">Cash Flows</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')


    <div class="mt-3 col-md-12">
      <div class="infinite-scroll">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th width="4%">ID</th>
              <th width="7%">Name</th>
              <th width="6%">Capacity</th>
              <th width="5%">User Name</th>
              <th width="6%">Pwd</th>
              <th width="7%">Ast Type</th>
              <th width="5%">Cat</th>
              <th width="7%">Pro Name</th>
              <th width="7%">Pur Type</th>
              <th width="9%">Pymt Cycle</th>
              <th width="7%">Due Date</th>
              <th width="5%">Amount</th>
              <th width="5%">Currency</th>
              <th width="3%">Location</th>
              <th width="7%">Usage</th>
              <th width="15%">Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($assets as $asset)
              <tr>
                <td>{{ $asset->id }}</td>
                <td class="expand-row-msg" data-name="name" data-id="{{$asset->id}}">
                  <span class="show-short-name-{{$asset->id}}">{{ str_limit($asset->name, 12, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-name-{{$asset->id}} hidden">{{$asset->name}}</span>
                </td>
                <td class="expand-row-msg" data-name="capacity" data-id="{{$asset->id}}">
                  <span class="show-short-capacity-{{$asset->id}}">{{ str_limit($asset->capacity, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-capacity-{{$asset->id}} hidden">{{$asset->capacity}}</span>
                </td>
                <td class="expand-row-msg" data-name="user_name" data-id="{{$asset->id}}">
                  <span class="show-short-user_name-{{$asset->id}}">{{ str_limit($asset->user_name, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-user_name-{{$asset->id}} hidden">{{$asset->user_name}}</span>
                </td>
                
                <td class="expand-row-msg" data-name="password" data-id="{{$asset->id}}">
                  <span class="show-short-password-{{$asset->id}}">{{ str_limit($asset->password, 3, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-password-{{$asset->id}} hidden">{{$asset->password}}</span>
                </td>
                <td>{{ $asset->asset_type }}</td>
                <td>@if(isset($asset->category)) {{ $asset->category->cat_name }} @endif</td>

                <td>{{ $asset->provider_name }}</td>
                <td>{{ $asset->purchase_type }}</td>
                <td>{{ $asset->payment_cycle }}</td>
                <td>{{ ($asset->due_date)?$asset->due_date:'--' }}</td>
                <td>{{ $asset->amount }}</td>
                <td>{{ $asset->currency }}</td>
                <td>{{ $asset->location }}</td>
                <td class="expand-row-msg" data-name="usage" data-id="{{$asset->id}}">
                  <span class="show-short-usage-{{$asset->id}}">{{ str_limit($asset->usage, 9, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-usage-{{$asset->id}} hidden">{{$asset->usage}}</span>
                </td>
                <td>
                    <!--   <a href="{{ route('assets-manager.show', $asset->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a> -->
                    <button type="button" class="btn btn-xs edit-assets pull-left" data-toggle="modal" data-target="#assetsEditModal" data-assets="{{ json_encode($asset) }}"><i class="fa fa-edit"></i></button>
                        <button type="button" class="btn btn-xs make-remark pull-left" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $asset->id }}"><i class="fa fa-clipboard"></i></button>
                        {!! Form::open(['method' => 'DELETE','route' => ['assets-manager.destroy', $asset->id],'style'=>'display:inline']) !!}
                        <button type="submit" class="btn btn-xs pull-left"><i class="fa fa-trash"></i></button>
                        {!! Form::close() !!}
                        <button type="button" title="Payment history" class="btn payment-history-btn btn-xs pull-left" data-id="{{$asset->id}}">
                          <i class="fa fa-history"></i>
                        </button>
                        <button type="button" class="btn btn-xs show-assets-history-log pull-left" data-toggle="modal" data-target="#showAssetsHistoryLogModel" data-assets_id="{{ $asset->id }}"><i class="fa fa-eye"></i></button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
        {{ $assets->appends(request()->except('page'))->links() }}
      </div>
    </div>
    @include('partials.modals.remarks')
    @include('assets-manager.partials.payment-history')
    @include('assets-manager.partials.assets-modals')
@endsection

@section('scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
   $('.select-multiple').select2({width: '100%'});
    $('ul.pagination').hide();
    $(function() {
      $('.infinite-scroll').jscroll({
        autoTrigger: true,
        loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        padding: 2500,
        nextSelector: '.pagination li.active + li a',
        contentSelector: 'div.infinite-scroll',
        callback: function() {
          $('ul.pagination').first().remove();
          $(".select-multiple").select2();
          initialize_select2();
        }
      });
    });
      
    $(document).on('click', '.edit-assets', function() {
      var asset = $(this).data('assets');
      var url = "{{ url('assets-manager') }}/" + asset.id;
      console.log(asset);
      var d = new Date(asset.start_date);
      var day = (d.getDate() < 10 ? '0' : '') + d.getDate();
      var str = d.getFullYear() + '-' + (d.getMonth()+1) + '-' + day;
      $('#assetsEditModal form').attr('action', url);
      $('#asset_name').val(asset.name);
      $('#user_name').val(asset.user_name);
      $('#old_user_name').val(asset.user_name);
      $('#password').val(asset.password);
      $('#old_password').val(asset.password);
      $('#ip').val(asset.ip);
      $('#old_ip').val(asset.ip);
      $('#assigned_to').val(asset.assigned_to);
      $('#provider_name').val(asset.provider_name);
      $('#location').val(asset.location);
      $('#currency').val(asset.currency);
      $('#start_date').val(''+str+'');
      $('#asset_asset_type').val(asset.asset_type);
      $('#category_id2').val(asset.category_id);
      $('#asset_purchase_type').val(asset.purchase_type);
      $('#asset_payment_cycle').val(asset.payment_cycle);
      $('#asset_amount').val(asset.amount);
      $('#usage').val(asset.usage);
      $('#capacity').val(asset.capacity);
    });
    $(document).on('click', '.expand-row-msg', function () {
      var name = $(this).data('name');
      var id = $(this).data('id');
      var full = '.expand-row-msg .show-short-'+name+'-'+id;
      var mini ='.expand-row-msg .show-full-'+name+'-'+id;
      $(full).toggleClass('hidden');
      $(mini).toggleClass('hidden');
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
            module_type: "assets-manager"
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
            module_type: 'assets-manager'
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

    $(document).ready(function() {
      // Change category on create page logic
      $('#category_id').on('change', function(){
          var category_id = $('#category_id').val();
          if( category_id != '' && category_id == '-1')
          {
              $('.othercat').show();
          }
          else{
            $('.othercat').hide();
          }
      });
      // Change categoryon create page logic
      $('#category_id2').on('change', function(){
          var category_id = $('#category_id2').val();
          if( category_id != '' && category_id == '-1')
          {
              $('.othercatedit').show();
          }
          else{
            $('.othercatedit').hide();
          }
      });

    });
    
    $(document).ready(function() {
          $('.payment-history-btn').click(function(){
            var asset_id = $(this).data('id');
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.paymentHistory') }}",
              data: {
                asset_id:asset_id,
              },
          }).done(response => {
            $('#payment-history-modal').find('.payment-history-list-view').html('');
              if(response.success==true){
                $('#payment-history-modal').find('.payment-history-list-view').html(response.html);
                $('#payment-history-modal').modal('show');
              }

          }).fail(function(response) {

            alert('Could not fetch payments');
          });
        });

      });

      $(document).ready(function() {
          $('.show-assets-history-log').click(function(){
            var asset_id = $(this).data('assets_id');
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.assetManamentLog') }}",
              data: {
                asset_id:asset_id,
              },
          }).done(response => {
            $('#payment-history-modal').find('.payment-history-list-view').html('');
              if(response.success==true){
                $('#showAssetsHistoryLogModel').find('#showAssetsHistoryLogView').html(response.html);
                $('#showAssetsHistoryLogModel').modal('show');
              }

          }).fail(function(response) {

            alert('Could not fetch Log');
          });
        });

      });

  </script>
@endsection
