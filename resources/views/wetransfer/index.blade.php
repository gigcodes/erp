@extends('layouts.app')

@section('title', 'WeTransfer Queues')

@section("styles")
    
@endsection

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">WeTransfer Queues</h2>
             <div class="pull-right">
                <button type="button" class="btn btn-image" onclick="refreshPage()"><img src="/images/resend2.png" /></button>
            </div>

        </div>
    </div>

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="10%">Type</th>
                <th width="10%">URL</th>
                <th width="10%">Is Processed</th>
               
            </tr>
             <tr>
                    <td></td>
                    <td></td>
                    <td></td>     
            </tr>
        </thead>
    </table>
</div>

@endsection    