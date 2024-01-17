@extends('layouts.app')
@section('favicon' , 'user-management.png')

@section('title', 'Bank statement')

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
            <h2 class="page-heading">Bank statements >> List (<span id="user_count">{{ $data->total() }}</span>)</h2>
        </div>
        <div class="col-lg-12 margin-tb ml-2 mb-2">
            <a href="{{ route('bank-statement.import') }}" class="btn btn-default">
                {{__('Import Excel File')}}
            </a>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                <th rowspan="2">Sr. No</th>
                <th rowspan="2" class="text-center">File</th>
                {{-- <th rowspan="2" class="text-left">Path</th> --}}
                <th rowspan="2" class="text-center">Status</th>
                <th rowspan="2" class="text-center">Created By</th>
                <th rowspan="2" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $detail)
                    <tr>
                        <td>{{$key + 1}}</td>
                        <td class="text-center">{{$detail->filename}}</td>
                        <td class="text-center">{{$detail->status}}</td>
                        <td class="text-center">{{$detail->user->name}}</td>
                        <td class="text-center">
                            @if($detail->status != 'mapped')
                            <a href="{{ route('bank-statement.import.map', ['id' => $detail->id]) }}" class="btn btn-default">
                                {{__('Map & Import')}}
                            </a>
                            @else
                            <a href="{{ route('bank-statement.import.mapped.data', ['id' => $detail->id]) }}" class="btn btn-default">
                                {{__('Mapped Data')}}
                            </a>
                            @endif
                        </td>  
                    </tr>    
                @endforeach
            </tbody>
        </table>
        <div class="text-center">
            <div class="text-center">
                {!! $data->links() !!}
            </div>
        </div>
    </div> 
@endsection

@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script type="text/javascript">
    $('.select-multiple').select2({width: '100%'});

    function submitSearch(){
        src = '/users'
        term = $('#term').val()
        id = $('#user-select').val()
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                term : term,
                id : id,

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#users-table tbody").empty().html(data.tbody);
            $("#user_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
        src = '/users'
        blank = ''
        $.ajax({
            url: src,
            dataType: "json",
            data: {
               
               blank : blank, 

            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $('#term').val('')
            $('#user-select').val('')
            $("#users-table tbody").empty().html(data.tbody);
            $("#user_count").text(data.count);
            if (data.links.length > 10) {
                $('ul.pagination').replaceWith(data.links);
            } else {
                $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
    }
    $(".number .whatsapp_number").change(function(e){        
            e.preventDefault();
            $("#loading-image").show();
            $.ajax({
                type:"POST",
                url:"{{ route('user.changewhatsapp') }}",
                data:{
                    "_token": "{{ csrf_token() }}",
                    user_id: $(this).attr('data-user-id'),
                    whatsapp_number:$(this).val()
                },
                success:function(response){
                    $("#loading-image").hide();
                }
            });
        });
</script>

@endsection
