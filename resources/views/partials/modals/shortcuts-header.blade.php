<div id="shortcut-header-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6 d-inline form-inline">
                        <input style="width: 87%" type="text" name="category_name" placeholder="Enter New Category" class="form-control mb-3 quick_category">
                        <button class="btn custom-button quick_category_addHeader" style="position: absolute; padding: 5px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                        <div style="float: left; width: 86%">
                            <select name="quickCategoryHeader" class="form-control mb-3 quickCategoryHeader">
                                <option value="">Select Category</option>

                                @php
                                $reply_categories = \App\ReplyCategory::select('id', 'name')->with('approval_leads', 'sub_categories')->where('parent_id', 0)->orderby('name', 'ASC')->get();
                                @endphp

                                @foreach($reply_categories as $category)
                                    <option value="{{ $category->approval_leads }}" data-id="{{$category->id}}" data-value="{{ $category->sub_categories }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="float: right; width: 14%;">
                            <a class="btn custom-button delete_categoryHeader" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-inline form-inline">
                        <input style="width: 87%" type="text" name="quick_comment" placeholder="Enter New Quick Comment" class="form-control mb-3 quick_comment">
                        <button class="btn custom-button quick_comment_addHeader" style="position: absolute; padding: 5px;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                    </div>
                    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                        <div style="float: left; width: 86%">
                            <select name="quickSubCategoryHeader" class="form-control quickSubCategoryHeader">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div style="float: right; width: 14%;">
                            <a class="btn custom-button delete_sub_categoryHeader" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 d-inline form-inline p-0" style="padding-left: 0px;">
                    </div>
                    <div class="col-6 d-inline form-inline" style="padding-left: 0px;">
                        <div style="float: left; width: 86%">
                            <select name="quickComment" class="form-control quickCommentEmailHeader">
                                <option value="">Quick Reply</option>
                            </select>
                        </div>
                        <div style="float: right; width: 14%;">
                            <a class="btn custom-button delete_quick_commentHeader" style="padding: 5px;"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript">
