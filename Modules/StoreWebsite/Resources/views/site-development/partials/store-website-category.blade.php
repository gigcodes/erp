@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
</div>
@if(!empty($categories))
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <div class="col-md-12">
            <div class="col-md-12 margin-tb">
                <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Category ID</th>
                        <th>Category</th>
                        <th>Website</th>
                        <th>Master Category</th>
                      </tr>
                    </thead>
                    @foreach($categories as $category)
                    <tbody>
                      <tr>
                        <td style="vertical-align:middle;">{{ $category->id }}</td>
                        <td style="vertical-align:middle;">{{ $category->title }}</td>
                        <td style="vertical-align:middle;">{{ $category->website }}</td>
                        <td style="vertical-align:middle;">
                            {{ Form::select('site_development_master_category_id', ['' => '- Select-'] + $masterCategories, null, ['class' => 'select2 globalSelect2 master_category_id','data-websiteid'=>$category->website_id, 'data-site' => $category->site_development_id ,'data-category' => $category->id, 'data-title' => $category->title]) }}    
                        </td>
                      </tr>
                    </tbody>
                    @endforeach
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
$(document).on("change", ".master_category_id" , function(){
        $.ajax({
            type: "POST",
            url: "{{ route('site-development.save') }}",   
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
</script>
@endsection