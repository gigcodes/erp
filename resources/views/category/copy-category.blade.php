@extends('layouts.app')

@section('content')
    <style>
        .btn-secondary {
            color: #757575;
            border: 1px solid #ddd;
            background-color: #fff;
        }

        table {
            word-break: break-all;
        }

    </style>

    <h2 class="page-heading flex" style="padding: 8px 5px 8px 10px;border-bottom: 1px solid #ddd;line-height: 32px;">
        Category
        <div class="margin-tb" style="flex-grow: 1;">
            
        </div>
    </h2>
    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <form method="POST" action="{{ route('category.storeCopy') }}" id="copy_category_data">
        @csrf
        <div class="row">
            <div class="col-md-4">
                Source Category Id
            </div>
            <div class="col-md-4">

                <select class="form-control submit_on_change globalSelect2"
                    name="sourceCategoryId" id="sourceCategoryId" required >
                    <option value=''>Select Source Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col-md-4">
                Source Category Id
            </div>
            <div class="col-md-4">
                <select class="form-control submit_on_change globalSelect2"
                    name="targetCategoryId" id="targetCategoryId" required>
                    <option value=''>Select Target Category</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}">
                        {{ $category->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <input type="submit" name="submit" class="btn btn-xs">
    </form>

    <script type="text/javascript">
        $( "#copy_category_data" ).submit(function( e ) {
            if(($("#sourceCategoryId").val() !== $("#targetCategoryId").val()) && ($("#sourceCategoryId").val() != '') && ($("#targetCategoryId").val() != '')){
                $(this).closest('form').submit()
                
            }  else {
                console.log("data matched")
                e.preventDefault();
                
            }    
                

        });
    </script>
@endsection
