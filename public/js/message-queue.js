var msQueue = {
    init: function(settings) {
        
        msQueue.config = {
            bodyView: settings.bodyView
        };
        
        $.extend(msQueue.config, settings);
        
        this.getResults();

        //initialize pagination
        msQueue.config.bodyView.on("click",".page-link",function(e) {
        	e.preventDefault();
            
            var activePage = $(this).closest(".pagination").find(".active").text();
            var clickedPage = $(this).text();

            if(clickedPage == "‹" || clickedPage < activePage) {
                $('html, body').animate({scrollTop: ($(window).scrollTop() - 500) + "px"}, 200);
                msQueue.getResults($(this).attr("href"));
            }else{
                msQueue.getResults($(this).attr("href"));
            }

        });

        msQueue.config.bodyView.on("click",".btn-search-action",function(e) {
            e.preventDefault();
            msQueue.getResults();
        });
        
        // delete product templates
        msQueue.config.bodyView.on("click",".btn-delete-template",function(e) {
            if(!confirm("Are you sure you want to delete record?")) {
                return false;
            }else {
                msQueue.deleteRecord($(this));
            }
        });

        msQueue.config.bodyView.on("click",".btn-send-action",function(e) {
            e.preventDefault();
            if(!confirm("Are you sure you want to perform this operation?")) {
                return false;
            }else {
                msQueue.submitForm($(this));
            }
        });

        msQueue.config.bodyView.on("click",".btn-send-limit",function(e) {
            e.preventDefault();
            msQueue.submitLimit($(this));
        });

        

        msQueue.config.bodyView.on("click",".select-all-records",function(e) {
            msQueue.config.bodyView.find(".select-id-input").trigger("click");
        });

        $(".select2").select2({tags:true});

        $(window).scroll(function() {
            if($(window).scrollTop() > ($(document).height() - $(window).height())) {
                msQueue.config.bodyView.find("#page-view-result").find(".pagination").find(".active").next().find("a").click();
            }
        });
    },
    loadFirst: function() {
        var _z = {
            url: this.config.baseUrl + "/message-queue/records",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/message-queue/records",
            method: "get",
            data : $(".message-search-handler").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults",{append : true});
    },
    showResults : function(response,params) {
        $("#loading-image").hide();
        var addProductTpl = $.templates("#template-result-block");
        var tplHtml       = addProductTpl.render(response);
            if(params && typeof params.append != "undefined" && params.append == true) {
               // remove page first  
    	       var removePage = response.page;
                   if(removePage > 0) {
                      var pageList = msQueue.config.bodyView.find("#page-view-result").find(".page-template-"+removePage);
                      pageList.nextAll().remove();
                      pageList.remove();
                   }
                   if(removePage > 1) {
                     msQueue.config.bodyView.find("#page-view-result").find(".pagination").first().remove();
                   }
               msQueue.config.bodyView.find("#page-view-result").append(tplHtml);
            }else{
               msQueue.config.bodyView.find("#page-view-result").html(tplHtml);
            }
        $("#total-counter").html("(" +response.total+ ")");

    }
    ,
    deleteRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/message-queue/records/"+ele.data("id")+"/delete",
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
    submitForm: function(ele) {
        var action = $(".message-queue-handler").find("#action-to-run").val();
        var ids    = [];
            $.each($(".select-id-input:checked"),function(k,v){
               ids.push($(v).val()); 
            })
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/message-queue/records/action-handler",
            method: "post",
            data : {"action" : action , "ids" : ids, "_token"  : $('meta[name="csrf-token"]').attr('content')},
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "afterSubmitForm");
    },
    afterSubmitForm : function (response) {
        $("#loading-image").hide();
        if(response.code == 200){
            toastr['success'](response.message, 'success');
            msQueue.loadFirst();
        }else{
            toastr['error']('Oops.something went wrong', 'error');
        }
    },
    submitLimit: function(ele) {
        var limit = $(".message-queue-limit-handler").find(".message_sending_limit").val();
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/message-queue/setting/update-limit",
            method: "post",
            data : {"limit" : limit ,"_token"  : $('meta[name="csrf-token"]').attr('content')},
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "afterLimit");
    },
    afterLimit : function(response) {
        $("#loading-image").hide();
        if(response.code == 200){
            toastr['success']('Message limit updated successfully', 'success');
        }else{
            toastr['error']('Oops.something went wrong', 'error');
        }
    }
}

$.extend(msQueue, common);