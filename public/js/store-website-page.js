var page = {
    init: function(settings) {

        page.config = {
            bodyView: settings.bodyView
        };

        settings.baseUrl += "/store-website";

        $.extend(page.config, settings);

        this.getResults();

        //initialize pagination
        page.config.bodyView.on("click",".page-link",function(e) {
            e.preventDefault();
            page.getResults($(this).attr("href"));
        });

        page.config.bodyView.on("click",".btn-search-action",function(e) {
            e.preventDefault();
            page.getResults();
        });

        page.config.bodyView.on("click",".btn-add-action",function(e) {
            e.preventDefault();
            page.createRecord();
        });

        // delete product templates
        page.config.bodyView.on("click",".btn-delete-template",function(e) {
            if(!confirm("Are you sure you want to delete record?")) {
                return false;
            }else {
                page.deleteRecord($(this));
            }
        });

        page.config.bodyView.on("click",".btn-edit-template",function(e) {
            page.editRecord($(this));
        });

        $(".common-modal").on("click",".submit-store-site",function() {
            page.submitFormSite($(this));
        });

        page.config.bodyView.on("click",".btn-push",function(e) {
            page.push($(this));
        });

        page.config.bodyView.on("click",".btn-pull",function(e) {
            page.pull($(this));
        });

        $(document).on("change",".store-website-change",function(e) {
            e.preventDefault();
            page.findStores($(this));
        });

        $(document).on("change",".website-page-change",function(e) {
            e.preventDefault();
            page.loadPage($(this));
        });

        $(document).on("change",".website-language-change",function(e) {
            e.preventDefault();
            page.loadTranslation($(this));
        });

        $(document).on("click",".btn-find-history",function(e) {
            e.preventDefault();
            page.loadHistory($(this));
        });

        $(document).on("click",".btn-activities",function(e) {
            e.preventDefault();
            page.loadActivities($(this));
        });

        $(document).on("click",".btn-translate-for-other-language",function(e) {
            e.preventDefault();
            page.translateForOtherLanguage($(this));
        });

        $(document).on("click",".push-pages-store-wise",function(e) {
            e.preventDefault();
            page.pushPageInLive($(this));
        });

        $(document).on("click",".pull-pages-store-wise",function(e) {
            e.preventDefault();
            page.pullPageInLive($(this));
        });

    },
    validationRule : function(response) {
         $(document).find("#product-template-from").validate({
            rules: {
                name     : "required",
            },
            messages: {
                name     : "Template name is required",
            }
        })
    },
    loadFirst: function() {
        var _z = {
            url: this.config.baseUrl + "/page/records",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/records",
            method: "get",
            data : $(".message-search-handler").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    showResults : function(response) {
        $("#loading-image").hide();
        var addProductTpl = $.templates("#template-result-block");
        var tplHtml       = addProductTpl.render(response);

        $(".count-text").html("("+response.total+")");

        page.config.bodyView.find("#page-view-result").html(tplHtml);
    }
    ,
    deleteRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+ele.data("id")+"/delete",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'deleteResults');
    },
    deleteResults : function(response) {
        if(response.code == 200){
            this.getResults();
            toastr['success']('Request deleted successfully', 'success');
            location.reload();
        }else{
            toastr['error']('Oops.something went wrong', 'error');
        }

    },
    createRecord : function(response) {
        var createWebTemplate = $.templates("#template-create-website");
        var tplHtml = createWebTemplate.render({data:{}});

        var common =  $(".common-modal");
            common.find(".modal-dialog").html(tplHtml); 
            common.modal("show");
    },
    editRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+ele.data("id")+"/edit",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'editResult');
    },
    editResult : function(response) {
        $("#loading-image").hide();
        var createWebTemplate = $.templates("#template-create-website");
        var tplHtml = createWebTemplate.render(response);
        var common =  $(".common-modal");
            common.find(".modal-dialog").html(tplHtml); 
            common.modal("show");
            $('input[name="meta_keywords"]').trigger('change');
            $('textarea[name="meta_description"]').trigger('change');
            $('#google_translate_element').summernote();

        //new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    },
    submitFormSite : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/save",
            method: "post",
            data : ele.closest("form").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "saveSite");
    },
    assignSelect2 : function () {
        var selectList = $("select.select-searchable");
            if(selectList.length > 0) {
                $.each(selectList,function(k,v){
                    var element = $(v);
                    if(!element.hasClass("select2-hidden-accessible")){
                        element.select2({tags:true,width:"100%"});
                    }
                });
            }
    },
    saveSite : function(response) {
        if(response.code  == 200) {
            page.loadFirst();
            $(".common-modal").modal("hide");
        }else {
            $("#loading-image").hide();
            toastr["error"](response.error,"");
        }
    },
    push : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+ele.data("id")+"/push",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'afterPush');
    },
    afterPush : function(response) {
        if(response.code  == 200) {
            toastr["success"](response.message,"");
            location.reload();
        }else {
            $("#loading-image").hide();
            toastr["error"](response.error,"");
        }
    },
    pull : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+ele.data("id")+"/pull",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'afterPull');
    },
    afterPull : function(response) {
        if(response.code  == 200) {
            toastr["success"](response.message,"");
            location.reload();
        }else {
            $("#loading-image").hide();
            toastr["error"](response.error,"");
        }
    },
    findStores : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+ele.val()+"/get-stores",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'afterStores');
    },
    afterStores : function(response) {
        if(response.code  == 200) {
            $(".store-selection").find("option").remove();
            $.each(response.stores, function (index, value) {
                $(".store-selection").append($('<option/>', { 
                    value: value.code,
                    text : value.code 
                }));
            });
            $("#loading-image").hide();
            $(".store-selection").select2({});
        }
    },
    loadPage : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+ele.val()+"/load-page",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'afterLoadPage');
    },
    afterLoadPage : function(response) {
        if(response.code  == 200) {
            $("#loading-image").hide();
            $('#google_translate_element').summernote('reset');
            $('#google_translate_element').summernote('insertText', response.content);
        }
    },
    loadTranslation : function(ele) {
        let language = ele.val();
        let page     = $(".website-page-change").val();
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/page/"+page+"/load-page",
            method: "get",
            data : {
                language : language
            },
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'afterLoadTranslation');
    },
    afterLoadTranslation : function(response) {
        if(response.code  == 200) {
            $("#loading-image").hide();
            $(".content-preview").html(response.content);
        }
    },
    loadHistory:function(ele) {
        
        let page     = ele.data("id");
        
        var _z = {
            url: this.config.baseUrl + "/page/"+page+"/history",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }

        this.sendAjax(_z, 'afterLoadHistory');
    },
    afterLoadHistory : function(response) {
        var html = ``;
        if(response.code  == 200) {
            $.each(response.data,function(k,v) {
                var user = (v.user) ? v.user.name : "";
                html += `<tr>
                    <td>`+v.id+`</td>
                    <td>`+v.content+`</td>
                    <td>`+user+`</td>
                    <td>`+v.created_at+`</td>
                </tr>`;
            })
            $("#preview-history-tbody").html(html);
            $("#loading-image").hide();
            $(".preview-history-modal").modal("show");
        }
    },
    loadActivities:function(ele) {
        
        let page     = ele.data("id");
        
        var _z = {
            url: this.config.baseUrl + "/page/"+page+"/activities",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }

        this.sendAjax(_z, 'afterLoadActivities');
    },
    afterLoadActivities : function(response) {
        var html = ``;
        if(response.code  == 200) {
            $.each(response.data,function(k,v) {
                var user = (v.causer) ? v.causer.name : "";
                html += `<tr>
                    <td>`+v.id+`</td>
                    <td>`+v.description+`</td>
                    <td>`+user+`</td>
                    <td>`+v.created_at+`</td>
                </tr>`;
            })
            $("#preview-activities-tbody").html(html);
            $("#loading-image").hide();
            $(".preview-activities-modal").modal("show");
        }
    },
    translateForOtherLanguage :function(ele) {
        let page     = ele.data("id");
        
        var _z = {
            url: this.config.baseUrl + "/page/"+page+"/translate-for-other-langauge",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }

        this.sendAjax(_z, 'afterTranslateForOtherLanguage');
    },
    afterTranslateForOtherLanguage : function(response) {
        if(response.code  == 200) {
            if(response.errorMessage != "") {
                $("#loading-image").hide();
                toastr["error"](response.errorMessage,"");
            }else{
                location.reload();
            }
        }
    },
    pushPageInLive : function(ele) {
        let page     = $(".push-website-store-id").val();
        
        var _z = {
            url: this.config.baseUrl + "/page/"+page+"/push-website-in-live",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }

        this.sendAjax(_z, 'afterPushPageInLive');
    },
    afterPushPageInLive : function (response) {
        if(response.code == 200) {
            $("#loading-image").hide();
            toastr["success"](response.message,"");
            location.reload();
        }else{
            toastr["error"](response.message,"");
        }
    },
    pullPageInLive : function(ele) {
        let page     = $(".pull-website-store-id").val();
        
        var _z = {
            url: this.config.baseUrl + "/page/"+page+"/pull-website-in-live",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }

        this.sendAjax(_z, 'afterPullPageInLive');
    },
    afterPullPageInLive : function (response) {
        if(response.code == 200) {
            $("#loading-image").hide();
            toastr["success"](response.message,"");
            location.reload();
        }else{
            toastr["error"](response.message,"");
        }
    }
}

