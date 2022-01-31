@extends('layouts.app')


@section('favicon' , 'productstats.png')


@section('title', 'Push to magento Conditions')


@section('content')
    <?php $base_url = URL::to('/');?>
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Push to magento Conditions ({{$conditions->count()}})</h2>
            <div class="pull-left cls_filter_box pb-4">
              <form class="form-inline" action="{{ route('products.push.conditions') }}" method="GET">
                <div class="pd-2">
                    <div class="form-group">
                        <label for="with_archived">Select Condition</label>
                        <select class="form-control select select2 required" name="condition"  >
                            <option value="">Please select Condition</option>
                            @foreach($drConditions as $condition)
                              <?php $sel='';
                              if (isset($_GET['condition']) && $_GET['condition']==$condition->condition)
                                      $sel=" selected='selected' "; ?>
                                <option value="{{ $condition->condition }}" {{ $sel }}>{{ $condition->condition }}</option>
                            @endforeach
                        </select>
                    </div>
               </div>
                  <div class="form-group ml-3 cls_filter_inputbox">
                      <label for="with_archived">Search Description</label>
                      <input name="magento_description" type="text" class="form-control" placeholder="Search" id="description-search" value="{{ @$_GET['magento_description'] }}">
                  </div>
                  <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
              </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-bordered">
                <tr>
                    <th width="10%">#</th>
                    <th width="20%">Condition</th>
                    <th width="60%">Description</th>
                    <th width="10%">Action</th>
                </tr>
                @foreach($conditions as $i=>$condition)
                    <tr>
                        <td width="10%">{{ $i+1 }}</td>
                        <td width="20%">
                            {{ $condition['condition'] }}
                        </td>
                        <td width="60%">
                           {{ $condition['description'] }}
                        </td>
                        <td width="10%"> 
                            {{Form::select('status', [1=>'Enable', 0=>'Disable'], $condition['status'], array('class'=>'form-control status', 'data-id'=>$condition['id']))}}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $( ".status" ).change(function() {
            var status = $(this).val();
            var id = $(this).data('id');
            $.ajax({
              url: '{{ url("products/conditions/status/update") }}'+'?id='+id+'&status='+status,
              method: 'GET'
            }).done(function(response) {
              alert('Status Updated');
            });
        });
    </script>
@endsection
