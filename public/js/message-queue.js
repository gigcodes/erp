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
        	msQueue.getResults($(this).attr("href"));
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

        msQueue.config.bodyView.on("click",".select-all-records",function(e) {
            msQueue.config.bodyView.find(".select-id-input").trigger("click");
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
            url: this.config.baseUrl + "/message-queue/records",
            method: "get",
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/message-queue/records",
            method: "get",
            data : $(".message-search-handler").serialize()
        }
        this.sendAjax(_z, "showResults");
    },
    showResults : function(response) {
    
    	var addProductTpl = $.templates("#template-result-block");
        var tplHtml       = addProductTpl.render(response);
    	msQueue.config.bodyView.find("#page-view-result").html(tplHtml);

    }
    ,
    deleteRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/message-queue/records/"+ele.data("id")+"/delete",
            method: "get",
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
            data : {"action" : action , "ids" : ids, "_token"  : $('meta[name="csrf-token"]').attr('content')}
        }
        this.sendAjax(_z, "loadFirst");
    }
}

$.extend(msQueue, common);