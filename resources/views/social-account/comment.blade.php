@extends('layouts.app')

@section('styles')

    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
        table {
table-layout: fixed !important;
}

table tr td {
max-width: 100% !important;
overflow-x: auto !important;
}

    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <h2 class="page-heading">Comments</h2>
                </div>
            </div>
        </div>

    </div>
    <input id="config-id" class="config-id" type="hidden" value="{{ $post->social_config_id ?? '' }}">
    <div class="mt-3">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="15%">Comment Original</th>
                    <th width="15%">Comment With Translation</th>
                    <th width="15%">Reply</th>
                    <th width="10%">User</th>
                    <th width="10%">Created At</th>
                    <th>Shortcuts</th>
                    <th width="5%">Action</th>
            </thead>
            <tbody>
                @forelse($comments as $key => $value)
                <tr>
                        <td>{{ $key + 1 }}</td>
                        <td style="width:50%">
                            <div style="word-break: break-word;">
                                @if ($value->message) {{ $value->message }} @else <small class="text-secondary">(No caption added)</small> @endif
                                <!-- @if ($value->translation) {{ $value->translation }} @else <small class="text-secondary">(No caption added)</small> @endif -->
                            </div>
                            @if ($value->photo)
                                <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                        <td style="width:50%">
                            <div style="word-break: break-word;">
                                <!-- @if ($value->message) {{ $value->message }} @else <small class="text-secondary">(No caption added)</small> @endif -->
                                @if ($value->translation) {{ $value->translation }} @else <small class="text-secondary">(No caption added)</small> @endif
                            </div>
                            @if ($value->photo)
                                <img src="{{ $value->photo }}" width="100" alt="{{ $value->message }}">
                            @endif
                        </td>
                     
                        <td class="message-input p-0 pt-2 pl-3">
                            <div class="cls_textarea_subbox">
                                <div class="btn-toolbar" role="toolbar">
                                    <div class="w-75">
                                        <textarea rows="1"
                                            class="form-control quick-message-field cls_quick_message addToAutoComplete"
                                            name="message" placeholder="Message" id="textareaBox_{{ $value->comment_id }}"
                                            data-customer-id="{{ $value->comment_id }}"></textarea>
                                    </div>
                                    <div class="w-25 pl-2" role="group" aria-label="First group">
                                        <button type="button" class="btn btn-sm m-0 p-0 mr-1 btn-image send-message1"
                                            data-id="textareaBox_{{ $value->comment_id }}">
                                            <img src="/images/filled-sent.png">
                                        </button>
                                    
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>{{ $value->user->name }}</td>
                        <td>{{ $value->time }}</td>
                        <td id="shortcutsIds">
                            @include('social-account.shortcuts')
                        </td>
                        <td>
                            <button id="showReplyButton" class="btn btn-light" title="Show Reply" data-comment-id="{{ $value->comment_id }}"><i class="fa fa-eye" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" align="center">No Comments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if (isset($posts))
            {{ $posts->links() }}
        @endif
    </div>

    <div id="showReplyModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Reply</h4>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>User</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody class="table-body"></tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '#showReplyButton', function(e) {
            $("#loading-image").show();
            const commentId = $(this).data('comment-id')
            $.ajax({
                url: "{{ route('social.account.comments.reply') }}",
                method: 'POST',
                async: true,
                data: {
                    _token: "{{ csrf_token() }}",
                    id: commentId
                },
                success: function(data) {
                    const comments = data.comments

                    $("#showReplyModal .modal-body .table-body").empty()
                    if (comments.length > 0) {

                        comments.forEach(element => {

                            let appendData = `<tr>
                                <td style="width:50%">
                                    <div>${element.message}</div>
                                    `;
                            if (element.photo) {
                                appendData +=
                                    `<img src="${element.photo}" width="100" alt="${element.message}" />`
                            }
                            appendData += `
                                </td>
                                <td style="white-space:nowrap">${element.user.name || ''}</td>
                                <td style="white-space:nowrap">${element.time}</td>
                            </tr> `
                            $("#showReplyModal .modal-body .table-body").append(appendData)

                        });
                    } else {
                        $("#showReplyModal .modal-body .table-body").append(`
                        <tr>
                            <td colspan="3" align="center">No reply found</td>
                        </tr>    
                        `)
                    }
                    $("#loading-image").hide();
                    $("#showReplyModal").modal("show")
                },
                error: function(error) {
                    alert("Couldn't load comment");
                    $("#loading-image").hide();
                    console.log(error);
                }
            })
        })

        $(document).on('click', '.send-message1', function() {
        const textareaId = $(this).data('id');
        const value = $(`#${textareaId}`).val();
        const configId = document.getElementById("config-id").value;  
        const contactId = $(`#${textareaId}`).data('customer-id');
        if (value.trim()) {
            $("#loading-image").show();
            $.ajax({
                url: "{{ route('social.dev.reply.comment') }}",
                method: 'POST',
                async: true,
                data: {
                    _token: '{{ csrf_token() }}',
                    input: value,
                    contactId: contactId,
                    configId: configId
                },
                success: function(res) {
                    $("#loading-image").hide();
                    document.getElementById("textareaBox_"+contactId).value = '';
                    toastr["success"]("Message successfully send!", "Message")
                },
                error: function(error) {
                    console.log(error.responseJSON);
                    alert("Counldn't send messages")
                    $("#loading-image").hide();
                }
            })
        } else {
            alert("Please enter a message")
        }
    })

