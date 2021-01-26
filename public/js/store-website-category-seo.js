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

        page.config.bodyView.on("click",".btn-push",function(e) {
            page.push($(this));
        });

        $(".common-modal").on("click",".submit-store-category-seo",function() {
            page.submitFormSite($(this));
        });

        $(document).on("click",".btn-translate-for-other-language",function(e) {
            e.preventDefault();
            page.translateForOtherLanguage($(this));
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
            url: this.config.baseUrl + "/category-seo/records",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/category-seo/records",
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
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/category-seo/"+ele.data("id")+"/delete",
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
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/category-seo/"+ele.data("id")+"/edit",
            method: "get",
        }
        this.sendAjax(_z, 'editResult');
    },

    editResult : function(response) {
        var createWebTemplate = $.templates("#template-create-website");
        var tplHtml = createWebTemplate.render(response);
        var common =  $(".common-modal");
            common.find(".modal-dialog").html(tplHtml); 
            common.modal("show");

            $('#google_translate_element').summernote();

        //new google.translate.TranslateElement({pageLanguage: 'en'}, 'google_translate_element');
    },

    submitFormSite : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/category-seo/save",
            method: "post",
            data : ele.closest("form").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "saveSite");
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
    translateForOtherLanguage :function(ele) {
        let store_website_category_seo = ele.data("id");
        
        var _z = {
            url: this.config.baseUrl + "/category-seo/"+store_website_category_seo+"/translate-for-other-langauge",
            method: "get"
        }

        this.sendAjax(_z, 'afterTranslateForOtherLanguage');
    },
    afterTranslateForOtherLanguage : function(response) {
        if(response.code  == 200) {
            if(response.errorMessage != "") {
                toastr["error"](response.errorMessage,"");
            }else{
                location.reload();
            }
        }
    },
    push : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/category-seo/"+ele.data("id")+"/push",
            method: "get",
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
}

$.extend(page, common);