function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('#custom').val(start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD'));
}

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

        msQueue.config.bodyView.on("click",".btn-edit-template",function(e) {
            e.preventDefault();
            var ele = $(this);
            msQueue.editRecord(ele);
        });

        msQueue.config.bodyView.on("click",".btn-search-action",function(e) {
            e.preventDefault();
            msQueue.getResults();
        });

        $(".select2").select2({tags:true});

        $(window).scroll(function() {
            if($(window).scrollTop() > ($(document).height() - $(window).height())) {
                msQueue.config.bodyView.find("#page-view-result").find(".pagination").find(".active").next().find("a").click();
            }
        });

        var start = moment().subtract(29, 'days');
        var end = moment();

        $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                 'Today': [moment(), moment()],
                 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                 'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                 'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                 'This Month': [moment().startOf('month'), moment().endOf('month')],
                 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
             }
        }, cb);

        cb(start, end);

    },
    loadFirst: function() {
        var _z = {
            url: this.config.baseUrl + "/return-exchange/records",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showResults");
    },
    getResults: function(href) {
    	var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/return-exchange/records",
            method: "get",
            data : $(".return-exchange-handler").serialize(),
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

    },
    editRecord : function(ele) {
        var id = ele.data("id");
         var _z = {
            url: (typeof href != "undefined") ? href : this.config.baseUrl + "/return-exchange/"+id+"/detail",
            method: "get",
            beforeSend : function() {
                $("#loading-image").show();
            }
        }
        this.sendAjax(_z, "showEditRecords",{append : true});   
    },
    showEditRecords : function(response) {
        if(response.code == 200) {
           $("#loading-image").hide();
            var addProductTpl = $.templates("#template-edit-block");
            var tplHtml       = addProductTpl.render(response); 
        }
    }
}

$.extend(msQueue, common);
