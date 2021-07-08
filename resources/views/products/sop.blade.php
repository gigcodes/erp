@extends('layouts.app')

@section('content')

@section('styles')
    <!-- START - Purpose : Add CSS - DEVTASK-4289 -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style type="text/css">
        table tr td {
            overflow-wrap: break-word;
        }

        .page-note {
            font-size: 14px;
        }

        .flex {
            display: flex;
        }

    </style>
    <!-- END - DEVTASK-4289 -->
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
@section('content')

    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading"> ListingApproved - SOP</h2>
        </div>
        <div class="col-lg-12 margin-tb">

            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form method="get" action="{{ route('sop.post') }}">
                            <div class="flex">
                                <div class="col" id="search-bar">

                                    <input type="text" value="{{ old('search') }}" name="search" class="form-control"
                                        placeholder="Search Here..">
                                    {{-- <input type="text" name="search" id="search" class="form-control search-input" placeholder="Search Here Text.." autocomplete="off"> --}}
                                </div>

                                <button type="submit" style="display: inline-block;width: 10%"
                                    class="btn btn-sm btn-image search-button">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>

                                <a href="{{ route('sop.add') }}" type="button" class="btn btn-image" id=""><img
                                        src="/images/resend2.png"></a>

                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#exampleModal">+</button>
            </div>
        </div>
    </div>

    <!-- Button trigger modal -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Name</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="FormModal">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" required />
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <input type="text" class="form-control" id="content" required />
                        </div>


                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btnsave" id="btnsave">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="pagenote-scroll">
            <!-- Purpose : Add Div for Scrolling - DEVTASK-4289 -->
            <div class="table-responsive">
                <table cellspacing="0" role="grid"
                    class="page-notes table table-bordered datatable mdl-data-table dataTable page-notes" style="width:100%"
                    id="NameTable">
                    <thead>
                        <tr>
                            <th width="5%">ID</th>

                            <th width="25%">Name</th>
                            <th width="25%">Content</th>
                            <th width="15%">Created at</th>
                            <th width="15%">Updated at</th>
                            <th width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
{{-- {{ dd($usersop) }} --}}
                        {{-- @if ($usersop->total() >= 1) --}}
                        @if (count($usersop))
                        @foreach ($usersop as $key => $value)
                            <tr id="sid{{ $value->id }}" class="parent_tr" data-id="{{ $value->id }}">
                                <td class="sop_table_id">{{ $value->id }}</td>
                                <td class="sop_table_name">{{ $value->name }}</td>
                                <td>{!! $value->content !!}</td>

                                <td>{{ date('m-d  H:i', strtotime($value->created_at)) }}</td>
                                <td>{{ date('m-d  H:i', strtotime($value->updated_at)) }}</td>
                                <td>
                                   
                                    <a href="javascript:;" data-id="{{ $value->id }}"
                                        class="editor_edit btn-xs btn btn-image p-2">
                                        <img src="/images/edit.png"></a>
                                    {{-- <a onclick="editname({{$value->id}})" class="btn btn-image"> <img src="/images/edit.png"></a> --}}

                                    <a class="btn btn-image deleteRecord" data-id="{{ $value->id }}"><img
                                            src="/images/delete.png" /></a>

                                  
                                </td>
                        @endforeach
                        @endif
                        {{-- @endif --}}
                    </tbody>
                </table>
{{                $usersop->appends(request()->input())->links()}}
            </div>

        </div>
    </div>

    

    <div id="erp-notes" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo route('updateName'); ?>" id="sop_edit_form">
                        <input type="text" hidden name="id" id="sop_edit_id">
                        @csrf
                        <div class="form-group">
                            <label for="name">Notes:</label>
                            <input type="text" class="form-control" name="name" id="sop_edit_name">
                        </div>

                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control sop_edit_class" name="content" id="sop_edit_content"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success ml-3 update-user-notes">Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>



   
@endsection

