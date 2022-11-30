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
                        <form class="form-inline message-search-handler" method="get">
                                    <div class="form-group m-1">
                                        <input name="name" list="name-lists" type="text" class="form-control" placeholder="Search file" value="{{request()->get('name')}}" />
                                        <datalist id="name-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->name}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group sm-1">
                                        <input name="docid" list="docid-lists" type="text" class="form-control" placeholder="Search Url" value="{{request()->get('docid')}}" />
                                        <datalist id="docid-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->docId}}">
                                            @endforeach
                                        </datalist>
                                    </div>

                                    <div class="form-group">
                                        <label for="button">&nbsp;</label>
                                        <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/google-docs" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                    </div>
                        </form>
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
