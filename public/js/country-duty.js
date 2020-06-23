var page = {
    init: function(settings) {
        
        page.config = {
            bodyView: settings.bodyView
        };

        $.extend(page.config, settings);
        
        page.config.mainUrl = page.config.baseUrl + "/country-duty";
        
    }
}

$.extend(page, common);