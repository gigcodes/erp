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

                <div class="form-group ml-3">
                  <select class="form-control " name="website_id" id="website_id" style="width:150px !important;">
                    <option value="">Website </option>
                    @foreach($websites as $website)
                      <option value="{{$website->id}}" {{ $website->id == old('website_id') ? 'selected' : '' }}>{{$website->website}}</option>
                    @endforeach
                </select>
                </div>

                <div class="form-group ml-3">
                  <select class="form-control " name="asset_plate_form_id" id="asset_plate_form_id" style="width:150px !important;">
                    <option value="">Plate Form</option>
                    @foreach($plateforms as $plateform)
                      <option value="{{$plateform->id}}" {{ $plateform->id == old('asset_plate_form_id') ? 'selected' : '' }}>{{$plateform->name}}</option>
                    @endforeach
                </select>
                </div>
      
                <div class="form-group ml-3">
                  <select class="form-control " name="email_address_id" id="email_address_id" style="width:150px !important; ">
                    <option value="">Email Address</option>
                    @foreach($emailAddress as $email)
                      <option value="{{$email->id}}" {{ $email->id == old('email_address_id') ? 'selected' : '' }}>{{$email->from_name}}</option>
                    @endforeach
                </select>
                </div>
      
                <div class="form-group ml-3">
                  <select class="form-control " name="whatsapp_config_id" id="whatsapp_config_id" style="width:150px !important; ">
                    <option value="">Phone Number</option>
                    @foreach($whatsappCon as $phone)
                      <option value="{{$phone->id}}" {{ $phone->id == old('whatsapp_config_id') ? 'selected' : '' }}>{{$phone->number}}</option>
                    @endforeach
                </select>
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
            <div class="pull-right">
              <button type="button" class="btn btn-xs ml-3 mr-3" title="Add Plate Form" data-toggle="modal" data-target="#plateFormModal"><i class="fa fa-plus"></i> Plate Form</button>
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
              <th width="6%">Name</th>
              <th width="6%">Capacity</th>
              <th width="5%">User Name</th>
              <th width="6%">Pwd</th>
              <th width="6%">Ast Type</th>
              <th width="5%">Cat</th>
              <th width="6%">Pro Name</th>
              <th width="6%">Pur Type</th>
              <th width="5%">Pymt Cycle</th>
              <th width="6%">Due Date</th>
              <th width="5%">Amount</th>
              <th width="4%">Currency</th>
              <th width="3%">Location</th>
              <th width="5%">Usage</th>
              <th width="3%">Site Name</th>
              <th width="3%">Plate Form</th>
              <th width="3%">Email</th>
              <th width="3%">Phone </th>
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
                <td class="expand-row-msg" data-name="website" data-id="{{$asset->id}}">
                  <span class="show-short-website-{{$asset->id}}">{{ str_limit($asset->website_name, 3, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-website-{{$asset->id}} hidden">{{$asset->website_name}}</span>
                </td>
                <td class="expand-row-msg" data-name="plateform_name" data-id="{{$asset->id}}">
                  <span class="show-short-plateform_name-{{$asset->id}}">{{ str_limit($asset->plateform_name, 3, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-plateform_name-{{$asset->id}} hidden">{{$asset->plateform_name}}</span>
                </td>
                <td class="expand-row-msg" data-name="from_address" data-id="{{$asset->id}}">
                  <span class="show-short-from_address-{{$asset->id}}">{{ str_limit($asset->from_address, 3, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-from_address-{{$asset->id}} hidden">{{$asset->from_address}}</span>
                </td>
                <td class="expand-row-msg" data-name="number" data-id="{{$asset->id}}">
                  <span class="show-short-number-{{$asset->id}}">{{ str_limit($asset->number, 3, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-number-{{$asset->id}} hidden">{{$asset->number}}</span>
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
                        <button type="button" class="btn btn-xs show-assets-history-log pull-left" data-toggle="modal" data-target="#showAssetsHistoryLogModel"  data-assets_id="{{ $asset->id }}"><i class="fa fa-eye"></i></button>
                        
                        <a style="padding:1px;" class="btn d-inline btn-image execute-bash-command-select-folder"  data-folder_name="{{$asset->folder_name}}" data-id="{{$asset->id}}" href="#"  title="Execute Bash Command">
                          <img src="/images/send.png" style="color:gray; cursor: nwse-resize; width: 0px;">
                        </a>
                        <button title="Response History" data-id="{{$asset->id}}" type="button"  class="btn execute_bash_command_response_history"style="padding:1px 0px;">
                          <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-history"></i></a>
                        </button>
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

    <div id="execute_bash_command_select_folderModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Send Bash Command</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            <div class="table-responsive mt-3">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Command Name</th>
                    <th>Send</th>
                  </tr>
                </thead>
                <tbody id="execute_select_folder_tbody">
    
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>

    <div id="magentoDevScriptUpdateHistoryModal" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
          <div class="modal-header">
              <h4 class="modal-title">User History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="table-responsive mt-3">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="20%">Created At</th>
                      <th width="20%">Ip</th>
                      <th width="30%">Response</th>
                      <th width="30%">Command Name</th>
                    </tr>
                  </thead>
                  <tbody id="magentoDevScriptUpdateHistory">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>



    <div id="showUserHistoryModel" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content ">
          <div class="modal-header">
              <h4 class="modal-title">Magento Setting Update Rersponse History</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="table-responsive mt-3">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th width="20%">ID</th>
                      <th width="30%">Change By</th>
                      <th width="30%">User Name</th>
                      <th width="20%">Created At</th>
                    </tr>
                  </thead>
                  <tbody class="showUserHistoryModelView">
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div id="plateFormModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content ">
          <div class="modal-header">
              <h4 class="modal-title">Add Plate Form Name</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
              <div class="table-responsive mt-3">
                <form id="add-remark">
                    <div class="form-group">
                      <input type="text" name="plateformName" id="plateformName" class="plateformname" value="">
                    </div>
                  <button type="button" class="btn btn-secondary btn-save-plateform mt-2" >Add</button>
                </form> 
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
   $('.select-multiple').select2({width: '100%'});
   $('.select2').select2({width: '100%'});
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
      
    $(document).on("click", ".execute-bash-command-select-folder", function(href) {
        
      var folder_name = $(this).data('folder_name');
      var id = $(this).data('id');
      var html = '';
      $('#execute_select_folder_tbody').html("");
      let result =  Array.isArray(folder_name);
      if(result){
        $.each(folder_name,function(key,value){
          if(value){
            html = '<tr><td>'+value+'  </td><td><a style="padding:1px;" class="btn d-inline btn-image execute-bash-command" data-folder_name="'+value+'" href="#" class="ip_name'+key+'" data-id="'+id+'" title="Execute Bash Command"><img src="/images/send.png" style="color: gray; cursor: nwse-resize; width: 0px;"></a></td></tr>';
            $('#execute_select_folder_tbody').append(html);
          }
        });
        $('#execute_bash_command_select_folderModal').modal('show');
      } else {
        alert("Please Check Record Site Folder Name.");
      }
      
		
	});
	$(document).on("click", ".execute-bash-command", function(href) {
		if(confirm ("Do you want to run this script???")){
			$.ajax({
				type: 'POST',
				url: 'assets-manager/magento-dev-script-update',
				beforeSend: function () {
					$("#loading-image").show();
				},
				data: {
					_token: "{{ csrf_token() }}",
					id: $(this).data('id'),
					folder_name : $(this).data('folder_name')
				},
				dataType: "json"
			}).done(function (response) {
				$("#loading-image").hide();
				if (response.code == 200) {
					toastr['success'](response.message, 'success');
				}
			}).fail(function (response) {
				$("#loading-image").hide();
				toastr['error'](response.message, 'error');
				console.log("Sorry, something went wrong");
			});
		}
	});

  $(document).on("click", ".execute_bash_command_response_history", function(href) {
		
      $.ajax({
        type: 'POST',
        url: 'assets-manager/magento-dev-update-script-history/'+ $(this).data('id') ,
        beforeSend: function () {
          $("#loading-image").show();
        },
        data: {
          _token: "{{ csrf_token() }}",
          id: $(this).data('id'),
        },
        dataType: "json"
      }).done(function (response) {
        $("#loading-image").hide();
        if (response.code == 200) {
          $('#magentoDevScriptUpdateHistory').html(response.data);
          $('#magentoDevScriptUpdateHistoryModal').modal('show');
          toastr['success'](response.message, 'success');
        }
      }).fail(function (response) {
        $("#loading-image").hide();
        console.log("Sorry, something went wrong");
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
      $(".select-multiple").select2("val", asset.user_name);
      $('#old_user_name').val(asset.user_name);
      $('#password').val(asset.password);
      $('#old_password').val(asset.password);
      $('#ip').val(asset.ip);
      $('#old_ip').val(asset.ip);
      $('#assigned_to').val(asset.assigned_to);
      $('#website_id').val(asset.website_id).trigger('change');
      $('#asset_plate_form_id').val(asset.asset_plate_form_id).trigger('change');
      $('#email_address_id').val(asset.email_address_id).trigger('change');
      $('#whatsapp_config_id').val(asset.whatsapp_config_id).trigger('change');
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
      
      
      $('#ip_name_ins').val(asset.ip_name);
      console.log(asset.ip_name);
      // $.each(JSON.parse(asset.ip_name),function(key,value){
      //     var addIpName = '<br/><input type="text" name="ip_name[]" id="ip_name'+key+'" class="form-control"  value="'+value+'" >';
      //     $(".addUpdIpName").append(addIpName);
      // });
      $(".addServerUpdate").html("");
      $.each(JSON.parse(asset.folder_name),function(key,value){
          var addserver = '<br/><input type="text" name="folder_name[]" id="folder_name'+key+'" class="form-control"  value="'+value+'" >';
          $(".addServerUpdate").append(addserver);
      });
      $('#server_password').val(asset.server_password);
      $('.show-user-history-btn').attr('data-id', +asset.id);
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
          $('.show-user-history-btn').click(function(){
            var asset_id = $(this).data('id');
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.userchange.history') }}",
              data: {
                asset_id:asset_id,
              },
          }).done(response => {
            $('#showUserHistoryModel').find('.showUserHistoryModelView').html('');
              if(response.success==true){
                $('#showUserHistoryModel').find('.showUserHistoryModelView').html(response.html);
                $('#showUserHistoryModel').modal('show');
              }
          }).fail(function(response) {
            alert('Could not fetch Data');
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
      $( ".updIpNamebtn" ).bind( "click", function() {
          var getCount = $(".getUpdCount").val();
          getCount = (parseInt(getCount) + parseInt(1));
          $(".getUpdCount").val(getCount);
          var addIpName = '<br/><input type="text" name="ip_name[]" id="ip_name'+getCount+'" class="form-control"  value="" >';
          $(".addUpdIpName").append(addIpName);
      });

      $( ".insIpNamebtn" ).bind( "click", function() {
          var getCount = $(".getInsCount").val();
          getCount = (parseInt(getCount) + parseInt(1));
          $(".getInsCount").val(getCount);
          var addIpName = '<br/><input type="text" name="ip_name[]" id="ip_name'+getCount+'" class="form-control"  value="" >';
          $(".addInsIpName").append(addIpName);
      });
      
      $( ".serverUpdbtn" ).bind( "click", function() {
          var getServerUpdCount = $(".getServerUpdCount").val();
          getServerUpdCount = (parseInt(getServerUpdCount) + parseInt(1));
          $(".getServerUpdCount").val(getServerUpdCount);
          var addServerUpdate = '<br/><input type="text" name="folder_name[]" id="folder_name'+getServerUpdCount+'" class="form-control"  value="" >';
          $(".addServerUpdate").append(addServerUpdate);
      });

      $( ".serverInsbtn" ).bind( "click", function() {
          var getInsServerCount = $(".getInsServerCount").val();
          getInsServerCount = (parseInt(getInsServerCount) + parseInt(1));
          $(".getInsServerCount").val(getInsServerCount);
          var addInsServerUpdate = '<br/><input type="text" name="folder_name[]" id="folder_name'+getInsServerCount+'" class="form-control"  value="" >';
          $(".addInsServerUpdate").append(addInsServerUpdate);
      });

      $(document).ready(function() {
          $('.btn-save-plateform').click(function(){
            var plateForm = $("#plateformName").val();
            $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('asset.manage.plateform.add') }}",
              data: {
                name:plateForm,
              },
          }).done(response => {
            toastr['success'](response.message, 'success');
            location.reload();
          }).fail(function(response) {
            toastr['error'](response.message, 'error');
          });
        });
      });

  </script>
@endsection
