@extends('layouts.app')



@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@endsection

@section('large_content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">{{ $title }} ({{ $missingBrands->count() }})</h2>
            <div class="pull-left">
<!--                 <form class="form-inline" action="{{ route('missing-brands.index') }}" method="GET">
                    <div class="form-group">
                        <input name="term" type="text" class="form-control"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="Search">
                    </div>
                    

                    <button type="submit" class="btn btn-image"><img src="/images/filter.png"/></button>
                </form> -->
            </div>

        </div>
    </div>

    @include('partials.flash_messages')

   <div class="table-responsive mt-3">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($missingBrands as $missingBrand)
                <tr>
                    <td>{{ $missingBrand->id }}</td>
                    <td>{{ $missingBrand->name }}</td>
                    <td>{{ $missingBrand->supplier }}</td>
                    <td>{{ $missingBrand->created_at }}</td>
                    <td><a href="javascript:;" data-name="{{$missingBrand->name}}" data-id="{{$missingBrand->id}}" class="create-brand">Brand</a> | 
                        <a href="javascript:;" data-name="{{$missingBrand->name}}" data-id="{{$missingBrand->id}}" class="create-reference">Reference</a></td>
                </tr>
            @endforeach
            </tbody>
            {!! $missingBrands->render() !!}
        </table>
    </div>

<div id="create-brand-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Brand</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo route('missing-brands.store'); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <strong>Name</strong>
                            <input type="hidden" class="form-control brand-name-id" name="id">
                            <input type="text" class="form-control brand-name-field" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-secondary save-brand-btn">Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="reference-brand-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign Reference</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo route('missing-brands.reference'); ?>" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" class="form-control brand-name-id" name="id">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <strong>Brand</strong>
                            <input type="text" class="form-control brand-name-field" name="name">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <strong>Brand</strong>
                            <?php echo Form::select("brand",\App\Brand::pluck('name','id')->toArray(),null,['class' => 'form-control']); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-secondary save-brand-btn">Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    $(document).on("click",".create-brand",function() {
        $(".brand-name-field").val($(this).data('name'));
        $(".brand-name-id").val($(this).data('id'));
        $("#create-brand-modal").modal("show");
    });

    $(document).on("click",".create-reference",function() {
        $(".brand-name-field").val($(this).data('name'));
        $(".brand-name-id").val($(this).data('id'));
        $("#reference-brand-modal").modal("show");
    });

</script>




@endsection


