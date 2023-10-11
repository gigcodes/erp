@extends('layouts.app')

@section('title', 'Assets Manager List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Assets Manager List</h2>
            <div class="pull-left">
              <form class="form-inline" action="{{ route('assets-manager.index') }}" method="GET">
                <div class="form-group ml-3">
                    <br>
                    <?php //echo Form::text("search", request()->get("search", ""), ["class" => "form-control", "placeholder" => "Enter keyword for search"]); ?>

                    <div class="form-group m-1">
                        <input name="search" list="search-lists" type="text" class="form-control" placeholder="Enter keyword for search" value="{{request()->get('search')}}" />
                        <datalist id="search-lists">
                            @foreach ($assets as $key => $val )
                                <option value="{{$val->name}}">
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="form-group ml-3">
                  <br>
                  <select class="form-control" name="archived">
                    <option value="">Select</option>
                    <option value="1" {{ isset($archived) && $archived == 1 ? 'selected' : '' }}>Archived</option>
                  </select>
                </div>
                <div class="form-group ml-3">
                  <br>
                  <?php echo Form::select("asset_type", \App\AssetsManager::assertTypeList(), request("asset_type", ""), ["class" => "form-control"]); ?>
                </div>
                <div class="form-group ml-3">
                  <br>
                  <?php echo Form::select("purchase_type", \App\AssetsManager::purchaseTypeList(), request("purchase_type", ""), ["class" => "form-control"]); ?>
                </div>
                <div class="form-group ml-3">
                  <br>
                  <?php echo Form::select("payment_cycle", \App\AssetsManager::paymentCycleList(), request("payment_cycle", ""), ["class" => "form-control"]); ?>
                </div>
                <div class="col-md-1">
                  <br>
                  <select class="form-control" id="createdAt-select">
                    <option value="">Select SortBy CreatedAt</option>						
                    <option value="asc">Asc</option>
                    <option value="desc">Desc</option>
                  </select>
                </div>
                <div class="form-group ml-3">
                  Select Created Users
                  <br>
                  {{ Form::select("user_ids[]", \App\User::pluck('name','id')->toArray(), request('user_ids'), ["class" => "form-control select2", "multiple"]) }}
                </div>
                <div class="form-group ml-3">
                  Select Ips
                  <br>
                  {{ Form::select("ip_ids[]", \App\AssetsManager::pluck('ip','ip')->toArray(), request('ip_ids'), ["class" => "form-control select2", "multiple"]) }}
                </div>
                <br>
                  <button type="submit" class="btn ml-2"><i class="fa fa-filter"></i></button>
                <a href="{{route('assets-manager.index')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
              </form>
            </div>
            <div class="pull-right">
              <br>
                <button type="button" class="btn btn-secondary btn-sm text-white mr-4 assets-create-modal"><i class="fa fa-plus"></i></button>
            </div>
            <div class="pull-right">
              <br>
                <button type="button" class="btn btn-xs ml-3 mr-3 mt-1" data-toggle="modal" data-target="#cashflows">Cash Flows</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')


    <div class="mt-3 col-md-12">
      <div class="infinite-scroll" style="overflow-y: auto">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th width="3%">ID</th>
              <th width="6%">Name</th>
              <th width="6%">Capacity</th>
              <th width="5%">User Name</th>
              <th width="6%">Pwd</th>
              <th width="7%">Ast Type</th>
              <th width="5%">Cat</th>
              <th width="7%">Pro Name</th>
              <th width="7%">Pur Type</th>
              <th width="6%">Pymt Cycle</th>
              <th width="5%">Due Date</th>
              <th width="5%">Amount</th>
              <th width="5%">Currency</th>
              <th width="3%">Location</th>
              <th width="5%">Usage</th>
              <th width="10%">Link</th>
              <th width="10%">IP</th>
              <th width="10%">IP Name</th>
              <th width="10%">Account Name</th>
              <th width="10%">Account password</th>
              <th width="5%">Created By</th>
              <th width="5%">Action</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($assets as $k => $asset)
              <tr>
                <td>{{ $asset->id }}</td>
                <td class="expand-row-msg" data-name="name" data-id="{{$asset->id}}">
                  <span class="show-short-name-{{$asset->id}}">{{ Str::limit($asset->name, 8, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-name-{{$asset->id}} hidden">{{$asset->name}}</span>
                </td>
                <td class="expand-row-msg" data-name="capacity" data-id="{{$asset->id}}">
                  <span class="show-short-capacity-{{$asset->id}}">{{ Str::limit($asset->capacity, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-capacity-{{$asset->id}} hidden">{{$asset->capacity}}</span>
                </td>
                <td class="expand-row-msg" data-name="user_name" data-id="{{$asset->id}}">
                  <span class="show-short-user_name-{{$asset->id}}">{{ Str::limit($asset->user_name, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-user_name-{{$asset->id}} hidden">{{$asset->user_name}}</span>

                  @if($asset->user_name!='-' && !empty($asset->user_name))
                  <button type="button"  class="btn btn-copy-username btn-sm float-right" data-id="{{$asset->user_name}}">
                  <i class="fa fa-clone" aria-hidden="true"></i>
                  @endif
                </td>
                
                <td class="expand-row-msg" data-name="password" data-id="{{$asset->id}}">
                  <span class="show-short-password-{{$asset->id}}">{{ Str::limit($asset->password, 3, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-password-{{$asset->id}} hidden">{{$asset->password}}</span>

                  @if($asset->password!='-' && !empty($asset->password))
                  <button type="button"  class="btn btn-copy-password btn-sm float-right" data-id="{{$asset->password}}">
                  <i class="fa fa-clone" aria-hidden="true"></i>
                  @endif
                </button>
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
                  <span class="show-short-usage-{{$asset->id}}">{{ Str::limit($asset->usage, 9, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-usage-{{$asset->id}} hidden">{{$asset->usage}}</span>
                </td>
                  <td><a href="{{ $asset->link }}" target="_blank">{{ $asset->link }}</a></td>
                  <td>{{ $asset->ip }}</td>
                  <td class="expand-row-msg" data-name="ip_name" data-id="{{$asset->id}}">
                    <span class="show-short-ip_name-{{$asset->id}}">{{ Str::limit($asset->ip_name, 10, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-ip-name-{{$asset->id}} hidden">{{$asset->ip_name}}</span>
                  </td>
                  <td>{{ $asset->account_username }}</td>
                  <td>{{ $asset->account_password }}</td>
                  <td>{{ $asset->user?->name }}</td>
                <td>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$asset->id}}')"><i class="fa fa-arrow-down"></i></button>
                    <!--   <a href="{{ route('assets-manager.show', $asset->id) }}" class="btn  d-inline btn-image" href=""><img src="/images/view.png" /></a> -->
                </td>
            </tr>
            <tr class="action-btn-tr-{{$asset->id}} d-none">
                <td>Action</td>
                <td colspan="15">
                    @if(auth()->user()->hasRole('Admin') || auth()->user()->hasRole('asset-manager'))
                      <button type="button" class="btn btn-xs edit-assets pull-left" data-toggle="modal" data-target="#assetsEditModal" title="Edit Assets" data-assets="{{ json_encode($asset) }}"><i class="fa fa-edit"></i></button>
                    @endif
                    <button type="button" class="btn btn-xs make-remark pull-left" data-toggle="modal" data-target="#makeRemarkModal" title="Make Remark" data-id="{{ $asset->id }}"><i class="fa fa-clipboard"></i></button>
                    @if(auth()->user()->hasRole('Admin'))
                      {!! Form::open(['method' => 'DELETE','route' => ['assets-manager.destroy', $asset->id],'style'=>'display:inline']) !!}
                      <button type="submit" class="btn btn-xs pull-left" title="Delete Assets" onclick="return confirm('{{ __('Are you sure you want to Delete?') }}')"><i class="fa fa-trash"></i></button>
                      {!! Form::close() !!}
                    @endif
                    <button type="button" title="Payment history" class="btn payment-history-btn btn-xs pull-left" title="Payment History" data-id="{{$asset->id}}">
                        <i class="fa fa-history"></i>
                    </button>
                    <button type="button" class="btn btn-xs show-assets-history-log pull-left" data-toggle="modal" title="Show Assets History" data-target="#showAssetsHistoryLogModel"  data-assets_id="{{ $asset->id }}"><i class="fa fa-eye"></i></button>

                    <a style="padding:1px;" class="btn d-inline btn-image execute-bash-command-select-folder" data-folder_name="{{$asset->folder_name}}" data-id="{{$asset->id}}" href="#"  title="Execute Bash Command">
                        <img src="{{asset('/images/send.png')}}" style="color:gray; cursor: nwse-resize; width: 0px;">
                    </a>
                    <button title="Response History" data-id="{{$asset->id}}" type="button"  class="btn execute_bash_command_response_history"style="padding:1px 0px;">
                        <a href="javascript:void(0);"style="color:gray;"><i class="fa fa-history"></i></a>
                    </button>
                    @if(auth()->user()->hasRole('Admin'))
                      <button type="button" class="btn btn-xs send-assets-email" data-toggle="modal" data-assetid="{{$asset->id}}" data-target="#assetsSendEmailModal" title="Send Email" ><i class="fa fa-envelope"></i></button>
                      <button type="button" class="btn btn-xs assets-manager-record-permission" data-toggle="modal" data-assetid="{{$asset->id}}" data-target="#assetsPermissionModal" title="Record Permission" ><i class="fa fa-lock"></i></button>
                    @endif
                    <button type="button" title="Update status" data-id="{{$asset->id}}" onclick="updateUserActiveForAssetManager(this)" class="btn" style="padding: 0px 1px;">
                      <i class="fa fas fa-toggle-{{$asset->active == 1 ? 'on' : 'off  '}}"></i>
                    </button>

                    <button type="button" class="btn show-users-access-modal" data-toggle="modal" data-target="#userAccessModal" title="Create User Access" style="padding: 0px 1px;">
                        <i class="fa fas fa-universal-access"></i>
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

    <div id="assetsSendEmailModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('asset.manage.send.email') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Send Email</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <input type="hidden" name="assets_manager_id" id="assets-manager-id">
                    <label for="value">Send To(Users)</label>
                    <select class="form-control select2" name="user_name" >
                        <option value="">Select</option>
                        @foreach($users as $key => $user)
                          <option value="{{$user['id']}}">{{$user['name']}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('user_name'))
                    <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                    @endif
                </div>
                <div class="form-group">
                    <label for="value">From Mail</label>
                    <select class="form-control" name="from_email" id="from_email" required>
                        @foreach ($emailAddress as $emailAddress)
                        <option value="{{ $emailAddress->from_address }}">{{ $emailAddress->from_name }} - {{ $emailAddress->from_address }} </option>
                        @endforeach
                    </select>
                    @if ($errors->has('from_email'))
                    <div class="alert alert-danger">{{$errors->first('from_email')}}</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Send</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div id="assetsPermissionModal" class="modal fade" role="dialog">
      <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
          <form action="{{ route('asset.manage.records.permission') }}" method="POST">
            @csrf

            <div class="modal-header">
              <h4 class="modal-title">Records Permission</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                  <input type="hidden" name="assets_manager_id" id="permission-assets-manager-id">
                    <label for="value">Users</label>
                    <select class="form-control select2" multiple id="asset_user_name" name="user_name[]" >
                        <option value="">Select</option>
                        @foreach($users as $key => $user)
                          <option value="{{$user['id']}}">{{$user['name']}}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('user_name'))
                    <div class="alert alert-danger">{{$errors->first('user_name')}}</div>
                    @endif
                </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>

@endsection

@section('scripts')
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
    function Showactionbtn(id){
      $(".action-btn-tr-"+id).toggleClass('d-none')
      $("#asset_user_name").select2('destroy');
    }
   $('.select-multiple').select2({width: '100%'});
   $('.select2').select2();
    // $('ul.pagination').hide();
    // $(function() {
    //   $('.infinite-scroll').jscroll({
    //     autoTrigger: true,
    //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //     padding: 2500,
    //     nextSelector: '.pagination li.active + li a',
    //     contentSelector: 'div.infinite-scroll',
    //     callback: function() {
    //       $('ul.pagination').first().remove();
    //       $(".select-multiple").select2();
    //       initialize_select2();
    //     }
    //   });
    // });
      
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
      //console.log(asset);
      var d = new Date(asset.start_date);
      var day = (d.getDate() < 10 ? '0' : '') + d.getDate();
      var mon = ((d.getMonth()+1) < 10 ? '0' : '') + (d.getMonth()+1);
      var str =  d.getFullYear()+ '-' + mon + '-' + day;
      $('#assetsEditModal form').attr('action', url);
      $('#asset_name').val(asset.name);
      $('#user_name').val(asset.user_name);
      if(asset.user_name)
        $(".select-multiple").select2("val", asset.user_name);
      else
        $(".select-multiple").select2("val", "");

      $('#old_user_name').val(asset.user_name);
      $('#password').val(asset.password);
      $('#old_password').val(asset.password);
      $('#ip').val(asset.ip);
      $('#old_ip').val(asset.ip);
      $('#assigned_to').val(asset.assigned_to);
      $('#provider_name').val(asset.provider_name);
      $('#location').val(asset.location);
      $('#currency').val(asset.currency);
      if(asset.start_date !='0000-00-00' && asset.start_date != null){
        $('.start_date').val(''+str+'');
        $('#old_start_date').val(''+str+'');
      }else{
        $('.start_date').val("dd/mm/yyyy");
        $('#old_start_date').val("yyyy-mm-dd");
      }
      $('#asset_asset_type').val(asset.asset_type);
      $('#category_id2').val(asset.category_id);
      $('#asset_purchase_type').val(asset.purchase_type);
      $('#asset_payment_cycle').val(asset.payment_cycle);
      $('#asset_amount').val(asset.amount);
      $('#usage').val(asset.usage);
      $('#capacity').val(asset.capacity);
      $('#link').val(asset.link);
      $('#client_id').val(asset.client_id);
      $('#account_username').val(asset.account_username);
      $('#account_password').val(asset.account_password);
      
      $('#ip_name_ins').val(asset.ip_name);
      
      $(".addServerUpdate").html("");
      var addserver = '';
      let folderName = JSON.parse(asset.folder_name);
      $.each(folderName,function(key,value){
        addserver = addserver+'<input type="text" name="folder_name[]" id="folder_name'+key+'" class="form-control"  value="'+value+'" >';
          
      });
      $(".addServerUpdate").append(addserver);
      $('#server_password').val(asset.server_password);
      $('.show-user-history-btn').attr('data-id', asset.id);
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
            var asset_id = $(this).attr('data-id');//$(this).data('id');
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
          var addServerUpdate = '<input type="text" name="folder_name[]" id="folder_name'+getServerUpdCount+'" class="form-control"  value="" >';
          $(".addServerUpdate").append(addServerUpdate);
      });

      $( ".serverInsbtn" ).bind( "click", function() {
          var getInsServerCount = $(".getInsServerCount").val();
          getInsServerCount = (parseInt(getInsServerCount) + parseInt(1));
          $(".getInsServerCount").val(getInsServerCount);
          var addInsServerUpdate = '<br/><input type="text" name="folder_name[]" id="folder_name'+getInsServerCount+'" class="form-control"  value="" >';
          $(".addInsServerUpdate").append(addInsServerUpdate);
      });

      $(".send-assets-email, .assets-manager-record-permission").on("click", function(){
        var assetid = $(this).data('assetid');
        $("#assets-manager-id").val(assetid);
        $("#permission-assets-manager-id").val(assetid);
        $("#asset_user_name option:selected").removeAttr('selected');
        $("#asset_user_name").select2();

        $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: "{{ route('assetsmanager.linkuser.list') }}",
              data: {
                asset_id:assetid,
              },
          }).done(response => {
            var ids = response.data.userids;
            ids = ids.split(',');
            for (let index = 0; index < ids.length; index++) {
              const element = ids[index];
              $("#asset_user_name option[value="+element+"]").attr('selected', 'selected');
            }
            $("#asset_user_name").select2();

          }).fail(function(response) {

            alert('Could not fetch Log');
          });
      });

      $(".assets-create-modal").on("click", function(){
        $("#assetsCreateModal").modal("show");
        /*var createPermission = "<? /*userCan('assets-manager-create'); ?>";
        if(createPermission){
          $("#assetsCreateModal").modal("show");
        }else{
          $(".unauthorised").removeClass("hidden");
          $(".unauthorised").html("<p> Unauthorised permission</p>")
        }*/
      });

      $("#asset_user_name").select2({
        placeholder: 'Select Users'
      });

      var active_btn = null;
      function updateUserActiveForAssetManager(ele) {
        active_btn = jQuery(ele);
        let asset_id = active_btn.data("id");
        // let is_task_planned = btn.data("is_task_planned");
      
        if (
          confirm("Are you sure want to update?")
        ) {
          jQuery.ajax({
            headers: {
              "X-CSRF-TOKEN": jQuery('meta[name="csrf-token"]').attr("content"),
            },
            url: "{{route('assets-manager.update-status')}}",
            type: "POST",
            data: {
              asset_id,
            },
            dataType: "json",
            beforeSend: function () {
              jQuery("#loading-image").show();
            },
            success: function (res) {
              toastr["success"](res.message);
              jQuery("#loading-image").hide();
              if(active_btn.find(".fa").hasClass("fa-toggle-on")) {
                active_btn.find(".fa").removeClass("fa-toggle-on").addClass("fa-toggle-off");
              } else {
                active_btn.find(".fa").removeClass("fa-toggle-off").addClass("fa-toggle-on");
              }
            },
            error: function (res) {
              if (res.responseJSON != undefined) {
                toastr["error"](res.responseJSON.message);
              }
              jQuery("#loading-image").hide();
            },
          });
        }
      }

        $(".btn-copy-username").click(function() {
            var username = $(this).data('id');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(username).select();
            document.execCommand("copy");
            $temp.remove();
        });

        $(".btn-copy-password").click(function() {
            var password = $(this).data('id');
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(password).select();
            document.execCommand("copy");
            $temp.remove();
        });

    $(document).ready(function() {
        $('.show-users-access-modal').click(function(){
            $.ajax({
                type: 'POST',
                headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('assetsmanager.assetManamentUsers') }}",
                 data: {},
            }).done(response => {
                
                if(response.success==true){
                    $('#showAssetsManagementUsersModel').find('#showAssetsManagementUsersView').html(response.html);
                    $('#showAssetsManagementUsersModel').modal('show');
                }

            }).fail(function(response) {

                alert('Could not fetch Log');
            });
        });

    });

    $(document).ready(function($) {
        // Now you can use $ safely within this block
        $("#tag-input").autocomplete({
            source: function(request, response) {
                // Send an AJAX request to the server-side script
                $.ajax({
                    url: '{{ route('assetsmanager.users') }}',
                    dataType: 'json',
                    data: {
                        term: request.term // Pass user input as 'term' parameter
                    },
                    success: function(data) {
                        response(data); // The server returns filtered suggestions as JSON
                    }
                });
            },
            minLength: 1, // Minimum characters before showing suggestions
            select: function(event, ui) {
                // Handle the selection if needed
            }
        });
    })
  </script>
@endsection
