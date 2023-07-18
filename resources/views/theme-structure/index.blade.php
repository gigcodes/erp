@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Theme Structure</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-8">
                    <form action="{{ route('project.index') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-3 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-3">
                                <?php 
									/*if(request('store_websites_search')){   $store_websites_search = request('store_websites_search'); }
									else{ $store_websites_search = []; }
								?>
								<select name="store_websites_search[]" id="store_websites_search" class="form-control select2" multiple>
									<option value="" @if($store_websites_search=='') selected @endif>-- Select a Store website --</option>
									@forelse($store_websites as $swId => $swName)
									<option value="{{ $swId }}" @if(in_array($swId, $store_websites_search)) selected @endif>{!! $swName !!}</option>
									@empty
									@endforelse
								</select>
                                */?>
                            </div>
                            
                            <div class="col-md-4 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                <a href="{{ route('project.index') }}" class="btn btn-image" id="">
                                    <img src="/images/resend2.png" style="cursor: nwse-resize;">
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-4">
                    <div class="pull-right">
                        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#project-create"> Create Project </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif
<div class="row m-3">
    <div class="col-lg-12">
        <div id="jstree_demo_div">
            <ul>
                <li>Root node 1
                  <ul>
                    <li id="child_node_1">Child node 1</li>
                    <li>Child node 2</li>
                  </ul>
                </li>
                <li>Root node 2</li>
              </ul>

        </div>
    </div>
</div>   
<?php /*@include('theme-structure.partials.project-create-modal')
@include('theme-structure.partials.project-edit-modal')
*/?>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function(){
        $(function () { $('#jstree_demo_div').jstree(); });
    });
   
</script>
@endsection