$.extend(page, common);

$(document).on('change', '#title-page', function () {
    getGoogleKeyWord($('#title-page').val());
});

$(document).on('click', '#extra-keyword-search-btn', function () {
    $(document).find('.suggestList').hide();
    getGoogleKeyWord($('#extra-keyword-search').val());
});

$(document).on('click', '#keyword-search-btn', function () {
    $(document).find('.suggestList').hide();
    getGoogleKeyWord($('#title-page').val());
});

$(document).on('click', '.keyword-list', function () {

    var keywords = $(this).text();
    if (keywords ) {
        if ($(this).hasClass('badge-green')) {
            $('#meta_keywords').val($('#meta_keywords').val().replace("," + keywords, ""));
            $(this).removeClass('badge-green').find('i').remove()
            // $(this+' i').remove()
        } else {
            $('#meta_keywords').val($('#meta_keywords').val() + ',' + keywords);
            $(this).addClass('badge-green').append('<i class="fa fa-remove pl-2"></i>')
        }
    }
    $('input[name="meta_keywords"]').trigger('change');

});

// $(document).on('click', '.suggestList > li', function () {

//     var keywords = $(this).data('keyword');
//     if (keywords ) {
//         if ($(this).hasClass('badge-green')) {
//             $('#meta_keywords').val($('#meta_keywords').val().replace("," + keywords, ""));
//             $(this).removeClass('badge-green').find('i').remove()
//             // $(this+' i').remove()
//         } else {
//             $('#meta_keywords').val($('#meta_keywords').val() + ',' + keywords);
//             $(this).addClass('badge-green').append('<i class="fa fa-remove pl-2"></i>')
//         }
//     }

