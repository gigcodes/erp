@extends('layouts.app')

@section('title', 'Magento Settings')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<style type="text/css">
.checkbox input {
    height: unset;
}
</style>

<div class="row m-0">
    <div class="col-12 p-0">


        <h2 class="page-heading">Magento Settings Logs ({{$counter}})</h2>
    </div>
    @if($errors->any())
        <div class="row m-2">
          {!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
        </div>
    @endif
    @if (session('success'))
        <div class="row m-2">
          <div class="alert alert-success">{{session('success')}}</div>
        </div>
    @endif
     <div class="row m-0">
         <div class="col-lg-12 margin-tb pl-3">
             <div class="pull-left cls_filter_box">
                 <form class="form-inline" action="{{ route('magento.setting.sync-logs') }}" method="GET" style="width: 100%;"> 
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                        <select class="form-control select2" name="website">
                            <option value="">All</option> 
                            @foreach($storeWebsites as $w)
                                <?php $selected = '';
                                $webArr = request('website') ? request('website') : 0;
                                ?>
                                @if($w->id == $webArr)
                                    <?php $selected = 'selected';?>
                                @endif
                               <option value="{{ $w->id }}" {{ $selected }}>{{ $w->website }}</option>
                           @endforeach
                        </select>
                     </div> 
                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                        <input placeholder="Date" type="text" class="form-control estimate-date_picker" name="date" id="date_picker">
                    </div>
                     <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                        <?php $base_url = URL::to('/');?> 
                        <button type="submit" style="" class="btn btn-image pl-0"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
                        <a href="{{ route('magento.setting.sync-logs') }}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                     </div> 
                 </form>

             </div>
         </div> 
        <div class="col-12 mb-3 mt-3 p-0">

            <div class="pull-left"></div>
            <div class="pull-right"></div>
            <div class="col-12 pl-3 pr-3">
                <div class="table-responsive">
                    <table class="table table-bordered"style="table-layout: fixed;">
                        <thead>
                            <tr>
                                <th width="5%">ID </th>
                                <th width="10%">Website </th>
                                <th width="8%">Synced on</th>
                                <th width="10%">URL</th>
                                <th width="30%">Request Data</th>
                                <th width="10%">Response</th>
                                <th width="10%">Status Code</th>
                                <th width="7%">Status</th>
                            </tr>
                        </thead>
    
                        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
                            @foreach ($pushLogs as $log) 
                                <tr>
                                    <td >{{ $log->id }}</td>
                                    <td>{{ $log->website }}</td>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->command_server }}</td>
                                    <td>{{ $log->command }}</td>
                                    <td>{{ $log->command_output }}</td>
                                    <td>{{ $log->job_id }}</td>
                                    <td>{{ $log->status }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $pushLogs->links() }}
            </div>
        </div>
    

             </div>
         </div> 
     </div>
</div>
<img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
50% 50% no-repeat;display:none;"></div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script> 
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js">
</script>
<script>
    $('#date_picker').datetimepicker({
        format: "YYYY-MM-DD"
    });
</script>
@endsection
