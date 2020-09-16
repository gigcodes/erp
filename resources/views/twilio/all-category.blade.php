@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Manage Twilio Category</h2>
        </div>
    </div>
    @include('partials.flash_messages')
    <div class="row mb-3">
        <div class="col-md-10 col-sm-12">
            <div class="row">
                <button class="btn btn-secondary" data-target="#addAccount" data-toggle="modal">+</button>
            </div>
            <div class="row mt-5">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">#</th>
                            <th scope="col" class="text-center">Category Name</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @if(isset($all_category))
                        	<?php $i = 1; ?>
                            @foreach($all_category as $categories)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{ $categories->category_name }}</td>
                                    <td>
                                    	<a type="button" href="{{ route('twilio-delete-category', $categories->id ) }}" data-id="{{$categories->id}}" class="btn btn-delete-template" onclick="return confirm('Are you sure you want to delete this category ?');">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
@endsection
