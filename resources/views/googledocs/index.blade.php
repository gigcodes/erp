@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Google Docs')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Docs</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createGoogleDocModal">
                    + Create Doc
                </button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered" id="googlefiletranslator-table">
            <thead>
            <tr>
                <th>No</th>
                <th>File Name</th>
                <th>Created At</th>
                <th>URL</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @include('googledocs.partials.list-files')
            </tbody>
        </table>
    </div>

    @include('googledocs.partials.create-doc')
@endsection