//     $('input[name="meta_keywords"]').trigger('change');
// });

$(document).on('change , keyup', 'input[name="meta_keywords"]', function () {
    $('#meta_keywords_count').text( 'Length: '+ $( this ).val().length );
});

$(document).on('change , keyup', 'textarea[name="meta_description"]', function () {
    $('#meta_desc_count').text( 'Length: '+ $(this).val().length );
});

function getGoogleKeyWord(title) {

    $(document).find('.suggestList-table').empty();
    var lan = $('.website-language-change').val();

    $.ajax({
       
        type: 'get',
        url: '/google-keyword-search-v6',
        data: { keyword: title, google_search: 'true' },

        beforeSend: function () {
            $("#loading-image").show();
        },
        success: function (response) {
            if (response.length > 0) {
                $(document).find('#extra-keyword-search-btn').removeClass('hide');
                $(document).find('#extra-keyword-search').removeClass('hide');
                var t = '';
                $.each(response, function (index, data) {
                    t += `<tr><td class="keyword-list">` + data.keyword + `</td>`;
                    t += `<td>` + data.avg_monthly_searches + `</td>`;
                    t += `<td>` + data.competition + `</td></tr>`;
                });
                $(document).find('.suggestList-table').html(t);
                $(document).find('.suggestList').show();
            } else {

                $(document).find('#extra-keyword-search-btn,#extra-keyword-search').hide();
            }

        },
        complete: function () {
            $("#loading-image").hide();
        },
    });
}