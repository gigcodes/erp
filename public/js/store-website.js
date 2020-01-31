var page = {
    init: function(settings) {
        
        page.config = {
            bodyView: settings.bodyView
        };
        
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

        page.config.bodyView.on("click",".btn-attach-category",function(e) {
            page.attachCategory($(this).data("id"));
        });

        $(".common-modal").on("click",".submit-store-site",function() {
            page.submitFormSite($(this));
        });

        $(".common-modal").on("click",".add-attached-category",function(e) {
            e.preventDefault();
            page.submitCategory($(this));
        });

        $(".common-modal").on("click",".btn-delete-store-website-category",function(e) {
            e.preventDefault();
            page.deleteCategory($(this));
        });

            

    },
    validationRule : function() {
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
            url: this.config.baseUrl + "/store-website/records",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/records",
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
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/"+ele.data("id")+"/delete",
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
            toastr['success']('Message deleted successfully', 'success');
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
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/"+ele.data("id")+"/edit",
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
    },

    submitFormSite : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/save",
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
    attachCategory : function(id) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/"+id+"/attached-category",
            method: "get",
        }
        this.sendAjax(_z, 'showAttachedCategory');
    },
    showAttachedCategory : function (response) {
        $("#loading-image").hide();
        if (response.code == 200) {
            var createWebTemplate = $.templates("#template-attached-category");
            var tplHtml = createWebTemplate.render(response);
            var common =  $(".common-modal");
                common.find(".modal-dialog").html(tplHtml);
                page.assignSelect2(); 
                common.modal("show");      
        }
    },
    submitCategory : function(ele) {
        var website_id = ele.closest("form").find('input[name="store_website_id"]').val();
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/store-website/"+website_id+"/attached-category",
            method: "post",
            data : ele.closest("form").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'afterSubmitCategory');
    },
    afterSubmitCategory : function(response) {
        if(response.code  == 200) {
            page.attachCategory(response.data.store_website_id);
        }else {
            $("#loading-image").hide();
            toastr["error"](response.error,"");
        }
    },
    deleteCategory : function(ele) {
        
        var storeWebsiteId = ele.data("store-website-id");
        var id = ele.data("id");

        var _z = {
            url: this.config.baseUrl + "/store-website/"+storeWebsiteId+"/attached-category/"+id+"/delete",
            method: "get",
        }

        this.sendAjax(_z, 'deleteCategoryResponse', ele);
    },
    deleteCategoryResponse: function(response,ele) {
        if(response.code == 200) {
            ele.closest("tr").remove();
        }
    } 
}

$.extend(page, common);