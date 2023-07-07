@extends('layouts.app')

@section("styles")
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection


@section('content')
	<div class="row">
		<div class="col-lg-12 margin-tb">
		    <h2 class="page-heading">Mulitple Run Commands({{$mulitipleCommands->total()}})</h2>
		</div>
	</div>
        <div class="mt-3 col-md-12">
            <table class="table table-bordered table-striped" id="log-table">
                <thead>
                    <tr>
                        <th width="3%">S.no</th>
                        <th width="10%">command</th>
                        <th width="10%">Created By</th>
                        <th width="10%">websites</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mulitipleCommands as $key => $mulitipleCommand)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$mulitipleCommand->command->command_name}}</td>
                            <td>{{$mulitipleCommand->user->name}}</td>
                            <td>
                                @php
                                    $websites = json_decode($mulitipleCommand->website_ids);
                                    $websites = array_map('intval', $websites);
                                    $websiteNames = [];
                                    foreach ($websites as $web) {
                                        if ($web !== 0) {
                                            $website = \App\StoreWebsite::find($web);
                                            if ($website) {
                                                $websiteNames[] = $website->website;
                                            }
                                        } else {
                                            $websiteNames[] = 'ERP';
                                        }
                                    }
                                    echo implode(', ', $websiteNames);
                                @endphp
                            </td>
                            <td>

                                <button type="button" id="test" class="btn btn-secondary" data-websiteIds="{{$mulitipleCommand->website_ids}}" data-command="{{$mulitipleCommand->command_id}}"> <i class="fa fa-paper-plane " aria-hidden="true"></i></button>
                
                                   
                                </a>
                            </td>
                        </tr>                        
                    @endforeach
                </tbody>
            </table>
        </div>
        
		{!! $mulitipleCommands->appends(Request::except('page'))->links() !!}
	</div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
    50% 50% no-repeat;display:none;">
</div>



<script type="text/javascript">


    $(document).on("click", "#test", function(e) {
        e.preventDefault();
        var websiteIds = $(this).data('websiteids');
        var commandId = $(this).data('command');
            if(commandId==''){
                toastr['error']('Please select Command', 'error');
                return;
            }
            if(typeof websiteIds == 'undefined' ||  websiteIds.length == 0){
                toastr['error']('Please select Website', 'error');
                return;
            }
        
            $.ajax({
                url: "/magento/command/run-on-multiple-website"
                , type: "post"
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , data: {
                    command_id: commandId,
                    websites_ids: websiteIds
                },
                beforeSend: function() {
                    $('#loading-image').show();
                },
            }).done(function(response) {
                if (response.code = '200') {
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
                $('#loading-image').hide();
                
            }).fail(function(errObj) {
                $('#loading-image').hide();
                if (errObj ?.responseJSON ?.message) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                    return;
                }
                toastr['error'](errObj.message, 'error');
            });
        
    });
   

</script>
@endsection