var siteHelpers = {
            
    quickCategoryHeaderAdd : function(ele) {
        var quickCategoryHeader = ele.closest("#shortcut-header-modal").find(".quickCategoryHeader");
        var quickCategoryHeaderId = quickCategoryHeader.children("option:selected").data('id');
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
                quickCategoryHeaderId : quickCategoryHeaderId
            },
            url: "/add-reply-category"
        };

        if(quickCategoryHeaderId!=''){
            siteHelpers.sendAjax(params,"afterquickSubCategoryHeaderAdd");
        } else {
            siteHelpers.sendAjax(params,"afterquickCategoryHeaderAdd");
        }
    },
    afterquickSubCategoryHeaderAdd : function(response) {
        $(".quick_category").val('');
        $(".quickSubCategoryHeader").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    afterquickCategoryHeaderAdd : function(response) {
        $(".quick_category").val('');
        $(".quickCategoryHeader").append('<option value="[]" data-id="' + response.data.id + '">' + response.data.name + '</option>');
    },
    deletequickCategoryHeader : function(ele) {
        var quickCategoryHeader = ele.closest("#shortcut-header-modal").find(".quickCategoryHeader");
        if (quickCategoryHeader.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryHeaderId = quickCategoryHeader.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickCategoryHeaderId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deletequickSubCategoryHeader : function(ele) {
        var quickSubCategoryHeader = ele.closest("#shortcut-header-modal").find(".quickSubCategoryHeader");
        if (quickSubCategoryHeader.val() == "") {
            alert("Please Select Sub Category!!");
            return false;
        }
        var quickSubCategoryHeaderId = quickSubCategoryHeader.children("option:selected").data('id');
        if (!confirm("Are sure you want to delete sub category?")) {
            return false;
        }
        var params = {
            method : 'post',
            data : {
                _token : $('meta[name="csrf-token"]').attr('content'),
                id : quickSubCategoryHeaderId
            },
            url: "/destroy-reply-category"
        };
        siteHelpers.sendAjax(params,"pageReload");
    },
    deleteQuickCommentHeader : function(ele) {
        var quickComment = ele.closest("#shortcut-header-modal").find(".quickCommentEmailHeader");
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
    quickCommentAddHeader : function(ele) {
        var textBox = ele.closest("div").find(".quick_comment");
        var quickCategoryHeader = ele.closest("#shortcut-header-modal").find(".quickCategoryHeader");
        var quickSubCategoryHeader = ele.closest("#shortcut-header-modal").find(".quickSubCategoryHeader");
        if (textBox.val() == "") {
            alert("Please Enter New Quick Comment!!");
            return false;
        }
        if (quickCategoryHeader.val() == "") {
            alert("Please Select Category!!");
            return false;
        }
        var quickCategoryHeaderId = quickCategoryHeader.children("option:selected").data('id');
        var quickSubCategoryHeaderId = quickSubCategoryHeader.children("option:selected").data('id');
        var formData = new FormData();
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append("reply", textBox.val());
        formData.append("category_id", quickCategoryHeaderId);
        formData.append("sub_category_id", quickSubCategoryHeaderId);
        formData.append("model", 'Approval Lead');
        var params = {
            method : 'post',
            data : formData,
            url: "/reply"
        };
        siteHelpers.sendFormDataAjax(params,"afterquickCommentAddHeader");
    },
    afterquickCommentAddHeader : function(reply) {
        $(".quick_comment").val('');
        $('.quickCommentEmailHeader').append($('<option>', {
            value: reply,
            text: reply
        }));
    },
    changequickCategoryHeader : function (ele) {

        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('value');

        ele.closest("#shortcut-header-modal").find('.quickSubCategoryHeader').empty();
        ele.closest("#shortcut-header-modal").find('.quickSubCategoryHeader').append($('<option>', {
            value: '',
            text: 'Select Sub Category'
        }));
        dataValue.forEach(function (category) {
            ele.closest("#shortcut-header-modal").find('.quickSubCategoryHeader').append($('<option>', {
                value: category.name,
                text: category.name,
                'data-id': category.id
            }));
        });

        if (ele.val() != "") {
            var replies = JSON.parse(ele.val());
            ele.closest("#shortcut-header-modal").find('.quickCommentEmailHeader').empty();
            ele.closest("#shortcut-header-modal").find('.quickCommentEmailHeader').append($('<option>', {
                value: '',
                text: 'Quick Reply'
            }));
            replies.forEach(function (reply) {
                ele.closest("#shortcut-header-modal").find('.quickCommentEmailHeader').append($('<option>', {
                    value: reply.reply,
                    text: reply.reply,
                    'data-id': reply.id
                }));
            });
        }
    },
    changeQuickComment : function (ele) {

        var textToCopy = ele.val();

        // Create a temporary input element
        var tempInput = $("<input>");

        // Set the value of the input to the text you want to copy
        tempInput.val(textToCopy);

        // Append the input to the body
        $("body").append(tempInput);

        // Select the text in the input
        tempInput.select();

        // Execute the copy command
        document.execCommand("copy");

        // Remove the temporary input
        tempInput.remove();

        alert("Text copied");
    },
    changequickSubCategoryHeader : function (ele) {
        var selectedOption = ele.find('option:selected');
        var dataValue = selectedOption.data('id');

        var userEmaillUrl = '/email/email-replise/'+dataValue;

        $.ajax({        
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: userEmaillUrl,
            type: 'get',
        }).done( function(response) {

            if(response!=''){
                var replies = JSON.parse(response);
                ele.closest("#shortcut-header-modal").find('.quickCommentEmailHeader').empty();
                ele.closest("#shortcut-header-modal").find('.quickCommentEmailHeader').append($('<option>', {
                    value: '',
                    text: 'Quick Reply'
                }));
                replies.forEach(function (reply) {
                    ele.closest("#shortcut-header-modal").find('.quickCommentEmailHeader').append($('<option>', {
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

$(document).on('click', '.quick_category_addHeader', function () {
    siteHelpers.quickCategoryHeaderAdd($(this));
});
$(document).on('click', '.delete_categoryHeader', function () {
    siteHelpers.deletequickCategoryHeader($(this));
});
$(document).on('click', '.delete_sub_categoryHeader', function () {
    siteHelpers.deletequickSubCategoryHeader($(this));
});
$(document).on('click', '.delete_quick_commentHeader', function () {
    siteHelpers.deleteQuickCommentHeader($(this));
});
$(document).on('click', '.quick_comment_addHeader', function () {
    siteHelpers.quickCommentAddHeader($(this));
});
$(document).on('change', '.quickCategoryHeader', function () {
    siteHelpers.changequickCategoryHeader($(this));
});
$(document).on('change', '.quickCommentEmailHeader', function () {
    siteHelpers.changeQuickComment($(this));
});
$(document).on('change', '.quickSubCategoryHeader', function () {
    siteHelpers.changequickSubCategoryHeader($(this));
});
</script>