@section('scripts')
    <script src="https://cdn.ckeditor.com/4.11.4/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content');
        CKEDITOR.replace('sop_edit_content');
    </script>
    <script>
        $('#FormModal').submit(function(e) {
            e.preventDefault();
            let name = $("#name").val();
            let content = CKEDITOR.instances['content'].getData(); //$('#cke_content').html();//$("#content").val();

            let _token = $("input[name=_token]").val();


            $.ajax({
                url: "{{ route('sop.add') }}",
                type: "POST",
                data: {
                    name: name,
                    content: content,

                    _token: _token
                },
                success: function(response) {
                    console.log('response', response);
                    if (response) {
                        //    $("#NameTable tbody").append('<tr><td>'+response.id+'</td><td>'+ response.name +'</td><td>'+ response.content +'</td><td>'+ response.created_at +'</td><td>'+ response.updated_at +'</td><td></td></tr>');

                        $("#NameTable tbody").append(`<tr><td>${response.id}</td><td> ${response.name} </td><td> ${response.content} </td><td> ${response.created_at} </td><td> ${response.updated_at} </td><td>
                                
                                <a href="javascript:;" data-id = "${response.id}" class="editor_edit btn-xs btn btn-image p-2">
                                            <img src="/images/edit.png"></a>
                                           

                                        <a class="btn btn-image deleteRecord" data-id="${response.id}" ><img src="/images/delete.png" /></a>
                                
                                
                                </td></tr>`);

                        // CKEDITOR.instances['content'].getData('ssfasd')
                        $("#FormModal")[0].reset();
                        $('.cke_editable p').text(' ')
                        CKEDITOR.instances['content'].setData('')
                        $("#exampleModal").modal('hide');
                        toastr["success"]("Data Inserted Successfully!", "Message")
                    }
                }


            });
        });
    </script>
    <script>

           
        $(document).on('click', '.deleteRecord', function() {
            // $(".deleteRecord").click(function(){
            //    e.preventDefault();
            let $this = $(this)
            console.log($this)

            alert('Are You Sure Want To Delete This Records?');
            var id = $(this).data("id");
            var token = $("meta[name='csrf-token']").attr("content");

            $.ajax({
                url: "sopdel/" + id,
                type: 'DELETE',
                data: {
                    "id": id,
                    "_token": token,
                },
                success: function(response) {

                    // $("#sid"+id).remove();
                    $this.closest('.parent_tr').remove()
                    toastr["error"]("Data deleted Successfully!", "Message")
                    //  location.reload(true);
                }
            });

        });
    
</script>
    <script>
        
        $(document).on('click', '.editor_edit', function() {

            var $this = $(this);

            $.ajax({
                type: "GET",
                data: {
                    id: $this.data("id")

                },
                url: "{{ route('editName') }}"
            }).done(function(data) {
                
                console.log(data);
                // $("#erp-notes").find(".modal-body").html(data);
                $('#sop_edit_id').val(data.id)
                $('#sop_edit_name').val(data.name)
                console.log($('#sop_edit_class'), 'aaa')
                // $('.sop_edit_class').text(data.content)
                CKEDITOR.instances['sop_edit_content'].setData(data.content)

                $("#erp-notes #sop_edit_form").attr('data-id', $($this).attr('data-id'));
                $("#erp-notes").modal("show");

            }).fail(function(response) {
                console.log(response);
            });
        });
</script>
<script>
        $(document).on('submit', '#sop_edit_form', function(e) {
            e.preventDefault();
            const $this = $(this)
            $(this).attr('data-id', );
            console.log($(this))
            //   var $this = $(this)[0];
            //   var $form  = $this.closest("form");
            $.ajax({
                type: "POST",
                data: $(this).serialize(),
                url: "{{ route('updateName') }}",
                datatype: "json"
            }).done(function(data) {
                console.log(data)
                // alert(data.name);
                let id = $($this).attr('data-id');
                $('#sid' + id + ' td:nth-child(2)').html(data.name);
                $('#sid' + id + ' td:nth-child(3)').html(data.content);
                //   console.log(data)
                //    $("#erp-notes").find(".modal-body").html("");
                $("#erp-notes").modal("hide");
                toastr["success"]("Data Updated Successfully!", "Message")

            }).fail(function(response) {
                console.log(response);
            });
        });
    </script>
      
@endsection
