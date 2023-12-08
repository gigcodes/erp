<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-6 d-inline form-inline">
                <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
                <button class="btn btn-secondary quick_category_add" style="position: absolute;  margin-left: 8px;">+</button>
            </div>
            <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                <div style="float: left; width: 86%">
                    <select name="quickCategory" class="form-control mb-3 quickCategory">
                        <option value="">Select Category</option>

                        @php
                        $reply_categories = \App\ReplyCategory::select('id', 'name')->with('approval_leads')->orderby('name', 'ASC')->get();
                        @endphp

                        @foreach($reply_categories as $category)
                            <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="float: right; width: 14%;">
                    <a class="btn btn-image delete_category"><img src="/images/delete.png"></a>
                </div>
            </div>
            <div class="col-6 d-inline form-inline">
                <input style="width: 87%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
                <button class="btn btn-secondary quick_comment_add" style="position: absolute;  margin-left: 8px;">+</button>
            </div>
            <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                <div style="float: left; width: 86%">
                    <select name="quickComment" class="form-control quickCommentEmail" data-id="{{$task->id}}">
                        <option value="">Quick Reply</option>
                    </select>
                </div>
                <div style="float: right; width: 14%;">
                    <a class="btn btn-image delete_quick_comment"><img src="/images/delete.png"></a>
                </div>
            </div>
        </div>
    </div>    
</div>
<script type="text/javascript">
var siteHelpers = {
            
    quickCategoryAdd : function(ele) {
        var textBox = ele.closest("div").find(".quick_category");
        if (textBox.val() == "") {
            alert("Please Enter Category!!");
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                name : textBox.val()
            },
            url: "/add-reply-category"
        };
        siteHelpers.sendAjax(params,"afterQuickCategoryAdd");
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
        if (textBox.val() == "") {
            alert("Please Enter New Quick Comment!!");
            return false;
        }
        if (quickCategory.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryId = quickCategory.children("option:selected").data('id');
        var formData = new FormData();
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append("reply", textBox.val());
        formData.append("category_id", quickCategoryId);
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
        $('#getMsg'+ele.attr('data-id')).val(ele.val());

        var userEmaillUrl = '/email/email-frame-info/'+$('#reply_email_id').val();;
        var senderName = 'Hello '+$('#sender_email_address').val().split('@')[0]+',';

        $("#reply-message").val(senderName)

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {
            $("#reply-message").val(senderName+'\n\n'+ele.val()+'\n\n'+response)
        }).fail(function(errObj) {
        })
        
    }
};


$(document).on('click', '.quick_category_add', function () {
    siteHelpers.quickCategoryAdd($(this));
});
$(document).on('click', '.delete_category', function () {
    siteHelpers.deleteQuickCategory($(this));
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
</script>