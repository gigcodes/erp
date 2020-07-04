var page = {
    init: function(settings) {
        
        page.config = {
            bodyView: settings.bodyView
        };

        $.extend(page.config, settings);

        page.config.mainUrl = page.config.baseUrl + "/country-duty/list";

        page.getRecords();

        page.config.bodyView.on("click",".btn-search-action",function(e) {
            e.preventDefault();
            page.getRecords($(this));
        });
        
    },
    getRecords : function(ele) {
        var form = $(".message-search-handler");
        var _z = {
            url: (typeof href != "undefined") ? href : this.config.mainUrl + "/records",
            method: "get",
            data:form.serialize(),
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, 'displayResult');
    },
    displayResult :  function(response) {
        if(response.code == 200) {
            $("#loading-image").hide();
            var addProductTpl = $.templates("#template-result-block");
            var tplHtml       = addProductTpl.render(response);
            $(".count-text").html("("+response.total+")");
            page.config.bodyView.find("#page-view-result").html(tplHtml);
        }else{
            $("#loading-image").hide();
            toastr["error"](response.message);
        }
    }
}

$.extend(page, common);