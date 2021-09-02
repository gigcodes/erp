@extends('layouts.app')

@section('title','GT Metrix Accounts')

@section('content')

<style>
    .model-width{
        max-width: 1250px !important;
    }
</style>

<div class="row m-4" style="margin-top: 5rem;">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
        <h2 class="font-weight-bold">GTMetrix Accounts</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-success" href="{{ route('account.create') }}"> Create New Account</a>
        </div>
    </div>
</div>

@include('partials.flash_messages')

<div class="row m-4">
    <div class="col-lg-12 margin-tb">
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="panel mt-3 panel-default table-responsive">
                <table class="table table-bordered table-striped site-gtMetrix-account">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Api Keys</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($Accounts as $key)
                        
                            <tr>
                                <td>{{ $key->email }}</td>
                                <td>{{ $key->password }}</td>
                                <td>{{ $key->account_id }}</td>
                                <td>

                                    <!-- <a href="{{ route('account.show',$key->id) }}" title="show">
                                        <i class="fa fa-eye text-success  fa-lg"></i>
                                    </a> -->

                                    <a href="{{ route('account.edit',$key->id) }}">
                                        <i class="fa fa-pencil  fa-lg"></i>
                                    </a>

                                    {!! Form::open(['method' => 'DELETE','route' => ['account.destroy', $key->id],'style'=>'display:inline']) !!} 
                                    <button type="submit"  style="border: none; background-color:transparent;">
                                        <i class="fa fa-trash fa-lg text-danger"></i>
                                    </button>
                                    {!! Form::close() !!}
                                </td>
        
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{ $Accounts->links() }}
    </div>
</div>

<div id="account_return_summary_edit" class="modal fade" role="dialog" style="padding: 0 !important;">
    <div class="modal-dialog modal-lg" id="account_return_summary_edit_list">
    </div>
    </div>

@section('scripts')
<script>
$(document).on("click","#btn-account-update-request",function(e) {
            e.preventDefault();
            var form = $(this).closest("form");
            $.ajax({
                url: form.attr("action"),
                method: form.attr("method"),
                data: form.serialize(),
                beforeSend : function() {
                    $("#loading-image").show();
                },
                success: function(response) {
                    $("#loading-image").hide();
                    $("#account_return_summary_edit").modal("hide"); 
                    toastr['success']('Success', 'success');
                },
                error: function(error) {
                    toastr['error']('Something went wrong', 'success');
                    $("#loading-image").hide();
                }
            });
        });


        </script>
@endsection


@include('gtmetrix.setSchedule')
@endsection
