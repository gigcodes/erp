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

        
        page.config.bodyView.on("click",".btn-create-group",function(e) {
            page.createTemplate($(this));
        });
        
        page.config.bodyView.on("click",".btn-edit-group",function(e) {
            page.editTemplate($(this));
        });

        $(".common-modal").on("click",".submit-group",function() {
            page.submitFormGroup($(this));
        });
 
        page.config.bodyView.on("click",".btn-delete-group",function(e) {
            if(!confirm("Are you sure you want to delete record?")) {
                return false;
            }else {
                page.deleteTemplate($(this));
            }
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
            url: this.config.baseUrl + "/website-store-views/records",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/records",
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
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/"+ele.data("id")+"/delete",
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

    }
    ,
    deleteTemplate : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/group/"+ele.data("id")+"/delete/"+ele.data("store_group_id"),
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'deleteTemplateResults');
    },
    deleteTemplateResults : function(response) {
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
    
    createTemplate : function(response) {
        var createWebTemplate = $.templates("#template-create-group");
        var tplHtml = createWebTemplate.render({data:{row_id:response.data('id'), type:'create'}});
        
        var common =  $(".common-modal");
            common.find(".modal-dialog").html(tplHtml); 
            common.modal("show");
    }, 

    editRecord : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/"+ele.data("id")+"/edit",
            method: "get",
        }
        this.sendAjax(_z, 'editResult');
    },

    editTemplate : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/group/"+ele.data("id")+"/edit/"+ele.data("store_group_id"),
            method: "get",
        }
        this.sendAjax(_z, 'editTemplateResult');
    },

    editResult : function(response) {
        var createWebTemplate = $.templates("#template-create-website");
        var tplHtml = createWebTemplate.render(response);
        var common =  $(".common-modal");
        common.find(".modal-dialog").html(tplHtml); 
        common.modal("show");
    },
    
    editTemplateResult : function(response) {
        var agents = response.responseData.agents;
        var agent_priorities = Object.keys(response.responseData.agent_priorities);
        var agent_priorities_value = Object.values(response.responseData.agent_priorities);
        var html = '';
        for(let i=0; i<agent_priorities.length; i++){
            let options = `<select name="agents[]" class="form-control select-2"> `;
            for(let j=0; j<agents.length; j++){
                options += `<option value="${agents[j].id}" ${agents[j].id == agent_priorities[i] ? 'selected' : ''}>${agents[j].id}</option>`;
            }
            options += '</select>';
            html += `
                <div class="abc">
                    <div class="form-group col-md-7 agents">
                        ${options}
                    </div> 
                    <div class="form-group col-md-4 priorities">
                        <select name="priorites[]" class="form-control select-2"> 
                            <option value="first" ${agent_priorities_value[i] == 'first' ? 'selected' : ''}>first</option> 
                            <option value="normal" ${agent_priorities_value[i] == 'normal' ? 'selected' : ''}>normal</option> 
                            <option value="last" ${agent_priorities_value[i] == 'last' ? 'selected' : ''}>last</option> 
                            <option value="supervisor" ${agent_priorities_value[i] == 'supervisor' ? 'selected' : ''}>supervisor</option> 
                        </select>
                    </div>
                    <div class="form-group col-md-1">
                        <button type="button" title="Remove" data-id="" class="btn btn-remove-priority">
                            <i class="fa fa-close" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            `;
        }
            
        var createWebTemplate = $.templates("#template-create-group");
        var tplHtml = createWebTemplate.render({data:response.responseData});
        
        var common =  $(".common-modal");
        common.find(".modal-dialog").html(tplHtml); 
        common.modal("show");
        $('#form-create-group .modal-body').append(html);
    },

    submitFormSite : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/save",
            method: "post",
            data : ele.closest("form").serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "saveSite");
    },

    submitFormGroup : function(ele) {
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/group/save",
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
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/website-store-views/"+ele.data("id")+"/push",
            method: "get",
        }
        this.sendAjax(_z, 'afterPush');
    },
    afterPush : function(response) {
        if(response.code  == 200) {
            toastr["success"](response.message,"");
        }else {
            $("#loading-image").hide();
            toastr["error"](response.error,"");
        }
    }
}

$.extend(page, common);