</script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript">
var siteHelpers = {
            
    quickCategoryAdd : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var textBox = ele.closest("div").find(".quick_category");
        if (textBox.val() == "") {
            alert("Please Enter Category!!");
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                name : textBox.val(),
                quickCategoryId : quickCategoryId
            },
            url: "/add-reply-category"
        };

        if(quickCategoryId!=''){
            siteHelpers.sendAjax(params,"afterQuickSubCategoryAdd");
        } else {
            siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
        }
    },
    afterQuickSubCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickSubCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    afterQuickCategoryAdd : function(response) {
        $(".quick_category").val('');
        $(".quickCategory").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    deleteQuickCategory : function(ele) {
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickSubCategory : function(ele) {
        var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
        if (quickSubCategory.val() == "") {
            alert("Please Select Sub Category!!");
            return false;
        }
        var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete sub category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickSubCategoryId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickComment : function(ele) {
        var quickComment = ele.closest("#shortcutsIds").find(".quickCommentEmail");
        if (quickComment.val() == "") {
            alert("Please Select Quick Comment!!");
            return false;
        }
        var quickCommentId = quickComment.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete comment?")) {
            return false;
        }
        var params = {
            method : 'DELETE',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content')
            },
            url: "/reply/" + quickCommentId,
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    pageReload : function(response) {
        location.reload();
    },
    quickCommentAdd : function(ele) {
        var textBox = ele.closest("div").find(".quick_comment");
        var quickCategory = ele.closest("#shortcutsIds").find(".quickCategory");
        var quickSubCategory = ele.closest("#shortcutsIds").find(".quickSubCategory");
        if (textBox.val() == "") {
            alert("Please Enter New Quick Comment!!");
            return false;
        }
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var quickSubCategoryId = quickSubCategory.children("option:selected").data('id');
        var formData = new FormData();
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append("reply", textBox.val());
        formData.append("category_id", quickCategoryId);
        formData.append("sub_category_id", quickSubCategoryId);
        formData.append("model", 'Approval Lead');
        var params = {
            method : 'post',
            data : formData,
            url: "/reply"
        };
        siteHelpers.sendFormDataAjax(params,"afterQuickCommentAdd");
    },
    afterQuickCommentAdd : function(reply) {
        $(".quick_comment").val('');
        $('.quickCommentEmail').append($('<option>', {
            value: reply,
            text: reply
        }));
    },
    changeQuickCategory : function (ele) {

        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('value');

        ele.closest("#shortcutsIds").find('.quickSubCategory').empty();
        ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
            value: '',
            text: 'Select Sub Category'
        }));
        dataValue.forEach(function (category) {
            ele.closest("#shortcutsIds").find('.quickSubCategory').append($('<option>', {
                value: category.name,
                text: category.name,
                'data-id': category.id
            }));
        });

        if (ele.val() != "") {
            var replies = JSON.parse(ele.val());
            ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
            ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                value: '',
                text: 'Quick Reply'
            }));
            replies.forEach(function (reply) {
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id
                }));
            });
        }
    },
    changeQuickComment : function (ele) {
        $('#textareaBox_'+ele.attr('data-id')).val(ele.val());        
    },
    changeQuickSubCategory : function (ele) {
        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('id');

        var userEmaillUrl = '/social/email-replise/'+dataValue;

        $.ajax({        
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {

            if(response!=''){
                var replies = JSON.parse(response);
                ele.closest("#shortcutsIds").find('.quickCommentEmail').empty();
                ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                    value: '',
                    text: 'Quick Reply'
                }));
                replies.forEach(function (reply) {
                    ele.closest("#shortcutsIds").find('.quickCommentEmail').append($('<option>', {
                        value: reply.reply,
                        text: reply.reply,
                        'data-id': reply.id
                    }));
                });
            }
            
        }).fail(function(errObj) {
        })
    },
};

$.extend(siteHelpers, common);

$(document).on('click', '.quick_category_add', function () {
    siteHelpers.quickCategoryAdd($(this));
});
$(document).on('click', '.delete_category', function () {
    siteHelpers.deleteQuickCategory($(this));
});
$(document).on('click', '.delete_sub_category', function () {
    siteHelpers.deleteQuickSubCategory($(this));
});
$(document).on('click', '.delete_quick_comment', function () {
    siteHelpers.deleteQuickComment($(this));
});
$(document).on('click', '.quick_comment_add', function () {
    siteHelpers.quickCommentAdd($(this));
});
$(document).on('change', '.quickCategory', function () {
    siteHelpers.changeQuickCategory($(this));
});
$(document).on('change', '.quickCommentEmail', function () {
    siteHelpers.changeQuickComment($(this));
});
$(document).on('change', '.quickSubCategory', function () {
    siteHelpers.changeQuickSubCategory($(this));
});
</script>
@endsection
