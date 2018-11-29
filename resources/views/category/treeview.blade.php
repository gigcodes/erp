{{--<!DOCTYPE html>
<html>
<head>
    <title>Category</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

</head>
<body>
<div class="container">
</div>
<script src="{{asset('js/treeview.js')}}"></script>
</body>
</html>--}}

    @extends('layouts.app')

    @section('content')

    <link href="{{ asset('css/treeview.css') }}" rel="stylesheet">
    <div class="panel panel-primary">
        <div class="panel-heading">Manage Category</div>
        <div class="panel-body">

            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <h3>Category List</h3>
                    <ul id="tree1">
                        @foreach($categories as $category)
                            <li>
                                {{ $category->title }}
                                @if(count($category->childs))
                                    @include('category.manageChild',['childs' => $category->childs])
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                        <h3>Add New Category</h3>

                        {!! Form::open(['route'=>'add.category']) !!}

                        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                            {!! Form::label('Title:') !!}
                            {!! Form::text('title', old('title'), ['class'=>'form-control', 'placeholder'=>'Enter Title']) !!}
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                        </div>

                        <div class="form-group {{ $errors->has('magento_id') ? 'has-error' : '' }}">
                            {!! Form::label('Magento Id:') !!}
                            {!! Form::text('magento_id', old('magento_id'), ['class'=>'form-control', 'placeholder'=>'Enter Magento Id']) !!}
                            <span class="text-danger">{{ $errors->first('magento_id') }}</span>
                        </div>


                        <div class="form-group {{ $errors->has('show_all_id') ? 'has-error' : '' }}">
                            {!! Form::label('Show all Id:') !!}
                            {!! Form::text('show_all_id', old('show_all_id'), ['class'=>'form-control', 'placeholder'=>'Enter Show All Id']) !!}
                            <span class="text-danger">{{ $errors->first('show_all_id') }}</span>
                        </div>


                        <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
                            {!! Form::label('Category:') !!}
                            {{--                        {!! Form::select('parent_id',$allCategories, old('parent_id'), ['class'=>'form-control', 'placeholder'=>'Select Category']) !!}--}}
			                <?php echo $allCategoriesDropdown; ?>
                            <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                        </div>


                        <div class="form-group">
                            <button class="btn btn-secondary">+</button>
                        </div>

                        {!! Form::close() !!}

                        <h3>Modify Category</h3>
                        @if ($message = Session::get('error-remove'))
                            <div class="alert alert-danger alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        @if ($message = Session::get('success-remove'))
                            <div class="alert alert-success alert-block">
                                <button type="button" class="close" data-dismiss="alert">×</button>
                                <strong>{{ $message }}</strong>
                            </div>
                        @endif
                        {!! Form::open(['route'=>'category.remove']) !!}
                        <div class="form-group">
                            {!! Form::label('Category:') !!}
			                <?php echo $allCategoriesDropdownEdit; ?>
                            <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                        </div>
                        <div class="form-group">
                            <button id="btn-edit-cat" class="btn btn-image"><img src="/images/edit.png" /></button>
                            <button id="btn-delete-cat" class="btn btn-image"><img src="/images/delete.png" /></button>
                        </div>
                        {!! Form::close() !!}
                </div>
            </div>



            <div class="row">


            </div>

        </div>
    </div>
    <script src="{{asset('js/treeview.js')}}"></script>
    @endsection
