@extends('layouts.app')
@section('title', 'Auto Refresh Page')
@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Auto Refresh Page</h2>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Whoops!</strong> There were some problems with your input.<br><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="row">
    <div class="col-lg-12 margin-tb">
        <button class="btn btn-secondary btn-create-auto-refresh-page">Create Auto Refresh Page</button>
    </div>
</div>

<div class="table-responsive mt-3">
    <table class="table table-bordered" id="category-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Page</th>
                <th>Time</th>
                <th>User</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
             @include('auto-refresh-page.partials.data')
        </tbody>
    </table>
</div>
{!! $pages->appends(Request::except('page'))->links() !!}


<div class="modal fade" id="create-auto-refresh-page" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="/system/auto-refresh/create" method="post">
            {{ csrf_field() }}
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Auto Refresh Page</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" id="createsizeform">
                        <div class="col-md-10">
                            <label for="create-page">Page Url</label>
                            <input type="text" class="form-control nav-link" id="create-page" name="page" placeholder="Page Url" style="margin-top : 1%;">
                        </div>
                        <div class="col-md-10">
                            <label for="create-time">Time (in second)</label>
                            <input type="text" class="form-control nav-link" id="create-time" name="time" placeholder="Time" style="margin-top : 1%;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="edit-auto-refresh-page" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        
    </div>
</div>
@endsection
@section('scripts')

<script>
$(document).on("click",".btn-create-auto-refresh-page",function() {
    $("#create-auto-refresh-page").modal("show");
});

$(document).on("click",".edit-page",function(e) {
    e.preventDefault();
    var $this = $(this);
    $.ajax({
        url:'/system/auto-refresh/'+$this.data("id")+"/edit",
        success:function(result){
            $("#edit-auto-refresh-page").find(".modal-dialog").html(result);
            $("#edit-auto-refresh-page").modal("show");
        },
        error:function(exx){
            console.log(exx)
        }
    })
});




$(document).ready(function() {
    $(document).on('click','#canceledit',function(){
        $('#editsizeform').hide();
        $('#createsizeform').show();
    });
    $(document).on('click','.systemsizeedit',function(){
        $('#systemsizenameedit').val($(this).data('name'));
        $('#systemsizeeditid').val($(this).data('id'));
        $('#editsizeform').show();
        $('#createsizeform').hide();
    });
    $(document).on('click','.systemsizedelete',function(){
        let id = $(this).data('id');
        $selector = $(this).parent().parent(); 
        if (confirm('Are you sure want to delete!')){
            $.ajax({
                url:'{{route("system.size.delete")}}',
                dataType:'json',
                data:{
                    id: id,
                },
                success:function(result){
                    $selector.remove();   
                     window.location.reload();
                },
                error:function(exx){
                    console.log(exx)
                }
            })
        }
    })
    $(document).on('click','#sizestorebtn',function(){
        $.ajax({
            url:'{{route("system.size.store")}}',
            dataType:'json',
            data:{
                name: $('#systemsizename').val(),
            },
            success:function(result){
                window.location.reload();
            },
            error:function(exx){
                if (exx.status == 422){
                    $.each(exx.responseJSON.errors,function(key,value){
                        $('[name="'+key+'"]').parent().append('<span class="error">'+value[0]+'</span>')
                    });
                }else{
                    alert('Something went wrong!');
                }
            }
        })
    });
    $(document).on('click','#sizestorebtnupdate',function(){
        $.ajax({
            url:'{{route("system.size.update")}}',
            dataType:'json',
            data:{
                code: $('#systemsizenameedit').val(),
                id: $('#systemsizeeditid').val(),
            },
            success:function(result){
                window.location.reload();
            },
            error:function(exx){
                if (exx.status == 422){
                    $.each(exx.responseJSON.errors,function(key,value){
                        $('[name="'+key+'"]').parent().append('<span class="error">'+value[0]+'</span>')
                    });
                }else{
                    alert('Something went wrong!');
                }
            }
        })
    });
    
    $(document).on('submit','#createsizeformmodel',function(e){
        e.preventDefault();
        $.ajax({
            url:'{{route("system.size.managerstore")}}',
            type:'POST',
            data:$('#createsizeformmodel').serialize(),
            success:function(result){
                if (result.success){
                    window.location.reload();
                }else{
                    $('.alert-sizemanager').text(result.message);
                    $('.alert-sizemanager').show();
                    setTimeout(function(){
                        $('.alert-sizemanager').hide();
                    },10000);
                }
            },
            error:function(exx){
                alert('Something went wrong!')
            }
        })
    });
    $(document).on('click','.editmanager',function(){
        $('#loading-image-preview').show()
        let id = $(this).data('id');
        $.ajax({
            url:'{{route("system.size.manageredit")}}',
            dataType:'json',
            data:{
                id:id
            },
            success:function(result){
                $('.sizevarintinput1').remove();
                $('#editnmanagerf').append(result.data);
                $('#categorydrpedit').val(result.category_id);
                $('#sizemanagementedit').modal('show');
                $('#loading-image-preview').hide()
            },
            error:function(exx){
                $('#loading-image-preview').hide()
                alert('Something went wrong!')
            }
        })
    });
    $(document).on('submit','#updatesizeformmodel',function(e){
        e.preventDefault();
        $.ajax({
            url:'{{route("system.size.managerupdate")}}',
            type:'POST',
            data:$('#updatesizeformmodel').serialize(),
            success:function(result){
                if (result.success){
                    window.location.reload();
                }else{
                    $('.alert-sizemanageredit').text(result.message);
                    $('.alert-sizemanageredit').show();
                    setTimeout(function(){
                        $('.alert-sizemanageredit').hide();
                    },10000);
                }
            },
            error:function(exx){
                alert('Something went wrong!')
            }
        });
    });  
    $(document).on('click','.deletemanager',function(){
        let id = $(this).data('id');
        if (confirm('Are you sure want do delete?')){
            $.ajax({
                url:'{{route("system.size.managerdelete")}}',
                dataType:'json',
                data:{
                    id:id,
                },
                success:function(result){
                    window.location.reload();
                },
                error:function(exx){
                    alert('Something went wrong!')
                }
            });
        }
    });  
    // $(document).on('click','#sizemanagementmodelbtn',function(){
    //     checkVariant();
    // });
    $(document).on('change','#categorydrp',function(){
        // checkVariant();
    });
    function checkVariant(){
        console.log('aas');
        let id = $('#categorydrp').val();
        if (id != null && id != ''){
            $('#loading-image-preview').show()
            $.ajax({
                url:'{{route("system.size.managercheckexistvalue")}}',
                dataType:'json',
                data:{
                    id:id,
                },
                success:function(result){
                    $('.sizevarintinput').remove();
                    $('#sizevariant').before(result.data);
                    $('#loading-image-preview').hide()
                },
                error:function(exx){
                    $('#loading-image-preview').hide()
                    alert('Something went wrong1!')
                }
            });
        }else{
            $('.sizevarintinput').remove();

        }
    }
});  
</script>
@endsection
