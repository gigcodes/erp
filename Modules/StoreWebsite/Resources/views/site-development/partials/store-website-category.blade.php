@extends('layouts.app')

@section('title', $title)

@section('content')
@php
$isAdmin = auth()->user()->isAdmin();
$isHod = auth()->user()->hasRole('HOD of CRM');
$hasSiteDevelopment = auth()->user()->hasRole('Site-development');
@endphp
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">{{$title}} 
            @if (isset($categories) && $categories->total())
                <span class="count-text">({{$categories->total()}})</span>
            @endif
        </h2>
    </div>
</div>
@if(!empty($categories))
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <form>
            {{-- @if ($pagination)
                <input type="hidden"  name="pagination" value="{{$pagination}}">
            @endif
            @if ($page)
                <input type="hidden"  name="page" value="{{$page}}">
            @endif --}}
        <div class="form-group col-lg-2">
            {{ Form::select('show', ['' => '- Show all -', '0' => 'Not Mapped only', '1' => 'Mapped only'] , $show, ['class' => 'form-control select2']) }}
        </div>
        <div class="form-group col-lg-2">
            {{ Form::select('category_id[]', $allCategories, $selectedCategoryIds, ['placeholder' => 'Select Categories', 'class' => 'select2 globalSelect2', 'id' => 'category_id', 'style' => 'float:left', 'multiple' => true]) }}
        </div>
        <div class="p-0 form-group col-lg-2">
            {{ Form::select('search_master_category_id', ['' => '- Select Master Category -'] + $masterCategories, '', ['class' => 'select2 globalSelect2', 'id' => 'search_master_category_id', 'style' => 'float:left']) }}
        </div>
        <div class="form-group col-lg-1" style="margin: 7px 0 0 0">
            <button type="submit" class="btn btn-sm btn-image btn-search-record" style="float:left">
                <img src="/images/send.png" title="Apply filter" style="cursor: pointer; width: 16px;">
            </button>
            <a href="{{route('site-development.store-website-category')}}" class="btn btn-image">
                <img src="/images/resend2.png" style="cursor: nwse-resize;">
            </a>
        </div>
        </form>
        <div>
            <label>Bulk update</label>
            <div>
                <div class="p-0 form-group col-lg-2">
                    {{ Form::select('master_category_id', ['' => '- Select Master Category -'] + $masterCategories, '', ['class' => 'select2 globalSelect2', 'id' => 'master_category_id', 'style' => 'float:left']) }}
                </div>
                <div class="form-group col-lg-1" style="margin: 7px 0 0 0"><img src="/images/send.png" title="Bulk update" style="cursor: pointer; width: 16px; float:left" id="bulk_action"></div>
            </div>

        </div>
    </div>
    <div class="col-lg-12 margin-tb">
        <div class="col-md-12">
            <div class="col-md-12 margin-tb">
                <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>
                            <div style="display: flex;align-items: center;">
                                <input type="checkbox" name="checkAll" id="checkAll"> 
                                <label for="checkAll" class="ml-2">Category ID</label></th>
                            </div>
                        <th>Category</th>
                        <th>Master Category</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                      </tr>
                    </thead>
                    @if ($isAdmin || $isHod || $hasSiteDevelopment)
                        @foreach($categories as $category)
                        <tbody>
                        <tr>
                            <td style="vertical-align:middle;">
                                <label style="display: flex;align-items: center;">
                                    <input type="checkbox" name="bulk_user_action[]" class="d-inline bulk_user_action m-0 p-0" value="{{ $category->id }}">
                                    <div class="ml-2">
                                        {{ $category->id }}
                                    </div>
                                </label>
                            </td>
                            <td style="vertical-align:middle;">{{ $category->title }}</td>
                            <td style="vertical-align:middle;">
                                {{ Form::select('site_development_master_category_id', ['' => '- Select-'] + $masterCategories, $category->site_development_master_category_id, ['class' => 'select2 globalSelect2 master_category_id','data-websiteid'=>$category->website_id, 'data-site' => $category->site_development_id ,'data-category' => $category->id, 'data-title' => $category->title]) }}    
                            </td>
                            <td>{{ $category->created_at }}</td>
                            <td>{{ $category->updated_at }}</td>
                        </tr>
                        </tbody>
                        @endforeach
                    @endif
                  </table>
            </div>
        </div>
    </div>
    <div class="col-md-12 margin-tb text-center">
        {!! $categories->appends(request()->capture()->except('page', 'pagination') + ['pagination' => true])->render() !!}
    </div>
</div>
@endif
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px !important; width: 100% !important;">
        <div class="modal-content" id="record-content">
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $("#checkAll").click(function () {
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
});

$(document).on("change", ".master_category_id" , function(){
        $.ajax({
            type: "POST",
            url: "{{ route('site-development.update-category') }}",   
            data: { 
                websiteId: $(this).data("websiteid"), 
                category: $(this).data("category"), 
                type: 'site_development_master_category_id', 
                text:$(this).val(),
                site:$(this).data("site"), 
                _token: "{{ csrf_token() }}"
            },  
            success: function(response) {
                if (response.code == 200) {
                    toastr['success'](response.messages);
                } else {
                    toastr['error'](response.messages);
                }
            }
        });
});

$(document).on("click", "#bulk_action" , function(){
    var mcid = $('#master_category_id').val();
    if (mcid != '') {
        let checkIds = [];
        $('.bulk_user_action').each(function(){
            if($(this).is(':checked')) {
                checkIds.push($(this).val());
            }
        });

        if (checkIds.length) {
            $.ajax({
                type: "POST",
                url: "{{ route('site-development.update-category-bulk') }}",   
                data: { 
                    categories: checkIds, 
                    master_category_id: mcid,
                    _token: "{{ csrf_token() }}"
                },  
                success: function(response) {
                    if (response.code == 200) {
                        toastr['success'](response.messages);
                    } else {
                        toastr['error'](response.messages);
                    }
                }
            });
        } else {
            toastr['error']('Please select atleast one category id.');
        }
    } else {
        toastr['error']('Select master category');
    }
});
</script>
@endsection