@extends('layouts.app')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <style type="text/css">
        .select-multiple-cat-list .select2-container {
            position: relative;
            z-index: 2;
            float: left;
            width: 100%;
            margin-bottom: 0;
            display: table;
            table-layout: fixed;
        }
        /*.update-product + .select2-container--default{
            width: 60% !important;
        }*/
        .no-pd {
            padding:0px;
        }

        .select-multiple-cat-list + .select2-container {
            width:100% !important;
        }

        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }

        .row .btn-group .btn {
            margin: 0px;
        }
        .btn-group-actions{
            text-align: right;
        }

        .multiselect-supplier + .select2-container{
            width: 198px !important;
        }
        .size-input{
            width: 155px !important;
        }
        .quick-sell-multiple{
            width: 98px !important;
        }
        .image-filter-btn{
            padding: 10px;
            margin-top: -12px;
        }
        .update-product + .select2-container{
            width: 150px !important;
        }
        .product-list-card > .btn, .btn-sm {
            padding: 5px;
        }

        .select2-container {
            width:100% !important;
            min-width:200px !important;   
        }
        .no-pd {
            padding:3px;
        }
        .mr-3 {
            margin:3px;
        }
        td{
            padding: 4px !important;
        }
    </style>
@endsection

@section('content')
 <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row m-0">
        <div class="col-lg-12 margin-tb p-0">
            <div class="">
                <!--roletype-->
                <h2 class="page-heading">Scrapper Url list </h2>
                <!--pending products count-->
                <!--attach Product-->
                <!--Product Search Input -->
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

 

    <div class="col-md-12 margin-tb">
        <div class="table-responsive">
            <table class="table table-bordered" {{--style="table-layout:fixed;"--}}>
                <thead>
                    <th style="width:5%">Id</th>
                    <th style="width:70%">URL</th>
                </thead>
                <tbody class="infinite-scroll-data">
                @foreach ($urls  as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{$item->url}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        {{ $urls->appends(request()->except('page'))->links() }}
    </div>

 @endsection
